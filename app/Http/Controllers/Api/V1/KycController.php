<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\VerifiedUser;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\LocalAppUserResolver;

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
            'pan' => ['required', 'string', 'max:20'],
            'aadhaar' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
        ]);

        $user = $this->userResolver->resolve($request);

        if (!$user) {
            return ApiResponse::error('Unauthenticated. Please log in to submit KYC.', 401);
        }

        // Update or create verified user details
        $verifiedUser = VerifiedUser::updateOrCreate(
            ['auth_user_id' => $user->id],
            [
                'full_name' => $payload['name'],
                'pan_number' => strtoupper($payload['pan']),
                'aadhaar_number' => preg_replace('/\s+/', '', $payload['aadhaar']),
                'email' => $payload['email'] ?? null,
                'kyc_status' => 'pending', // Reset to pending on update
            ]
        );

        // Handle image uploads if present
        if ($request->hasFile('pan_image')) {
            $verifiedUser->update(['pan_image' => $request->file('pan_image')->store('kyc/pan', 'public')]);
        }
        if ($request->hasFile('aadhaar_front_image')) {
            $verifiedUser->update(['aadhaar_image' => $request->file('aadhaar_front_image')->store('kyc/aadhaar', 'public')]);
        }
        if ($request->hasFile('selfie_image')) {
            $verifiedUser->update(['selfie_image' => $request->file('selfie_image')->store('kyc/selfie', 'public')]);
        }

        return ApiResponse::success([
            'message' => 'KYC request submitted successfully.',
            'profile' => [
                'name' => $verifiedUser->full_name,
                'email' => $verifiedUser->email,
                'phone' => $user->mobile,
                'kyc_status' => $verifiedUser->kyc_status,
                'is_verified' => false,
            ],
        ]);
    }
}
