<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LocalAppUserResolver;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function __construct(
        private readonly LocalAppUserResolver $userResolver,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'gst_number' => ['nullable', 'string', 'max:50'],
            'pan_number' => ['required', 'string', 'max:20'],
            'aadhaar_number' => ['required', 'string', 'max:20'],
            'pan_image' => ['required', 'image', 'max:5120'],
            'aadhaar_front_image' => ['required', 'image', 'max:5120'],
            'aadhaar_back_image' => ['required', 'image', 'max:5120'],
            'selfie_image' => ['required', 'image', 'max:5120'],
        ]);

        $user = $this->userResolver->resolve();
        $panImagePath = $request->file('pan_image')->store('kyc/pan', 'public');
        $aadhaarFrontImagePath = $request->file('aadhaar_front_image')->store('kyc/aadhaar-front', 'public');
        $aadhaarBackImagePath = $request->file('aadhaar_back_image')->store('kyc/aadhaar-back', 'public');
        $selfieImagePath = $request->file('selfie_image')->store('kyc/selfie', 'public');

        $user->fill([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone' => $payload['phone'],
            'gst_number' => $payload['gst_number'] ?? null,
            'pan_number' => strtoupper($payload['pan_number']),
            'pan_image_path' => $panImagePath,
            'aadhaar_number' => preg_replace('/\s+/', '', $payload['aadhaar_number']),
            'aadhaar_front_image_path' => $aadhaarFrontImagePath,
            'aadhaar_back_image_path' => $aadhaarBackImagePath,
            'selfie_reference' => basename($selfieImagePath),
            'selfie_image_path' => $selfieImagePath,
            'kyc_status' => 'verified',
            'kyc_submitted_at' => now(),
            'kyc_verified_at' => now(),
        ]);
        $user->save();

        return ApiResponse::success([
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_verified' => true,
                'kyc_status' => $user->kyc_status,
                'pan_number' => $user->pan_number,
                'pan_image_url' => $user->pan_image_path ? Storage::disk('public')->url($user->pan_image_path) : null,
                'aadhaar_number' => $user->aadhaar_number,
                'aadhaar_front_image_url' => $user->aadhaar_front_image_path ? Storage::disk('public')->url($user->aadhaar_front_image_path) : null,
                'aadhaar_back_image_url' => $user->aadhaar_back_image_path ? Storage::disk('public')->url($user->aadhaar_back_image_path) : null,
                'selfie_image_url' => $user->selfie_image_path ? Storage::disk('public')->url($user->selfie_image_path) : null,
                'gst_number' => $user->gst_number,
                'kyc_verified_at' => optional($user->kyc_verified_at)->toIso8601String(),
            ],
            'message' => 'KYC submitted and verified successfully.',
        ]);
    }
}
