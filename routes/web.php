<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderPagesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/admin/dashboard', DashboardController::class);

// User Management
Route::view('/admin/users', 'admin.users.index');
Route::view('/admin/users/requests', 'admin.users.requests');
Route::view('/admin/users/show', 'admin.users.show');

// Order Management
Route::get('/admin/orders', [OrderPagesController::class, 'index']);
Route::get('/admin/orders/pending', [OrderPagesController::class, 'pending']);
Route::get('/admin/orders/completed', [OrderPagesController::class, 'completed']);
Route::get('/admin/orders/{order}', [OrderPagesController::class, 'show'])->whereNumber('order');

// Market & Reports
Route::view('/admin/rates/gold', 'admin.rates.gold.index');
Route::view('/admin/rates/silver', 'admin.rates.silver.index');
Route::view('/admin/reports/history', 'admin.reports.history');

Route::view('/admin/settings', 'admin.settings.index');
Route::view('/admin/login', 'admin.auth.login.index');
