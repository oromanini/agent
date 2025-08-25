<?php

namespace App\Http\Controllers;

use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\Kit;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class KitsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Kit::query();

        // ... (sua lógica de filtro aqui) ...

        // Ordenação dinâmica
        $sortBy = $request->input('sort_by', 'kwp'); // Padrão: 'kwp'
        $sortDirection = $request->input('sort_direction', 'asc'); // Padrão: 'asc'

        // Evita ordenação em colunas inválidas
        $allowedColumns = ['id', 'description', 'kwp', 'cost'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'kwp';
        }

        $query->orderBy($sortBy, $sortDirection);

        $kits = $query->paginate(20);

        return view('kits.index', compact('kits', 'sortBy', 'sortDirection'));
    }
}
