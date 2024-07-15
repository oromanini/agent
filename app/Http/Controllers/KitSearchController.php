<?php

namespace App\Http\Controllers;

use App\Services\KitSearchService;
use Illuminate\Http\JsonResponse;

class KitSearchController extends Controller
{

    public function kitsSearch(float $kwp, int $roof, string $tension): JsonResponse
    {
        return response()->json(
            (new KitSearchService($kwp, $roof, $tension))->kitSearch()
        );
    }
}
