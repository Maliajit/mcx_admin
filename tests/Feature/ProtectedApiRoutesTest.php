<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProtectedApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_is_unverified_by_default(): void
    {
        config()->set('api.auth.demo_profile.email', 'local-user@example.com');
        config()->set('api.auth.demo_profile.name', 'Local User');
        config()->set('api.auth.demo_profile.phone', '+91 9999999999');

        $this->getJson('/api/v1/profile')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.profile.is_verified', false)
            ->assertJsonPath('data.profile.kyc_status', 'unverified');
    }

    public function test_kyc_submission_marks_profile_verified(): void
    {
        config()->set('api.auth.demo_profile.email', 'local-user@example.com');
        config()->set('api.auth.demo_profile.name', 'Local User');
        config()->set('api.auth.demo_profile.phone', '+91 9999999999');

        $this->post('/api/v1/profile/kyc', [
            'name' => 'Verified User',
            'email' => 'verified@example.com',
            'phone' => '+91 8888888888',
            'pan_number' => 'ABCDE1234F',
            'aadhaar_number' => '123412341234',
            'pan_image' => UploadedFile::fake()->image('pan.jpg'),
            'aadhaar_front_image' => UploadedFile::fake()->image('aadhaar-front.jpg'),
            'aadhaar_back_image' => UploadedFile::fake()->image('aadhaar-back.jpg'),
            'selfie_image' => UploadedFile::fake()->image('selfie.jpg'),
        ])->assertOk()
            ->assertJsonPath('data.profile.is_verified', true)
            ->assertJsonPath('data.profile.kyc_status', 'verified');
    }

    public function test_order_creation_is_blocked_for_unverified_profile(): void
    {
        config()->set('api.auth.demo_profile.email', 'local-user@example.com');
        config()->set('api.auth.demo_profile.name', 'Local User');
        config()->set('api.auth.demo_profile.phone', '+91 9999999999');

        $this->postJson('/api/v1/orders', [
            'asset' => 'GOLD',
            'side' => 'buy',
            'order_type' => 'market',
            'quantity' => 1,
            'price' => 100,
            'total' => 100,
        ])->assertForbidden()
            ->assertJsonPath('data.code', 'kyc_required');
    }

    public function test_verified_profile_can_place_order_and_it_is_stored(): void
    {
        config()->set('api.auth.demo_profile.email', 'local-user@example.com');
        config()->set('api.auth.demo_profile.name', 'Local User');
        config()->set('api.auth.demo_profile.phone', '+91 9999999999');

        $user = User::factory()->create([
            'email' => 'local-user@example.com',
            'name' => 'Local User',
            'phone' => '+91 9999999999',
            'kyc_status' => 'verified',
            'kyc_verified_at' => now(),
        ]);

        $this->postJson('/api/v1/orders', [
            'asset' => 'GOLD',
            'side' => 'buy',
            'order_type' => 'market',
            'quantity' => 1.5,
            'price' => 100,
            'total' => 150,
        ])->assertCreated()
            ->assertJsonPath('data.order.user_id', $user->id)
            ->assertJsonPath('data.order.status', 'pending');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'asset' => 'GOLD',
            'status' => 'pending',
        ]);
    }

    public function test_auth_settings_returns_business_controlled_content_from_config(): void
    {
        config()->set('api.mobile_app.support.phone', '+91 9999999999');
        config()->set('api.mobile_app.support.message', 'Contact support for assisted KYC.');
        config()->set('api.mobile_app.kyc.rules', ['PAN must be valid', 'Name must match PAN']);

        $this->getJson('/api/v1/auth/settings')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.support.phone', '+91 9999999999')
            ->assertJsonPath('data.kyc.rules.0', 'PAN must be valid')
            ->assertJsonPath('error', null);
    }
}
