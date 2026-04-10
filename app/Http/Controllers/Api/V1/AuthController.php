<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KycRequest;
use App\Models\User;
use App\Services\OtpService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private readonly OtpService $otpService,
    ) {
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'phone' => ['required', 'string', 'min:10'],
        ]);

        // Clean phone number: remove non-digits and handle prefix
        $phone = preg_replace('/\D/', '', $payload['phone']);
        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            $phone = substr($phone, 2);
        }

        if (strlen($phone) !== 10) {
            return ApiResponse::error('Please provide a valid 10-digit mobile number.', 422);
        }

        // Generate and send OTP
        $otp = $this->otpService->generateOtp($phone);

        return ApiResponse::success([
            'message' => 'OTP sent successfully.',
            'phone' => $phone,
            'otp_for_testing' => $otp,
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'phone' => ['required', 'string', 'min:10'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        // Clean phone number: remove non-digits and handle prefix
        $phone = preg_replace('/\D/', '', $payload['phone']);
        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            $phone = substr($phone, 2);
        }

        if (strlen($phone) !== 10) {
            return ApiResponse::error('Please provide a valid 10-digit mobile number.', 422);
        }

        $otp = $payload['otp'];

        // Verify OTP
        if (!$this->otpService->verifyOtp($phone, $otp)) {
            return ApiResponse::error('Invalid or expired OTP.', 401);
        }

        // Get or create user - login using mobile column
        $user = User::firstOrCreate(
            ['mobile' => $phone],
            [
                'password' => Hash::make('phone-auth-user'),
                'otp_verified' => true,
                'is_blocked' => false,
            ]
        );

        // Ensure otp_verified is true if user existed
        if (!$user->otp_verified) {
            $user->update(['otp_verified' => true]);
        }

        return ApiResponse::success([
            'message' => 'Login successful.',
            'user' => [
                'id' => $user->id,
                'mobile' => $user->mobile,
                'otp_verified' => (bool) $user->otp_verified,
                'is_blocked' => (bool) $user->is_blocked,
                'kyc_status' => $user->verifiedUser?->kyc_status ?? 'not_submitted',
                'is_verified' => ($user->verifiedUser?->kyc_status ?? '') === 'approved',
                'can_trade' => (bool) ($user->verifiedUser?->is_trading_enabled ?? false),
                'limits' => [
                    'gold' => (float) ($user->verifiedUser?->gold_limit ?? 0),
                    'silver' => (float) ($user->verifiedUser?->silver_limit ?? 0),
                ],
            ],
            'session_token' => 'mock-session-' . $user->id,
        ]);
    }
}
