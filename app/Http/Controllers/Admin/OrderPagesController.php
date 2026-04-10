<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Services\OrderLimitService;
use App\Services\OrderProcessingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class OrderPagesController extends Controller
{
    /**
     * Show confirmed orders (The target main list).
     */
    public function index(): View
    {
        return view('admin.orders.index', [
            'orders' => Order::query()
                ->where('status', 'confirmed')
                ->latest('placed_at')
                ->latest('id')
                ->get(),
        ]);
    }

    /**
     * Show pending limit orders awaiting price hit.
     */
    public function pending(): View
    {
        return view('admin.orders.pending', [
            'orders' => Order::query()
                ->where('status', 'pending')
                ->where('type', 'limit')
                ->latest('placed_at')
                ->latest('id')
                ->get(),
        ]);
    }

    /**
     * Show delivered order history.
     */
    public function completed(): View
    {
        return view('admin.orders.completed', [
            'orders' => Order::query()
                ->where('status', 'delivered')
                ->latest('placed_at')
                ->latest('id')
                ->get(),
        ]);
    }

    /**
     * Trigger automatic limit processing (Heartbeat).
     */
    public function processLimits(OrderProcessingService $service): JsonResponse
    {
        $count = $service->processPendingLimits();
        return response()->json([
            'success' => true,
            'processed_count' => $count,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Confirm a limit order (Limit Hit). Move to main orders.
     */
    public function approve(Request $request, Order $order, OrderLimitService $orderLimitService): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Order is not in pending state.');
        }

        $order->update([
            'status' => 'confirmed',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $orderLimitService->consumeRemainingLimit($order->load('user.verifiedUser'));

        return back()->with('success', 'Limit order confirmed and moved to active orders.');
    }

    /**
     * Mark a confirmed order as Delivered.
     */
    public function deliver(Request $request, Order $order): RedirectResponse
    {
        if ($order->status !== 'confirmed') {
            return back()->with('error', 'Order must be confirmed before delivery.');
        }

        $order->update([
            'status' => 'delivered',
        ]);

        return back()->with('success', 'Order marked as Delivered and moved to history.');
    }

    public function reject(Request $request, Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending limit orders can be rejected.');
        }

        $order->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Order rejected.');
    }
}
