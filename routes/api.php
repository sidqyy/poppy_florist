<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/dashboard/stats', [\App\Http\Controllers\Api\DashboardController::class, 'getStats']);
    
    // Florist
    Route::get('/orders/pending', [\App\Http\Controllers\Api\OrderController::class, 'pendingQueue']);
    Route::post('/orders/{id}/complete', [\App\Http\Controllers\Api\OrderController::class, 'markCompleted']);

    // Marketing
    Route::post('/orders/online', [\App\Http\Controllers\Api\OrderController::class, 'storeOnline']);
});

// POS Kiosk API (Public/Generic Access for Walk-in Kiosk Devices)
Route::prefix('pos')->group(function () {
    Route::get('/categories', [\App\Http\Controllers\Api\PosApiController::class, 'getCategories']);
    Route::get('/products', [\App\Http\Controllers\Api\PosApiController::class, 'getProducts']);
    Route::get('/materials', [\App\Http\Controllers\Api\PosApiController::class, 'getMaterials']);
    Route::post('/checkout', [\App\Http\Controllers\Api\PosApiController::class, 'checkout']);
});
