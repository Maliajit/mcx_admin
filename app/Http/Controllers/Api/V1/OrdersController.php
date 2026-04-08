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
        $user = $this->userResolver->resolve();

        if ($user->kyc_status !== 'verified') {
            return ApiResponse::error(
                'KYC verification is required before placing an order.',
                403,
                [
                    'code' => 'kyc_required',
                    'profile' => [
                        'is_verified' => false,
                        'kyc_status' => $user->kyc_status,
                    ],
                ],
            );
        }

        $payload = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:255'],
            'asset' => ['required', 'string', 'max:255'],
            'side' => ['required', 'string', Rule::in(['buy', 'sell'])],
            'order_type' => ['required', 'string', Rule::in(['market', 'pending'])],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'price' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $order = Order::query()->create([
            'user_id' => $user->id,
            'customer_name' => $payload['customer_name'] ?? $user->name,
            'customer_phone' => $payload['customer_phone'] ?? $user->phone,
            ...$payload,
            'status' => 'pending',
            'placed_at' => now(),
        ]);

        return ApiResponse::success([
            'order' => $this->mapOrder($order),
            'message' => 'Order placed successfully.',
        ], 201);
    }

    /**
     * @return array<string, mixed>
     */
    private function mapOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'asset' => $order->asset,
            'side' => $order->side,
            'order_type' => $order->order_type,
            'quantity' => number_format((float) $order->quantity, 2, '.', ''),
            'price' => number_format((float) $order->price, 2, '.', ''),
            'total' => number_format((float) $order->total, 2, '.', ''),
            'status' => $order->status,
            'notes' => $order->notes,
            'placed_at' => optional($order->placed_at)->toIso8601String(),
        ];
    }
}
