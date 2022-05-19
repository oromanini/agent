<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProposalController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function() {

    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/logout', [AuthenticatedSessionController::class,'destroy'])->name('logout');

    Route::controller(ClientController::class)->group(function () {

        Route::name('client.')->group(function () {

            Route::get('/clientes', 'index')->name('index');
            Route::get('/clientes/novo', 'create')->name('create');
            Route::post('/clientes/novo', 'store')->name('store');
            Route::get('/clientes/editar/{id}', 'edit')->name('edit');
            Route::put('/clientes/editar/{id}', 'update')->name('update');
        });
    });

    Route::controller(ProposalController::class)->group(function () {
        Route::name('proposal.')->group(function () {

            Route::get('/propostas', 'index')->name('index');
            Route::get('/propostas/nova', 'create')->name('create');
            Route::post('/propostas/nova', 'store')->name('store');
            Route::get('/propostas/editar/{id}', 'edit')->name('edit');
            Route::put('/propostas/editar/{id}', 'update')->name('update');

            Route::get('/propostas/manual/nova', 'manual')->name('manual.create');
            Route::post('/propostas/manual/nova', 'manualStore')->name('manual.store');
        });
    });

    Route::get('/citiesByState/{id}', [CityController::class, 'citiesByState']);

});

require __DIR__.'/auth.php';
