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

        return ApiResponse::success([
            'profile' => [
                'name' => $user->verifiedUser?->full_name ?? 'User',
                'email' => $user->verifiedUser?->email ?? '',
                'phone' => $user->mobile,
                'is_verified' => ($user->verifiedUser?->kyc_status ?? '') === 'approved',
                'can_trade' => (bool) ($user->verifiedUser?->is_trading_enabled ?? false),
                'kyc_status' => $user->verifiedUser?->kyc_status ?? 'not_submitted',
                'limits' => [
                    'gold' => (float) ($user->verifiedUser?->gold_limit ?? 0),
                    'silver' => (float) ($user->verifiedUser?->silver_limit ?? 0),
                ],
            ],
            'auth' => [
                'type' => 'local',
                'guard' => 'none',
            ],
        ]);
    }
}
