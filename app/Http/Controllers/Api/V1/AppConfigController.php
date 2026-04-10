<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PriceService;
use App\Services\LocalAppUserResolver;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppConfigController extends Controller
{
    public function __construct(
        private readonly PriceService $priceService,
        private readonly LocalAppUserResolver $userResolver,
    ) {
    }

    /**
     * Get dynamic config and user profile status.
     * 
     * GET /api/v1/config
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->userResolver->resolve($request);
        $profile = null;

        if ($user) {
            $kyc = $user->verifiedUser;
            $profile = [
                'id' => $user->id,
                'mobile' => $user->mobile,
                'otp_verified' => (bool) $user->otp_verified,
                'is_verified' => $kyc?->kyc_status === 'approved',
                'kyc_status' => $kyc?->kyc_status ?? 'not_submitted',
                'can_trade' => (bool) $kyc?->is_trading_enabled,
                'limits' => [
                    'gold' => (float) ($kyc?->gold_limit ?? 0),
                    'silver' => (float) ($kyc?->silver_limit ?? 0),
                ]
            ];
        }

        return ApiResponse::success([
            'config' => $this->priceService->getConfig(),
            'profile' => $profile,
            'message' => 'Live config and profile loaded successfully.',
        ]);
    }
}
