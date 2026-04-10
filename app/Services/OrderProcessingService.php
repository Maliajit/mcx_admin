<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductRow;
use App\Models\Coin;
use Illuminate\Support\Facades\Log;

class OrderProcessingService
{
    public function __construct(
        private readonly PriceService $priceService,
        private readonly OrderLimitService $orderLimitService
    ) {}

    /**
     * Scan and process pending limit orders based on current base prices.
     */
    public function processPendingLimits(): int
    {
        $pendingOrders = Order::where('status', 'pending')
            ->where('type', 'limit')
            ->get();

        if ($pendingOrders->isEmpty()) {
            return 0;
        }

        $basePrices = $this->priceService->getBasePrices();
        $processedCount = 0;

        foreach ($pendingOrders as $order) {
            $metalType = str_contains(strtolower($order->asset), 'silver') ? 'silver' : 'gold';
            $currentBase = (float) ($basePrices[$metalType] ?? 0);
            $targetBase = (float) $order->target_price;

            // Log for debugging
            // Log::debug("Checking order #{$order->id}: Target {$targetBase} vs Current Base {$currentBase}");

            // Simplified Hit Logic: If current base price matches or crosses target
            // Assuming Buy Side for now as per current requirements
            if ($currentBase >= $targetBase) {
                if ($this->executeLimitHit($order, $targetBase)) {
                    $processedCount++;
                }
            }
        }

        return $processedCount;
    }

    /**
     * Finalize the order with automated margins and taxes.
     */
    private function executeLimitHit(Order $order, float $hitBasePrice): bool
    {
        try {
            // 1. Fetch current product margins/adjustments
            $margin = 0;
            $adjustment = 0;

            if ($order->product_type === 'row') {
                $product = ProductRow::find($order->product_id);
                if ($product) {
                    $margin = (float) $product->margin;
                    $adjustment = (float) $product->adjustment;
                }
            } else {
                $product = Coin::find($order->product_id);
                if ($product) {
                    $margin = (float) $product->margin;
                }
            }

            // 2. Calculate Intermediate Price (Base + Margin + Adjustment)
            $intermediatePrice = $hitBasePrice + $margin + $adjustment;

            // 3. Calculate Taxes
            $taxDetails = $this->priceService->calculateOrderTaxes($intermediatePrice, (float)$order->quantity);

            // 4. Update Order
            $order->update([
                'price' => $intermediatePrice,
                'tax_amount' => $taxDetails['total_tax'],
                'total' => $taxDetails['grand_total'],
                'status' => 'confirmed',
                'approved_at' => now(),
            ]);

            $this->orderLimitService->consumeRemainingLimit($order->load('user.verifiedUser'));

            Log::info("Limit Order #{$order->id} Auto-Confirmed at Base: {$hitBasePrice}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to auto-confirm Limit Order #{$order->id}: " . $e->getMessage());
            return false;
        }
    }
}
