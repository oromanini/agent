<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EdeltecController;
use App\Http\Controllers\SoollarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/soollar/update-products', [SoollarController::class, 'index']);
    Route::post('/edeltec/update-products', [EdeltecController::class, 'index']);
});

Route::post('/authorize', [AuthController::class, 'login']);
