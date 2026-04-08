<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LocalAppUserResolver
{
    public function resolve(): User
    {
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
