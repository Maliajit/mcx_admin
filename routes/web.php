<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::view('/admin/dashboard', 'admin.dashboard.index');

// User Management
Route::view('/admin/users', 'admin.users.index');
Route::view('/admin/users/requests', 'admin.users.requests');
Route::view('/admin/users/show', 'admin.users.show');

// Order Management
Route::view('/admin/orders', 'admin.orders.index');
Route::view('/admin/orders/pending', 'admin.orders.pending');
Route::view('/admin/orders/completed', 'admin.orders.completed');
Route::view('/admin/orders/show', 'admin.orders.show');

// Market & Reports
Route::view('/admin/rates/gold', 'admin.rates.gold.index');
Route::view('/admin/rates/silver', 'admin.rates.silver.index');
Route::view('/admin/reports/history', 'admin.reports.history');

Route::view('/admin/settings', 'admin.settings.index');
Route::view('/admin/login', 'admin.auth.login.index');

