<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;

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
}
