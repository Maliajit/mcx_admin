<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class LocalAppUserResolver
{
    public function resolve(Request $request = null): User
    {
        // Check if we have a session token (from OTP login)
        $sessionToken = $request?->header('Authorization') ?? $request?->input('session_token');

        if (is_string($sessionToken) && str_starts_with($sessionToken, 'Bearer ')) {
            $sessionToken = substr($sessionToken, 7);
        }

        if ($sessionToken && str_starts_with($sessionToken, 'mock-session-')) {
            $userId = (int) str_replace('mock-session-', '', $sessionToken);
            $user = User::find($userId);

            if ($user) {
                return $user;
            }
        }

        // Fallback to demo user for development
        /** @var User $user */
        $user = User::query()->firstOrCreate(
            ['mobile' => '9876543210'],
            [
                'password' => Hash::make('local-app-user'),
                'otp_verified' => true,
            ],
        );

        return $user->fresh();
    }
}
