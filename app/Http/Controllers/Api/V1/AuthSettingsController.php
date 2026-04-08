<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class AuthSettingsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return ApiResponse::success([
            'otp_enabled' => false,
            'message' => 'Local app mode is enabled. Enter any details to continue.',
            'support' => config('api.mobile_app.support'),
            'kyc' => config('api.mobile_app.kyc'),
        ]);
    }
}
