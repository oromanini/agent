<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FinancingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KitSearchController;
use App\Http\Controllers\PreInspectionController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValueHistoryController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::controller(FinancingController::class)->group(function (){
    Route::name('simulator.')->group(function () {
        Route::get('financing-simulator', 'show')->name('index');
        Route::get('financing-simulator/mfs', 'getMfs')->name('mfs');
    });
});

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::controller(ClientController::class)->group(function () {

        Route::name('client.')->group(function () {

            Route::get('/clientes', 'index')->name('index');
            Route::get('/clientes/novo', 'create')->name('create');
            Route::post('/clientes/novo', 'store')->name('store');
            Route::get('/clientes/editar/{id}', 'edit')->name('edit');
            Route::put('/clientes/editar/{id}', 'update')->name('update');
            Route::get('/clientes/inativar/{client_id}', 'delete')->name('delete');
        });
    });

    Route::controller(ProposalController::class)->group(function () {

        Route::name('proposal.')->group(function () {

            Route::get('/propostas', 'index')->name('index');
            Route::get('/propostas/nova', 'create')->name('create');
            Route::post('/propostas/nova', 'store')->name('store');
            Route::get('/propostas/visualizar/{id}', 'edit')->name('edit');
            Route::put('/propostas/editar/{id}', 'update')->name('update');
            Route::get('/propostas/inativar/{id}', 'delete')->name('delete');

            Route::get('/propostas/manual/nova', 'manual')->name('manual.create');
            Route::post('/propostas/manual/nova', 'manualStore')->name('manual.store');

            Route::get('propostas/{proposal_id}/pdf', 'generatePdf')->name('pdf');
            Route::get('propostas/{proposal_id}/{true}/pdf', 'generatePdf')->name('small-pdf');
            Route::get('propostas/{proposal_id}/aprovacao', 'approve')->name('approve');
        });
    });

    Route::controller(ValueHistoryController::class)->group(function () {

        Route::name('valueHistory.')->group(function () {
            Route::post('propostas/{id}/comissao', 'applyCommissionOrDiscount')->name('updatePrice');
        });
    });

    Route::controller(PreInspectionController::class)->group(function () {

        Route::put('propostas/{id}/previstoria', 'edit')->name('inspection.update');
    });

    Route::controller(AddressController::class)->group(function () {

        Route::name('address.')->group(function () {
            Route::post('/address/store/{clientId}', 'store')->name('store');
        });
    });

    Route::controller(UserController::class)->group(function () {

        Route::name('user.')->group(function () {

            Route::get('usuarios/', 'index')->name('index');
            Route::get('usuarios/novo', 'create')->name('create');
            Route::post('usuarios/novo', 'store')->name('store');
            Route::get('usuarios/{id}', 'edit')->name('edit');
            Route::put('usuarios/{id}', 'edit')->name('update');
            Route::post('usuarios/{id}/inativar', 'inactive')->name('inactive');
        });
    });

    Route::controller(ApprovalController::class)->group(function () {

        Route::name('approval.')->group(function () {

            Route::get('aprovacoes', 'index')->name('index');
            Route::get('aprovacoes/{id}', 'show')->name('show');
            Route::put('aprovacoes/{id}/vistoria', 'updateInspection')->name('update.inspection');
            Route::put('aprovacoes/{id}/financiamento', 'updateFinancing')->name('update.financing');
            Route::put('aprovacoes/{id}/contrato', 'updateContract')->name('update.contract');
        });
    });

    Route::get('/citiesByState/{id}', [CityController::class, 'citiesByState']);
    Route::get('/getCityAndStateByNameAndUf/{name}/{uf}', [CityController::class, 'citiesByNameAndUf']);
    Route::get('/addressesFromClientId/{id}', [ClientController::class, 'addressesFromClientId']);
    Route::get('/ucsFromClientId/{id}', [ClientController::class, 'ucsFromClientId']);
    Route::get('/incidenceFromAddressId/{id}', [ClientController::class, 'incidenceFromAddress']);
    Route::get('/incidenceByClientId/{id}', [ClientController::class, 'IncidenceByClientId']);
    Route::get('/kitSearch/{kwp}/{roof}/{tension}', [KitSearchController::class, 'kitsSearch']);
    Route::post('/setFinalValue', [ProposalController::class, 'setFinalValue']);
    Route::post('/setAverageProduction', [ProposalController::class, 'setAverageProduction']);


});

require __DIR__ . '/auth.php';
