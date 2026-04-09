<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\VerifiedUser;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    /**
     * Submit KYC documentation.
     * 
     * POST /api/v1/profile/kyc
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. Identify Existing Status
        $verifiedUser = $user->verifiedUser;
        if ($verifiedUser && in_array($verifiedUser->kyc_status, ['pending', 'approved'])) {
            return ApiResponse::error('KYC request already exists or is already approved.', 400);
        }

        // 2. Validate Inputs
        $payload = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'pan_number' => ['required', 'string', 'max:20'],
            'aadhaar_number' => ['required', 'string', 'max:20'],
            'pan_image' => ['required', 'image', 'max:5120'], // 5MB limit
            'aadhaar_image' => ['required', 'image', 'max:5120'],
            'selfie_image' => ['nullable', 'image', 'max:5120'],
        ]);

        // 3. Handle File Uploads
        $panPath = $request->file('pan_image')->store('kyc/pan', 'public');
        $aadhaarPath = $request->file('aadhaar_image')->store('kyc/aadhaar', 'public');
        $selfiePath = $request->hasFile('selfie_image') 
            ? $request->file('selfie_image')->store('kyc/selfie', 'public') 
            : null;

        // 4. Update or Create VerifiedUser record
        $kyc = VerifiedUser::updateOrCreate(
            ['auth_user_id' => $user->id],
            [
                'full_name' => $payload['full_name'],
                'email' => $payload['email'],
                'pan_number' => strtoupper($payload['pan_number']),
                'aadhaar_number' => preg_replace('/\s+/', '', $payload['aadhaar_number']),
                'pan_image' => $panPath,
                'aadhaar_image' => $aadhaarPath,
                'selfie_image' => $selfiePath,
                'kyc_status' => 'pending', // Re-verify if it was rejected before
            ]
        );

        return ApiResponse::success([
            'message' => 'KYC documents submitted successfully. Please wait for administrator approval.',
            'kyc' => [
                'full_name' => $kyc->full_name,
                'status' => $kyc->kyc_status,
                'submitted_at' => $kyc->updated_at->toIso8601String(),
            ]
        ]);
    }

    /**
     * Get current KYC status.
     * 
     * GET /api/v1/profile/kyc-status
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $kyc = $user->verifiedUser;

        if (!$kyc) {
            return ApiResponse::success([
                'status' => 'not_submitted',
                'message' => 'KYC documents not yet submitted.',
            ]);
        }

        return ApiResponse::success([
            'status' => $kyc->kyc_status,
            'full_name' => $kyc->full_name,
            'is_trading_enabled' => (bool)$kyc->is_trading_enabled,
            'limits' => [
                'gold' => (float)$kyc->gold_limit,
                'silver' => (float)$kyc->silver_limit,
            ],
            'message' => 'KYC status retrieved successfully.',
        ]);
    }
}
