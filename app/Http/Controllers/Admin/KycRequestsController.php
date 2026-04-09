<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerifiedUser;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class KycRequestsController extends Controller
{
    /**
     * List all KYC/Profiling requests.
     */
    public function index(): View
    {
        // Fetch users who have submitted KYC (i.e. have a verified_users record)
        $kycRequests = VerifiedUser::with('user')->latest()->get();

        return view('admin.kyc.requests', compact('kycRequests'));
    }

    /**
     * Approve KYC.
     */
    public function approve(Request $request, VerifiedUser $verifiedUser): RedirectResponse
    {
        if ($verifiedUser->kyc_status !== 'pending') {
            return back()->with('error', 'KYC request is not pending.');
        }

        $verifiedUser->update([
            'kyc_status' => 'approved',
            'is_trading_enabled' => true, // Auto-enable trading on approval
        ]);

        return back()->with('success', 'KYC request approved and trading enabled.');
    }

    /**
     * Reject KYC.
     */
    public function reject(Request $request, VerifiedUser $verifiedUser): RedirectResponse
    {
        if ($verifiedUser->kyc_status !== 'pending') {
            return back()->with('error', 'KYC request is not pending.');
        }

        $verifiedUser->update([
            'kyc_status' => 'rejected',
            'is_trading_enabled' => false,
        ]);

        return back()->with('success', 'KYC request rejected.');
    }
}
