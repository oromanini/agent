<?php

namespace App\Http\Controllers;

use App\Models\ActiveKit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActiveKitController extends Controller
{
    public function index(): View
    {
        $kits = ActiveKit::query()->orderBy('is_active', 'desc')->get();
        return view('combinations.index', compact('kits'));
    }

    public function create(): View
    {
        return view('kits.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'panel_brand' => 'required|string|max:255',
            'inverter_brand' => 'required|string|max:255',
            'distributor' => 'required|string|max:255',
        ]);

        $validatedData['last_updated_time'] = now();

        try {
            $newKit = ActiveKit::create($validatedData);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return response()->json([
            'id' => $newKit->id,
            'panel_brand' => $newKit->panel_brand,
            'inverter_brand' => $newKit->inverter_brand,
            'distributor' => $newKit->distributor,
            'is_active' => $newKit->is_active,
            'last_updated_time' => now()->toDateString(),
        ], 201);
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

    public function toggleActive(ActiveKit $activeKit): JsonResponse
    {
        $activeKit->is_active = !$activeKit->is_active;
        $activeKit->save();

        return response()->json(['success' => true, 'is_active' => $activeKit->is_active]);
    }
}
