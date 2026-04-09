<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Get authenticated user profile and KYC status.
     * 
     * GET /api/v1/profile
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $kyc = $user->verifiedUser;

        return ApiResponse::success([
            'profile' => [
                'id' => $user->id,
                'mobile' => $user->mobile,
                'is_verified' => $kyc?->kyc_status === 'approved',
                'kyc_status' => $kyc?->kyc_status ?? 'not_submitted',
                'can_trade' => (bool) $kyc?->is_trading_enabled,
                'limits' => [
                    'gold' => (float) ($kyc?->gold_limit ?? 0),
                    'silver' => (float) ($kyc?->silver_limit ?? 0),
                ],
                'kyc_details' => $kyc ? [
                    'full_name' => $kyc->full_name,
                    'pan' => $kyc->pan_number,
                    'aadhaar' => $kyc->aadhaar_number,
                    'submitted_at' => $kyc->created_at ? $kyc->created_at->toIso8601String() : null,
                ] : null,
            ],
            'auth' => [
                'type' => 'otp',
                'verified' => (bool) $user->otp_verified,
            ],
        ]);
    }
}
