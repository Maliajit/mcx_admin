<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerifiedUser;
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

    /**
     * Send OTP to the mobile number.
     * 
     * POST /api/v1/send-otp
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'mobile' => ['required', 'string', 'regex:/^\d{10}$/'],
        ]);

        $mobile = $payload['mobile'];

        // Generate and send OTP (Using OtpService which uses Cache)
        $otp = $this->otpService->generateOtp($mobile);

        // Save OTP to auth_users table for redundant verification / audit
        User::updateOrCreate(
            ['mobile' => $mobile],
            ['otp' => $otp, 'otp_verified' => false]
        );

        return ApiResponse::success([
            'message' => 'OTP sent successfully.',
            'mobile' => $mobile,
            'otp_for_testing' => $otp, // Remove in production
        ]);
    }

    /**
     * Verify OTP and issue Sanctum token.
     * 
     * POST /api/v1/verify-otp
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'mobile' => ['required', 'string', 'regex:/^\d{10}$/'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $mobile = $payload['mobile'];
        $otp = $payload['otp'];

        // 1. Verify via Cache (OtpService)
        if (!$this->otpService->verifyOtp($mobile, $otp)) {
            return ApiResponse::error('Invalid or expired OTP.', 401);
        }

        // 2. Find or Create User in auth_users
        $user = User::where('mobile', $mobile)->first();

        if (!$user) {
             // Should have been created by sendOtp, but fallback for safety
             $user = User::create([
                'mobile' => $mobile,
                'password' => Hash::make('otp-user-' . $mobile),
             ]);
        }

        if ($user->is_blocked) {
            return ApiResponse::error('Your account has been blocked.', 403);
        }

        // 3. Mark as verified and clear OTP
        $user->update([
            'otp' => null,
            'otp_verified' => true,
        ]);

        // 4. Generate Sanctum token
        $token = $user->createToken('mobile-auth')->plainTextToken;

        // 5. Build response profile (including VerifiedUser status)
        $profile = $user->verifiedUser;

        return ApiResponse::success([
            'message' => 'Login successful.',
            'user' => [
                'id' => $user->id,
                'mobile' => $user->mobile,
                'is_blocked' => $user->is_blocked,
                'otp_verified' => $user->otp_verified,
                'kyc_status' => $profile?->kyc_status ?? 'not_submitted',
                'is_verified' => $profile?->kyc_status === 'approved',
                'can_trade' => (bool) $profile?->is_trading_enabled,
            ],
            'session_token' => $token,
        ]);
    }

    /**
     * Get current user profile.
     * 
     * GET /api/v1/profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $kyc = $user->verifiedUser;

        return ApiResponse::success([
            'user' => [
                'id' => $user->id,
                'mobile' => $user->mobile,
                'otp_verified' => $user->otp_verified,
            ],
            'kyc' => $kyc ? [
                'full_name' => $kyc->full_name,
                'email' => $kyc->email,
                'status' => $kyc->kyc_status,
                'gold_limit' => (float)$kyc->gold_limit,
                'silver_limit' => (float)$kyc->silver_limit,
                'trading_enabled' => (bool)$kyc->is_trading_enabled,
            ] : null
        ]);
    }

    /**
     * Logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::success(['message' => 'Logged out successfully.']);
    }
}
