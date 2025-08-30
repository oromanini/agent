<?php

namespace App\Http\Controllers;

use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function log(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string',
            'context' => 'nullable|string',
        ]);

        $logMessage = "Erro no Frontend: " . $request->input('message');
        $context = $request->input('context', 'Nenhum contexto adicional.');

        Log::error($logMessage, ['details' => $context]);

        $hasProcessingImportRunning = SoollarImportHistory::query()
            ->where('status', SoollarImportHistory::STATUS_PROCESSING)
            ->exists();

        $hasProcessingImportRunning && SoollarImportHistory::finishProcess(status: SoollarImportHistory::STATUS_ERROR);

        return response()->json(['status' => 'logged']);
    }
}
