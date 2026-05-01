<?php

use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\ServiceOrderController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\UserTrackingApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::middleware(['auth', 'role:mantenimiento,administrador'])->group(function () {
        Route::get('/service-orders', [ServiceOrderController::class, 'index']);
        Route::post('/service-orders', [ServiceOrderController::class, 'store']);
        Route::get('/service-orders/{serviceOrder}', [ServiceOrderController::class, 'show']);

        Route::get('/statuses', [StatusController::class, 'index']);
        Route::post('/service-orders/{serviceOrder}/status', [StatusController::class, 'change']);
    });

    Route::middleware(['auth', 'role:usuario'])->group(function () {
        Route::get('/my-service-orders', [UserTrackingApiController::class, 'index']);
        Route::get('/my-service-orders/{serviceOrder}', [UserTrackingApiController::class, 'show']);
    });

    Route::post('/chat', [ChatbotController::class, 'chat']);
});

