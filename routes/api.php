<?php

use App\Http\Controllers\Api\V1\AppConfigController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AuthSettingsController;
use App\Http\Controllers\Api\V1\KycController;
use App\Http\Controllers\Api\V1\LiveRatesController;
use App\Http\Controllers\Api\V1\NewsController;
use App\Http\Controllers\Api\V1\OrdersController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\TradeHistoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Public Auth endpoints
    Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::get('/auth/settings', AuthSettingsController::class);
    Route::get('/login', fn() => response()->json(['message' => 'Unauthenticated.'], 401))->name('login');

    // Dynamic Configuration & Home Data (Public for initial app load)
    Route::get('/config', [AppConfigController::class, 'index']);
    Route::get('/live-rates', LiveRatesController::class)->middleware('throttle:live-rates');
    Route::get('/news', NewsController::class);

    // Protected endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/user', [AuthController::class, 'profile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/profile', ProfileController::class);
        Route::post('/profile/kyc', [KycController::class, 'store']);
        Route::get('/profile/kyc-status', [KycController::class, 'status']); // Added as per prompt

        Route::get('/orders', [OrdersController::class, 'index']);
        Route::post('/orders', [OrdersController::class, 'store']);
        Route::get('/trade-history', TradeHistoryController::class);
    });
});
