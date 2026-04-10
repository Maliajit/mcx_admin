<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\LocalAppUserResolver;
use App\Services\OrderLimitService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrdersController extends Controller
{
    public function __construct(
        private readonly LocalAppUserResolver $userResolver,
        private readonly OrderLimitService $orderLimitService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $this->userResolver->resolve($request);

        return ApiResponse::success([
            'items' => Order::query()
                ->where('user_id', $user->id)
                ->latest('placed_at')
                ->latest('id')
                ->get()
                ->map(fn(Order $order): array => $this->mapOrder($order))
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
            'product_type' => ['required', 'string', Rule::in(['row', 'coin'])],
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'], // This is intermediate price from UI
            'target_price' => ['nullable', 'numeric', 'min:0', 'required_if:type,limit'],
        ]);

        try {
            $this->orderLimitService->assertWithinRemainingLimit(
                $user,
                $payload['asset_name'],
                $payload['product_type'],
                (int) $payload['product_id'],
                (float) $payload['quantity']
            );
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 422);
        }

        $priceService = app(\App\Services\PriceService::class);
        $taxCalculations = $priceService->calculateOrderTaxes(
            (float) $payload['price'],
            (float) $payload['quantity']
        );

        $isMarketOrder = $payload['type'] === 'market';
        $status = $isMarketOrder ? 'confirmed' : 'pending';

        $order = Order::query()->create([
            'user_id' => $user->id,
            'asset' => $payload['asset_name'],
            'product_type' => $payload['product_type'],
            'product_id' => $payload['product_id'],
            'type' => $payload['type'],
            'quantity' => $payload['quantity'],
            'price' => $payload['price'], // Base Intermediate Price
            'tax_amount' => $taxCalculations['gst_amount'],
            'total' => $taxCalculations['grand_total'], // Includes GST + TDS (user pays both as per current helper)
            'target_price' => $payload['target_price'] ?? null,
            'status' => $status,
            'placed_at' => now(),
            'approved_at' => $isMarketOrder ? now() : null,
        ]);

        if ($isMarketOrder) {
            $this->orderLimitService->consumeRemainingLimit($order->load('user.verifiedUser'));
        }

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
            'asset' => $order->asset,
            'asset_name' => $order->asset,
            'product_type' => $order->product_type,
            'type' => $order->type,
            'quantity' => number_format((float) $order->quantity, 3, '.', ''),
            'price' => number_format((float) $order->price, 2, '.', ''),
            'total' => number_format((float) $order->total, 2, '.', ''),
            'target_price' => $order->target_price ? number_format((float) $order->target_price, 2, '.', '') : null,
            'status' => $order->status,
            'placed_at' => optional($order->placed_at)->toIso8601String(),
            'approved_at' => optional($order->approved_at)->toIso8601String(),
        ];
    }
}
