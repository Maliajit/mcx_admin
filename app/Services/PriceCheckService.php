<?php

namespace App\Services;

use App\Models\Order;
use App\Services\LiveRates\LiveRatesService;
use Illuminate\Support\Facades\Log;

class PriceCheckService
{
    public function __construct(
        private readonly LiveRatesService $liveRatesService,
    ) {}

    public function checkWaitingOrders(): void
    {
        $waitingOrders = Order::where('status', 'waiting')->get();

        if ($waitingOrders->isEmpty()) {
            return;
        }

        $liveRatesResult = $this->liveRatesService->getLiveRates();

        if (!$liveRatesResult['success']) {
            Log::warning('Price check failed: unable to fetch live rates');
            return;
        }

        $liveRates = collect($liveRatesResult['data']['items']);

        foreach ($waitingOrders as $order) {
            $matchingRate = $liveRates->firstWhere('name', $order->asset);

            if (!$matchingRate) {
                continue;
            }

            $currentPrice = (float) $matchingRate['bid'];

            if ($currentPrice <= (float) $order->target_price) {
                $order->update(['status' => 'pending']);
                Log::info("Order {$order->id} moved to pending: price {$currentPrice} <= target {$order->target_price}");
            }
        }
    }
}
