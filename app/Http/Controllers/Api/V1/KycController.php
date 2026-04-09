<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KycRequest;
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
        ]);

        $user = $this->userResolver->resolve($request);

        // Check if user already has a pending or approved KYC request
        $existingRequest = KycRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRequest) {
            return ApiResponse::error('KYC request already exists.', 400);
        }

        $kycRequest = KycRequest::create([
            'user_id' => $user->id,
            'name' => $payload['name'],
            'pan' => strtoupper($payload['pan']),
            'aadhaar' => preg_replace('/\s+/', '', $payload['aadhaar']),
        ]);

        return ApiResponse::success([
            'kyc_request' => [
                'id' => $kycRequest->id,
                'name' => $kycRequest->name,
                'pan' => $kycRequest->pan,
                'aadhaar' => $kycRequest->aadhaar,
                'status' => $kycRequest->status,
                'created_at' => $kycRequest->created_at->toIso8601String(),
            ],
            'message' => 'KYC request submitted successfully.',
        ]);
    }
}
