<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('transactions', TransactionController::class)
        ->only(['index', 'create', 'show']);

    Route::resource('manifests', ManifestController::class)
        ->only(['index', 'create', 'store', 'show']);
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
