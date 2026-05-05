<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\MonitorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserTrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/sistema', function () {
    return view('app');
})->middleware(['auth', 'role:mantenimiento'])->name('sistema');

Route::get('/consulta', [UserTrackingController::class, 'index'])
    ->middleware(['auth', 'role:usuario'])
    ->name('consulta');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = request()->user();

        if ($user?->isUser()) {
            return redirect()->route('consulta');
        }

        if ($user?->isAdmin()) {
            return redirect()->route('admin.monitor.dashboard');
        }

        return redirect()->route('sistema');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/monitor', [MonitorController::class, 'dashboard'])->name('monitor.dashboard');
    Route::get('/monitor/configuracion', [MonitorController::class, 'configuration'])->name('monitor.configuration');
    Route::get('/monitor/cuenta', [MonitorController::class, 'account'])->name('monitor.account');
    Route::get('/monitor/roles', [MonitorController::class, 'roleAssignment'])->name('monitor.role-assignment');
    Route::get('/monitor/historial', [MonitorController::class, 'history'])->name('monitor.history');
    Route::get('/monitor/notificaciones', [MonitorController::class, 'notifications'])->name('monitor.notifications');

    Route::resource('users', UserManagementController::class)->except(['show']);
});

require __DIR__.'/auth.php';
