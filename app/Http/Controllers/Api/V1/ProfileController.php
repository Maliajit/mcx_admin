<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LocalAppUserResolver;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(
        private readonly LocalAppUserResolver $userResolver,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $user = $this->userResolver->resolve();

        return ApiResponse::success([
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gst_number' => $user->gst_number,
                'pan_number' => $user->pan_number,
                'aadhaar_number' => $user->aadhaar_number,
                'kyc_status' => $user->kyc_status,
                'is_verified' => $user->kyc_status === 'verified',
                'kyc_verified_at' => optional($user->kyc_verified_at)->toIso8601String(),
            ],
            'auth' => [
                'type' => 'local',
                'guard' => 'none',
            ],
        ]);
    }
}
