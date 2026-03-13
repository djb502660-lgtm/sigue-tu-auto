<?php

use App\Http\Controllers\Api\ServiceOrderController;
use App\Http\Controllers\Api\StatusController;
use Illuminate\Support\Facades\Route;

Route::get('/service-orders', [ServiceOrderController::class, 'index']);
Route::post('/service-orders', [ServiceOrderController::class, 'store']);
Route::get('/service-orders/{serviceOrder}', [ServiceOrderController::class, 'show']);

Route::get('/statuses', [StatusController::class, 'index']);
Route::post('/service-orders/{serviceOrder}/status', [StatusController::class, 'change']);

