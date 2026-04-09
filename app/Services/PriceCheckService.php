<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PriceCheckService
{
    public function __construct(
        private readonly PriceService $priceService,
    ) {}

    public function checkWaitingOrders(): void
    {
        $waitingOrders = Order::where('status', 'waiting')->get();

        if ($waitingOrders->isEmpty()) {
            return;
        }

        $config = $this->priceService->getConfig();
        $products = collect($config['products']);
        $coins = collect($config['coins']);

        /** @var \App\Models\Order $order */
        foreach ($waitingOrders as $order) {
            $currentPrice = null;

            if ($order->product_type === 'row') {
                $product = $products->firstWhere('id', $order->product_id);
                if ($product) {
                    $currentPrice = $product['final_price'];
                }
            } elseif ($order->product_type === 'coin') {
                $coin = $coins->firstWhere('id', $order->product_id);
                if ($coin) {
                    $currentPrice = $coin['final_price'];
                }
            }

            if ($currentPrice === null) {
                // If product doesn't exist or doesn't match new schema, skip
                continue;
            }

            if ($currentPrice <= (float) $order->target_price) {
                $order->update(['status' => 'pending']);
                Log::info("Order {$order->id} moved to pending: price {$currentPrice} <= target {$order->target_price}");
            }
        }
    }
}
