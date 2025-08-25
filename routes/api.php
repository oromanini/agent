<?php

use App\Http\Controllers\ActiveKitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\EdeltecController;
use App\Http\Controllers\KitsController;
use App\Http\Controllers\SoollarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/soollar/update-products', [SoollarController::class, 'index']);
    Route::post('/edeltec/update-products', [EdeltecController::class, 'index']);

    Route::prefix('brands')->group(function () {
        Route::post('/{type}', [BrandController::class, 'store'])->name('api.brands.store');
        Route::put('/{type}/{id}', [BrandController::class, 'update'])->name('api.brands.update');
        Route::delete('/{type}/{id}', [BrandController::class, 'destroy'])->name('api.brands.destroy');
        Route::patch('/{type}/{id}/toggle', [BrandController::class, 'toggleActive'])->name('api.brands.toggle');
    });

    Route::put('/active-kits/{activeKit}/toggle', [ActiveKitController::class, 'toggleActive']);
});

Route::post('/authorize', [AuthController::class, 'login']);
