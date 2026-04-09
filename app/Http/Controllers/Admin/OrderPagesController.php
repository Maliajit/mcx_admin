<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class OrderPagesController extends Controller
{
    public function index(): View
    {
        return view('admin.orders.index', [
            'orders' => Order::query()->latest('placed_at')->latest('id')->get(),
        ]);
    }

    public function pending(): View
    {
        return view('admin.orders.pending', [
            'orders' => Order::query()
                ->where('status', 'pending')
                ->latest('placed_at')
                ->latest('id')
                ->get(),
        ]);
    }

    public function completed(): View
    {
        return view('admin.orders.completed', [
            'orders' => Order::query()
                ->where('status', 'completed')
                ->latest('placed_at')
                ->latest('id')
                ->get(),
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    public function approve(Request $request, Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Order is not pending.');
        }

        $order->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Order approved.');
    }

    public function reject(Request $request, Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Order is not pending.');
        }

        $order->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Order rejected.');
    }
}
