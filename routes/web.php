<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KycRequestsController;
use App\Http\Controllers\Admin\OrderPagesController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/admin/dashboard', DashboardController::class);

// User Management
Route::get('/admin/users/requests', [KycRequestsController::class, 'index'])->name('kyc.requests');
Route::post('/admin/users/{user}/enable-trading', [UsersController::class, 'enableTrading'])->name('users.enableTrading');
Route::post('/admin/users/{user}/update-limits', [UsersController::class, 'updateLimits'])->name('users.updateLimits');
Route::post('/admin/kyc/{verifiedUser}/approve', [KycRequestsController::class, 'approve'])->name('admin.kyc.approve');
Route::post('/admin/kyc/{verifiedUser}/reject', [KycRequestsController::class, 'reject'])->name('admin.kyc.reject');

// Order Management
Route::get('/admin/orders', [OrderPagesController::class, 'index'])->name('orders.index');
Route::get('/admin/orders/pending', [OrderPagesController::class, 'pending'])->name('orders.pending');
Route::get('/admin/orders/completed', [OrderPagesController::class, 'completed'])->name('orders.completed');
Route::get('/admin/orders/{order}', [OrderPagesController::class, 'show'])->name('orders.show')->whereNumber('order');
Route::post('/admin/orders/{order}/approve', [OrderPagesController::class, 'approve'])->name('orders.approve');
Route::post('/admin/orders/{order}/reject', [OrderPagesController::class, 'reject'])->name('orders.reject');

// Market & Reports
Route::view('/admin/rates/gold', 'admin.rates.gold.index');
Route::view('/admin/rates/silver', 'admin.rates.silver.index');
Route::view('/admin/reports/history', 'admin.reports.history');

Route::get('/admin/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings.index');
Route::post('/admin/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update');

Route::resource('admin/products', \App\Http\Controllers\Admin\ProductRowController::class)->names('admin.products');
Route::resource('admin/coins', \App\Http\Controllers\Admin\CoinController::class)->names('admin.coins');
Route::view('/admin/login', 'admin.auth.login.index');
