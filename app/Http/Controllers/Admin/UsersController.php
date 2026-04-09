<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class UsersController extends Controller
{
    /**
     * Enable trading for a user.
     */
    public function enableTrading(Request $request, User $user): RedirectResponse
    {
        $kyc = $user->verifiedUser;
        
        if (!$kyc || $kyc->kyc_status !== 'approved') {
            return back()->with('error', 'User must be KYC verified and approved first.');
        }

        $kyc->update(['is_trading_enabled' => true]);

        return back()->with('success', 'Trading enabled for user.');
    }

    /**
     * Update trading limits for a user.
     */
    public function updateLimits(Request $request, User $user): RedirectResponse
    {
        $kyc = $user->verifiedUser;

        if (!$kyc) {
            return back()->with('error', 'User has no KYC record.');
        }

        $data = $request->validate([
            'gold_limit' => 'required|numeric|min:0',
            'silver_limit' => 'required|numeric|min:0',
        ]);

        $kyc->update($data);

        return back()->with('success', 'User limits updated successfully.');
    }
}
