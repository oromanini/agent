<?php

namespace App\Http\Controllers;

use App\Http\Requests\SoollarProductsRequest;
use App\Packages\SoolarApiPackage\SoolarApiManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class EdeltecController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            Artisan::call('import:edeltec-kits');
            return response()->json([
                'status' => 200,
                'message' => ['OK' => "Kits atualizados com sucesso"]
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => 404,
                'message' => ['ERROR' => $exception->getMessage()]
            ]);
        }
    }
}
