<?php

namespace App\Services;

use App\Models\User;
use Exception;

class UserValidationService
{
    /**
     * Validate if a user can place an order.
     *
     * @param User $authUser
     * @param string $type ('gold' or 'silver')
     * @param float $quantity
     * @return bool
     * @throws Exception
     */
    public function validateForOrder(User $authUser, string $type, float $quantity): bool
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

        // 5. Check user limits
        if ($type === 'gold' && $quantity > (float)$verifiedUser->gold_limit) {
            throw new Exception("Requested quantity exceeds your Gold trading limit of {$verifiedUser->gold_limit}g.");
        }

        if ($type === 'silver' && $quantity > (float)$verifiedUser->silver_limit) {
            throw new Exception("Requested quantity exceeds your Silver trading limit of {$verifiedUser->silver_limit}g.");
        }

        return true;
    }
}
