<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpService
{
    private const CACHE_PREFIX = 'otp:';
    private const OTP_LENGTH = 6;
    private const OTP_EXPIRY_MINUTES = 5;

    public function generateOtp(string $phone): string
    {
        $otp = $this->generateRandomOtp();

        // Store OTP in cache with phone number as key
        Cache::put(
            self::CACHE_PREFIX . $phone,
            $otp,
            now()->addMinutes(self::OTP_EXPIRY_MINUTES)
        );

        // Log OTP for testing (remove in production)
        Log::info("OTP generated for phone {$phone}: {$otp}");

        return $otp;
    }

    public function verifyOtp(string $phone, string $otp): bool
    {
        $cachedOtp = Cache::get(self::CACHE_PREFIX . $phone);

        if (!$cachedOtp || $cachedOtp !== $otp) {
            return false;
        }

        // Clear the OTP after successful verification
        Cache::forget(self::CACHE_PREFIX . $phone);

        return true;
    }

    public function isOtpValid(string $phone): bool
    {
        return Cache::has(self::CACHE_PREFIX . $phone);
    }

    private function generateRandomOtp(): string
    {
        return str_pad((string) random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }
}
