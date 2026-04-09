<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class UsersController extends Controller
{
    public function enableTrading(Request $request, User $user): RedirectResponse
    {
        if (!$user->is_verified) {
            return back()->with('error', 'User must be KYC verified first.');
        }

        $user->update(['can_trade' => true]);

        return back()->with('success', 'Trading enabled for user.');
    }
}
