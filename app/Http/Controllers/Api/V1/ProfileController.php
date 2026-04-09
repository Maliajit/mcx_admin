<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LocalAppUserResolver;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private readonly LocalAppUserResolver $userResolver,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = $this->userResolver->resolve($request);

        $latestKycRequest = $user->kycRequests()->latest()->first();

        return ApiResponse::success([
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_verified' => $user->is_verified,
                'can_trade' => $user->can_trade,
                'kyc_request' => $latestKycRequest ? [
                    'id' => $latestKycRequest->id,
                    'name' => $latestKycRequest->name,
                    'pan' => $latestKycRequest->pan,
                    'aadhaar' => $latestKycRequest->aadhaar,
                    'status' => $latestKycRequest->status,
                    'created_at' => $latestKycRequest->created_at->toIso8601String(),
                    'approved_at' => optional($latestKycRequest->approved_at)->toIso8601String(),
                ] : null,
            ],
            'auth' => [
                'type' => 'local',
                'guard' => 'none',
            ],
        ]);
    }
}
