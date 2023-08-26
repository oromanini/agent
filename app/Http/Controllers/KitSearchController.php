<?php

namespace App\Http\Controllers;

use App\Services\KitSearchService;
use Illuminate\Http\Request;

class KitSearchController extends Controller
{
    private $kitSearchService;

    public function __construct(KitSearchService $kitSearchService)
    {
        $this->kitSearchService = $kitSearchService;
    }

    public function kitsSearch($kwp, $roof, $tension): array
    {
        return $this->kitSearchService->kitSearch($kwp, $roof, $tension);
    }
}
