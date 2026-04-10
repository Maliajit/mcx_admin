<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class LocalAppUserResolver
{
    /**
     * Resolve the authenticated app user from the request.
     * Returns null if no valid session token is found.
     */
    public function resolve(Request $request = null): ?User
    {
        $sessionToken = $request?->header('Authorization') ?? $request?->input('session_token');

        // Strip 'Bearer ' prefix if present (sent by KycApiService and others)
        if ($sessionToken && str_starts_with($sessionToken, 'Bearer ')) {
            $sessionToken = substr($sessionToken, 7);
        }

        if ($sessionToken && str_starts_with($sessionToken, 'mock-session-')) {
            $userId = (int) str_replace('mock-session-', '', $sessionToken);
            $user = User::find($userId);

            if ($user) {
                return $user;
            }
        }

        // No valid session: return null so controllers can handle authentication errors explicitly.
        return null;
    }
}
