<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserTrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/sistema', function () {
    return view('app');
})->middleware(['auth', 'role:mantenimiento,administrador'])->name('sistema');

Route::get('/consulta', [UserTrackingController::class, 'index'])
    ->middleware(['auth', 'role:usuario'])
    ->name('consulta');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = request()->user();

        if ($user?->isUser()) {
            return redirect()->route('consulta');
        }

        return redirect()->route('sistema');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserManagementController::class)->except(['show']);
});

require __DIR__.'/auth.php';
