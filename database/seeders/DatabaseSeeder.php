<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TradingSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // 1. Create a primary test user
        $user = User::updateOrCreate(
            ['mobile' => '9999999999'],
            [
                'otp_verified' => true,
                'password' => bcrypt('password'),
            ]
        );

        // 2. Create the verified profile for the test user
        $user->verifiedUser()->updateOrCreate(
            ['auth_user_id' => $user->id],
            [
                'full_name' => 'Demo User',
                'email' => 'test@example.com',
                'kyc_status' => 'approved',
                'gold_limit' => 1000.0000,
                'silver_limit' => 50000.0000,
                'is_trading_enabled' => true,
            ]
        );

        // 3. Seed default trading settings
        $settings = [
            'gst_percentage' => '3.0',
            'tds_percentage' => '1.0',
            'price_source' => 'manual',
            'gold_base_price' => '62500.00',
            'silver_base_price' => '72000.00',
            'primary_color' => '#FFAA00',
            'secondary_color' => '#000000',
        ];

        foreach ($settings as $key => $value) {
            TradingSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
