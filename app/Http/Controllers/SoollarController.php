<?php

namespace App\Http\Controllers;

use App\Jobs\SoollarKitsUpdateJob;
use App\Jobs\SoollarProductsUpdateJob;
use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Bus;

class SoollarController extends Controller
{
    public function index(): JsonResponse
    {
        SoollarImportHistory::initProcess();

        $jobsToChain = [];

        $jobsToChain[] = new SoollarProductsUpdateJob();
        $jobsToChain[] = new SoollarKitsUpdateJob();

        Bus::chain($jobsToChain)->dispatch();

        return response()->json([
            'status' => 200,
            'message' => 'O processo de atualização foi iniciado em segundo plano.'
        ], 200);
    }

    public function getUpdateStatus(): JsonResponse
    {
        $runningProcess = SoollarImportHistory::getProcessing()->first();

        if ($runningProcess) {
            return response()->json($runningProcess);
        }

        $lastProcess = SoollarImportHistory::latest('id')->first();

        if ($lastProcess) {
            return response()->json($lastProcess);
        }

        return response()->json([
            'status' => 'IDLE',
            'message' => 'Nenhum processo de atualização em andamento ou executado anteriormente.'
        ]);
    }
}
