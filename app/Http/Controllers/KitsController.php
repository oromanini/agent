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

        if ($request->filled('min_kwp') && is_numeric($request->input('min_kwp'))) {
            $query->where('kwp', '>=', $request->input('min_kwp'));
        }

        if ($request->filled('max_kwp') && is_numeric($request->input('max_kwp'))) {
            $query->where('kwp', '<=', $request->input('max_kwp'));
        }

        if ($request->filled('roof_structure')) {
            $query->where('roof_structure', $request->input('roof_structure'));
        }

        if ($request->filled('tension_pattern')) {
            $query->where('tension_pattern', $request->input('tension_pattern'));
        }

        $sortBy = $request->input('sort_by', 'kwp');
        $sortDirection = $request->input('sort_direction', 'asc');

        $allowedColumns = ['id', 'description', 'kwp', 'cost'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'kwp';
        }

        $query->orderBy($sortBy, $sortDirection);

        $kits = $query->paginate(20)->withQueryString();

        return view('kits.index', compact('kits', 'sortBy', 'sortDirection'));
    }
}
