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
            'phone' => ['required', 'string', 'regex:/^\+91\s\d{10}$/'],
        ]);

        $phone = $payload['phone'];

        // Generate and send OTP
        $otp = $this->otpService->generateOtp($phone);

        return ApiResponse::success([
            'message' => 'OTP sent successfully.',
            'phone' => $phone,
            // In production, remove this - OTP should only be logged
            'otp_for_testing' => $otp,
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+91\s\d{10}$/'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $phone = $payload['phone'];
        $otp = $payload['otp'];

        // Verify OTP
        if (!$this->otpService->verifyOtp($phone, $otp)) {
            return ApiResponse::error('Invalid or expired OTP.', 401);
        }

        // Get or create user - login without KYC check
        // Generate unique email from phone number
        $phoneDigits = preg_replace('/[^0-9]/', '', $phone);
        $generatedEmail = "phone_{$phoneDigits}@mcxapp.local";

        $user = User::firstOrCreate(
            ['phone' => $phone],
            [
                'name' => 'User', // Will be updated from KYC if available
                'email' => $generatedEmail,
                'password' => Hash::make('phone-auth-user'),
                'is_verified' => false, // Will be set when KYC is approved
                'can_trade' => false, // Admin needs to enable trading
            ]
        );

        // Check KYC status separately (for frontend to know)
        $kycRequest = KycRequest::whereHas('user', function ($query) use ($phone) {
            $query->where('phone', $phone);
        })->where('status', 'approved')->first();

        return ApiResponse::success([
            'message' => 'Login successful.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'is_verified' => $user->is_verified,
                'can_trade' => $user->can_trade,
                'kyc_approved' => (bool) $kycRequest, // For reference
            ],
            'session_token' => 'mock-session-' . $user->id, // For demo purposes
        ]);
    }
}
