<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PriceService;
use App\Services\UserValidationService;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrdersController extends Controller
{
    public function __construct(
        private readonly PriceService $priceService,
        private readonly UserValidationService $validationService,
    ) {
    }

    /**
     * List user orders.
     */
    public function index(Request $request): JsonResponse
    {
        return ApiResponse::success([
            'items' => $request->user()->orders()
                ->latest('placed_at')
                ->latest('id')
                ->get()
                ->map(fn (Order $order): array => $this->mapOrder($order))
                ->all(),
            'message' => 'Orders loaded successfully.',
        ]);
    }

    /**
     * Place a new order.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. Basic Payload Validation
        $payload = $request->validate([
            'product_type' => ['required', 'string', Rule::in(['row', 'coin'])],
            'product_id' => ['required', 'integer'],
            'type' => ['required', 'string', Rule::in(['market', 'limit'])],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
            'target_price' => ['nullable', 'numeric', 'min:0', 'required_if:type,limit'],
        ]);

        // 2. Identify Product and Metal Type
        $config = $this->priceService->getConfig();
        $targetProduct = null;
        $metalType = 'gold';

        if ($payload['product_type'] === 'row') {
            $targetProduct = collect($config['products'])->firstWhere('id', $payload['product_id']);
        } else {
            $targetProduct = collect($config['coins'])->firstWhere('id', $payload['product_id']);
        }

        if (!$targetProduct) {
            return ApiResponse::error('Invalid product selected.', 422);
        }

        $metalType = $targetProduct['type']; // 'gold' or 'silver'

        // 3. Service Layer Validation (OTP, KYC, Trading Status, Limits)
        try {
            // Calculate effective weight if it's a coin for limit calculation
            // Note: Our validation service checks against limits. 
            // We should ensure we pass the 'weight' to the service if limits are weight-based.
            $effectiveWeight = (float) $payload['quantity'];
            if ($payload['product_type'] === 'coin') {
                $effectiveWeight = $payload['quantity'] * $targetProduct['weight_in_grams'];
            }

            $this->validationService->validateForOrder($user, $metalType, $effectiveWeight);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 403);
        }

        // 4. Pricing & Tax Calculation
        $executionPrice = $targetProduct['final_price'];
        $taxes = $this->priceService->calculateOrderTaxes($executionPrice, $payload['quantity']);

        // 5. Create Order
        $status = $payload['type'] === 'market' ? 'pending' : 'waiting';

        $order = Order::query()->create([
            'user_id' => $user->id,
            'asset' => $targetProduct['name'],
            'product_type' => $payload['product_type'],
            'product_id' => $payload['product_id'],
            'quantity' => $payload['quantity'],
            'type' => $payload['type'],
            'price' => $executionPrice,
            'tax_amount' => $taxes['total_tax'],
            'total' => $taxes['grand_total'],
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
            'asset_name' => $order->asset,
            'product_type' => $order->product_type,
            'product_id' => $order->product_id,
            'quantity' => (float)$order->quantity,
            'type' => $order->type,
            'price' => number_format((float) $order->price, 2, '.', ''),
            'tax_amount' => number_format((float) $order->tax_amount, 2, '.', ''),
            'total' => number_format((float) $order->total, 2, '.', ''),
            'target_price' => $order->target_price ? number_format((float) $order->target_price, 2, '.', '') : null,
            'status' => $order->status,
            'placed_at' => optional($order->placed_at)->toIso8601String(),
            'approved_at' => optional($order->approved_at)->toIso8601String(),
        ];
    }
}
