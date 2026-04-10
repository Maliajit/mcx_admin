<?php

namespace App\Services;

use App\Models\User;
use Exception;

class UserValidationService
{
    public function __construct(
        private readonly OrderLimitService $orderLimitService,
    ) {
    }

    /**
     * Validate if a user can place an order.
     *
     * @param User $authUser
     * @param string $type ('gold' or 'silver')
     * @param float $quantity
     * @return bool
     * @throws Exception
     */
    public function validateForOrder(
        User $authUser,
        string $type,
        float $quantity,
        string $productType = 'row',
        int $productId = 0
    ): bool
    {
        // 1. Check otp_verified
        if (!$authUser->otp_verified) {
            throw new Exception("Mobile not verified via OTP.");
        }

        // 2. Check if verified_user exists
        $verifiedUser = $authUser->verifiedUser;
        if (!$verifiedUser) {
            throw new Exception("KYC documentation not submitted.");
        }

        // 3. Check kyc_status
        if ($verifiedUser->kyc_status !== 'approved') {
            throw new Exception("Your KYC status is {$verifiedUser->kyc_status}. Approval is required before trading.");
        }

        // 4. Check is_trading_enabled
        if (!$verifiedUser->is_trading_enabled) {
            throw new Exception("Trading is currently disabled for your account by the administrator.");
        }

        $assetName = $type === 'silver' ? 'silver' : 'gold';
        $this->orderLimitService->assertWithinRemainingLimit(
            $authUser,
            $assetName,
            $productType,
            $productId,
            $quantity
        );

        return true;
    }
}
