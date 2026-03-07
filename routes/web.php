<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::post('/language', [\App\Http\Controllers\LanguageController::class, 'store'])->name('language.store');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\MonitorController::class, 'index'])->name('dashboard');
    Route::get('/monitors/create', [\App\Http\Controllers\MonitorController::class, 'create'])->name('monitors.create');
    Route::post('/monitors', [\App\Http\Controllers\MonitorController::class, 'store'])->name('monitors.store');
    Route::get('/monitors/{monitor}', [\App\Http\Controllers\MonitorController::class, 'show'])->name('monitors.show');
    Route::put('/monitors/{monitor}', [\App\Http\Controllers\MonitorController::class, 'update'])->name('monitors.update');
    Route::delete('/monitors/{monitor}', [\App\Http\Controllers\MonitorController::class, 'destroy'])->name('monitors.destroy');
    Route::patch('/monitors/{monitor}/toggle', [\App\Http\Controllers\MonitorController::class, 'toggle'])->name('monitors.toggle');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
