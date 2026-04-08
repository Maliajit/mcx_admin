<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class TradeHistoryController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return ApiResponse::success([
            'items' => Order::query()
                ->where('status', 'completed')
                ->latest('placed_at')
                ->latest('id')
                ->get()
                ->map(fn (Order $order): array => [
                    'id' => $order->id,
                    'asset' => $order->asset,
                    'quantity' => number_format((float) $order->quantity, 2, '.', ''),
                    'price' => number_format((float) $order->price, 2, '.', ''),
                    'total' => number_format((float) $order->total, 2, '.', ''),
                    'status' => $order->status,
                    'executed_at' => optional($order->placed_at)->toIso8601String(),
                ])
                ->all(),
            'message' => 'Trade history loaded successfully.',
        ]);
    }
}
