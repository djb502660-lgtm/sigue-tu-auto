<?php

use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\ServiceOrderController;
use App\Http\Controllers\Api\StatusController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

// Duplicamos las rutas de la API aquí para evitar problemas
// con la carga de routes/api.php en este entorno.
Route::prefix('api')->group(function () {
    Route::get('/service-orders', [ServiceOrderController::class, 'index']);
    Route::post('/service-orders', [ServiceOrderController::class, 'store']);
    Route::get('/service-orders/{serviceOrder}', [ServiceOrderController::class, 'show']);

    Route::get('/statuses', [StatusController::class, 'index']);
    Route::post('/service-orders/{serviceOrder}/status', [StatusController::class, 'change']);

    Route::post('/chat', [ChatbotController::class, 'chat']);
});

