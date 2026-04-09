<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class KycRequestsController extends Controller
{
    public function index(): View
    {
        $kycRequests = KycRequest::with('user')->latest()->get();

        return view('admin.kyc.requests', compact('kycRequests'));
    }

    public function approve(Request $request, KycRequest $kycRequest): RedirectResponse
    {
        if ($kycRequest->status !== 'pending') {
            return back()->with('error', 'KYC request is not pending.');
        }

        $kycRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(), // Assuming admin auth, but for demo use 1
            'approved_at' => now(),
        ]);

        $kycRequest->user->update(['is_verified' => true]);

        return back()->with('success', 'KYC request approved.');
    }

    public function reject(Request $request, KycRequest $kycRequest): RedirectResponse
    {
        if ($kycRequest->status !== 'pending') {
            return back()->with('error', 'KYC request is not pending.');
        }

        $kycRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'KYC request rejected.');
    }
}
