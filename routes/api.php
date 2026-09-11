<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PosApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);

    // Florist
    Route::get('/orders/pending', [OrderController::class, 'pendingQueue']);
    Route::post('/orders/{id}/complete', [OrderController::class, 'markCompleted']);

    // Marketing
    Route::post('/orders/online', [OrderController::class, 'storeOnline']);
});

// POS Kiosk API (Public/Generic Access for Walk-in Kiosk Devices)
Route::prefix('pos')->group(function () {
    Route::get('/categories', [PosApiController::class, 'getCategories']);
    Route::get('/products', [PosApiController::class, 'getProducts']);
    Route::get('/materials', [PosApiController::class, 'getMaterials']);
    Route::post('/checkout', [PosApiController::class, 'checkout']);
});
