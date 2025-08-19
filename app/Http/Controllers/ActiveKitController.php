<?php

namespace App\Http\Controllers;

use App\Models\ActiveKit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActiveKitController extends Controller
{
    public function index(): View
    {
        $kits = ActiveKit::all();
        return view('combinations.index', compact('kits'));
    }

    public function create(): View
    {
        return view('kits.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'panel_brand' => 'required|string|max:255',
            'inverter_brand' => 'required|string|max:255',
            'distributor' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        ActiveKit::create($validatedData);

        return redirect()->route('active-kits.index')->with('success', 'Combinação criada com sucesso!');
    }

    public function update(Request $request, ActiveKit $activeKit)
    {
        $validatedData = $request->validate([
            'panel_brand' => 'required|string|max:255',
            'inverter_brand' => 'required|string|max:255',
            'distributor' => 'required|string|max:255',
        ]);

        $activeKit->update($validatedData);

        return response()->json(['success' => true, 'message' => 'Combinação atualizada com sucesso!']);
    }

    public function destroy(ActiveKit $activeKit): RedirectResponse
    {
        $activeKit->delete();

        return redirect()->route('active-kits.index')->with('success', 'Combinação excluída com sucesso!');
    }

    public function toggleActive(ActiveKit $activeKit): RedirectResponse
    {
        $activeKit->is_active = !$activeKit->is_active;
        $activeKit->save();

        return redirect()->route('active-kits.index')->with('success', 'Status da combinação atualizado.');
    }
}
