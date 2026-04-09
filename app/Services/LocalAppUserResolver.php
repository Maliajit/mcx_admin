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
            ['email' => (string) config('api.auth.demo_profile.email')],
            [
                'name' => (string) config('api.auth.demo_profile.name'),
                'phone' => (string) config('api.auth.demo_profile.phone'),
                'password' => Hash::make('local-app-user'),
            ],
        );

        $updates = [];

        if ($user->name !== (string) config('api.auth.demo_profile.name')) {
            $updates['name'] = (string) config('api.auth.demo_profile.name');
        }

        if ($user->phone !== (string) config('api.auth.demo_profile.phone')) {
            $updates['phone'] = (string) config('api.auth.demo_profile.phone');
        }

        if ($updates !== []) {
            $user->fill($updates)->save();
        }

        return $user->fresh();
    }
}
