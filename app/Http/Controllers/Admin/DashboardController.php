<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard.index', [
            'recentOrders' => Order::query()->latest('placed_at')->latest('id')->limit(5)->get(),
            'pendingOrdersCount' => Order::query()->where('status', 'pending')->count(),
            'completedOrdersCount' => Order::query()->where('status', 'completed')->count(),
        ]);
    }
}
