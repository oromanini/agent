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
        foreach (ProductCategoriesEnum::cases() as $category) {
            foreach (WarehouseEnum::cases() as $warehouse) {
                $jobsToChain[] = new SoollarProductsUpdateJob($category, $warehouse);
            }
        }

        $jobsToChain[] = new SoollarKitsUpdateJob();

        Bus::chain($jobsToChain)->dispatch();

        return response()->json([
            'status' => 200,
            'message' => 'O processo de atualização foi iniciado em segundo plano.'
        ], 200);
    }
}
