<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VerifiedUser;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user in auth_users
        $user = User::updateOrCreate(
            ['mobile' => '9876543210'],
            [
                'otp_verified' => true,
                'is_blocked' => false,
            ]
        );

        // Create KYC details in verified_users
        $user->verifiedUser()->updateOrCreate(
            ['auth_user_id' => $user->id],
            [
                'full_name' => 'Demo Trader',
                'email' => 'trader@mcx.in',
                'pan_number' => 'ABCDE1234F',
                'aadhaar_number' => '123456789012',
                'kyc_status' => 'approved',
                'gold_limit' => 100.00,
                'silver_limit' => 5000.00,
                'is_trading_enabled' => true,
            ]
        );
    }
}
