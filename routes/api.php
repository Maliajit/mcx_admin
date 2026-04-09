<?php

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
    // Auth endpoints
    Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);

    Route::get('/live-rates', LiveRatesController::class)
        ->middleware('throttle:live-rates');
    Route::get('/news', NewsController::class);
    Route::get('/auth/settings', AuthSettingsController::class);
    Route::get('/orders', [OrdersController::class, 'index']);
    Route::post('/orders', [OrdersController::class, 'store']);
    Route::get('/trade-history', TradeHistoryController::class);
    Route::get('/profile', ProfileController::class);
    Route::post('/profile/kyc', [KycController::class, 'store']);
});
