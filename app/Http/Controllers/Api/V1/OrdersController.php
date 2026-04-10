<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\LocalAppUserResolver;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrdersController extends Controller
{
    public function __construct(
        private readonly LocalAppUserResolver $userResolver,
    ) {
    }

    public function index(): JsonResponse
    {
        return ApiResponse::success([
            'items' => Order::query()
                ->latest('placed_at')
                ->latest('id')
                ->get()
                ->map(fn (Order $order): array => $this->mapOrder($order))
                ->all(),
            'message' => 'Orders loaded successfully.',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $this->userResolver->resolve($request);

        if (!$user->is_verified) {
            return ApiResponse::error(
                'KYC verification is required before placing an order.',
                403,
                [
                    'code' => 'kyc_required',
                    'profile' => [
                        'is_verified' => false,
                    ],
                ],
            );
        }

        if (!$user->can_trade) {
            return ApiResponse::error(
                'Trading is not enabled for your account.',
                403,
                [
                    'code' => 'trading_disabled',
                    'profile' => [
                        'can_trade' => false,
                    ],
                ],
            );
        }

        $payload = $request->validate([
            'asset_name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(['market', 'limit'])],
            'price' => ['required', 'numeric', 'min:0'],
            'target_price' => ['nullable', 'numeric', 'min:0', 'required_if:type,limit'],
        ]);

        $status = $payload['type'] === 'market' ? 'pending' : 'waiting';

        $order = Order::query()->create([
            'user_id' => $user->id,
            'asset' => $payload['asset_name'],
            'type' => $payload['type'],
            'price' => $payload['price'],
            'target_price' => $payload['target_price'] ?? null,
            'status' => $status,
            'placed_at' => now(),
        ]);

        return ApiResponse::success([
            'order' => $this->mapOrder($order),
            'message' => 'Order placed successfully.',
        ], 201);
    }

    private function mapOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'asset_name' => $order->asset,
            'type' => $order->type,
            'price' => number_format((float) $order->price, 2, '.', ''),
            'target_price' => $order->target_price ? number_format((float) $order->target_price, 2, '.', '') : null,
            'status' => $order->status,
            'placed_at' => optional($order->placed_at)->toIso8601String(),
            'approved_at' => optional($order->approved_at)->toIso8601String(),
        ];
    }
}
