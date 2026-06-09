<?php

use App\Http\Controllers\CommodityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\PriceRecordController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Auth routes (no auth middleware)
Route::get('/login', [SessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [SessionController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [SessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('commodities', CommodityController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('regions', RegionController::class)
        ->only(['index', 'create', 'store']);

    Route::resource('price-records', PriceRecordController::class)
        ->only(['index', 'create', 'store']);

    Route::prefix('predictions')->name('predictions.')->group(function () {
        Route::get('/', [PredictionController::class, 'index'])->name('index');
        Route::get('/create', [PredictionController::class, 'create'])->name('create');
        Route::post('/generate', [PredictionController::class, 'generate'])->name('generate');
        Route::delete('/{id}', [PredictionController::class, 'destroy'])->name('destroy');
    });
});
