<?php

namespace App\Http\Controllers;

use App\Jobs\StartSoollarUpdateJob;
use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use Illuminate\Http\JsonResponse;

class SoollarController extends Controller
{
    public function index(): JsonResponse
    {
        StartSoollarUpdateJob::dispatch();

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
