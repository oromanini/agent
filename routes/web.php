<?php

use App\Http\Controllers\ActiveKitController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FinancingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomologationController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\KitSearchController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PreInspectionController;
use App\Http\Controllers\ProductsUpdateController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValueHistoryController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::controller(FinancingController::class)->group(function () {
    Route::name('simulator.')->group(function () {
        Route::get('financing-simulator', 'show')->name('index');
        Route::get('financing-simulator/mfs', 'getMfs')->name('mfs');
    });
});

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/login', function () {
        return view('home');
    });

    Route::get('/dashboard', function () {
        return view('home');
    });

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
            Route::get('/propostas/inativar/{id}', 'delete')->name('delete');

            Route::get('/propostas/atualizar/{id}', 'editExistentProposal')->name('editExistentProposal');
            Route::post('/propostas/atualizar/{id}', 'update')->name('updateExistentProposal');

            Route::get('/propostas/manual/nova', 'manual')->name('manual.create');
            Route::post('/propostas/manual/nova', 'manualStore')->name('manual.store');

            Route::get('propostas/{proposal_id}/pdf', 'generatePdf')->name('pdf');
            Route::get('propostas/{proposal_id}/{true}/pdf', 'generatePdf')->name('small-pdf');
            Route::get('propostas/{proposal_id}/aprovacao', 'approve')->name('approve');
            Route::get('leads/generate-pdf/{id}', 'generateLeadPdf')->name('leadpdf');
        });
    });

    Route::controller(LeadController::class)->group(function () {
        Route::name('leads.')->group(function () {
            Route::get('leads/propostas', 'index')->name('index');
            Route::get('leads/nova-proposta', 'create')->name('create');
            Route::post('leads/nova-proposta', 'store')->name('store');
            Route::get('leads/{id}', 'show')->name('show');
            Route::delete('leads/{id}', 'delete')->name('delete');
            Route::get('incidenceFromCity/{id}', 'incidenceFromCity')->name('incidenceFromCity');
            Route::put('update-lead-status', 'updateLeadStatus')->name('status');
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
            Route::put('usuarios/{id}', 'update')->name('update');
            Route::get('usuarios/{id}/inativar', 'inactive')->name('inactive');
        });
    });

    Route::controller(ApprovalController::class)->group(function () {

        Route::name('approval.')->group(function () {

            Route::get('aprovacoes', 'index')->name('index');
            Route::get('aprovacoes/{id}', 'show')->name('show');
            Route::put('aprovacoes/{id}/vistoria', 'updateInspection')->name('update.inspection');
            Route::put('aprovacoes/{id}/financiamento', 'updateFinancing')->name('update.financing');
            Route::put('aprovacoes/{id}/contrato', 'updateContract')->name('update.contract');
            Route::get('aprovacoes/{id}/inativar', 'inactive')->name('inactive');
        });
    });

    Route::controller(HomologationController::class)->group(function () {

        Route::name('homologation.')->group(function () {
            Route::get('homologacoes', 'index')->name('index');
            Route::get('homologacoes/{id}', 'show')->name('show');
            Route::put('homologacoes/{id}/atualizar', 'update')->name('update');
            Route::get('homologacoes/{id}/inativar', 'inactive')->name('inactive');
        });
    });

    Route::controller(InstallationController::class)->group(function () {

        Route::name('installation.')->group(function () {

            Route::get('instalacoes', 'index')->name('index');
            Route::get('instalacoes/{id}', 'show')->name('show');
            Route::put('instalacoes/{id}/atualizar', 'update')->name('update');
            Route::get('instalacoes/{id}/inativar', 'inactive')->name('inactive');
            Route::post('instalacoes/{id}/novoCustoAdicional', 'addPlusCosts')->name('addPlusCosts');
            Route::get('instalacoes/{id}/deletarCustoAdicional', 'deletePlusCost')->name('deletePlusCost');
            Route::post('instalacoes/{id}/fotos', 'updatePictures')->name('updatePictures');

        });
    });

    Route::controller(ProductsUpdateController::class)->group(function () {

        Route::name('update_products.')->group(function () {
            Route::get('/atualizar_kits', 'index')->name('index');
        });
    });

    Route::resource('active-kits', ActiveKitController::class);
    Route::put('active-kits/{activeKit}/toggle-active', [ActiveKitController::class, 'toggleActive'])->name('active-kits.toggleActive');

    Route::get('/citiesByState/{id}', [CityController::class, 'citiesByState']);
    Route::get('/getCityAndStateByNameAndUf/{name}/{uf}', [CityController::class, 'citiesByNameAndUf']);
    Route::get('/addressesFromClientId/{id}', [ClientController::class, 'addressesFromClientId']);
    Route::get('/ucsFromClientId/{id}', [ClientController::class, 'ucsFromClientId']);
    Route::get('/incidenceFromAddressId/{id}', [ClientController::class, 'incidenceFromAddress']);
    Route::get('/incidenceByClientId/{id}', [ClientController::class, 'IncidenceByClientId']);
    Route::get('/kitSearch/{kwp}/{roof}/{tension}', [KitSearchController::class, 'kitsSearch']);
    Route::post('/setFinalValue', [ProposalController::class, 'setFinalValue']);
    Route::post('/setAverageProduction', [ProposalController::class, 'setAverageProduction']);
    Route::post('/setAverageProductionByCity', [LeadController::class, 'setAverageProductionByCity']);
    Route::post('/get-tension-by-value', [ProposalController::class, 'setTensionByValue']);

});

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

require __DIR__ . '/auth.php';
