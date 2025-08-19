<?php

namespace App\Http\Controllers;

use App\Enums\WorkCostClassificationEnum;
use App\Http\Requests\StoreWorkCostRequest;
use App\Models\WorkCost;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WorkCostController extends Controller
{
    public function index()
    {
        $workCosts = WorkCost::orderBy('classification')->get()->map(function ($workCost) {
            $workCost->classification_name = WorkCostClassificationEnum::classificateByEnum($workCost->classification);

            $costsArray = $this->getCostsAsArray($workCost);

            $workCost->costs = $this->formatCostsForDisplay($costsArray);
            return $workCost;
        });

        return view('work_costs.index', compact('workCosts'));
    }

    public function edit(WorkCost $workCost)
    {
        $workCost->classification_name = WorkCostClassificationEnum::classificateByEnum($workCost->classification);

        $costsArray = $this->getCostsAsArray($workCost);
        $formattedCosts = $this->formatCostsForDisplay($costsArray);

        $workCost->costs = json_encode($formattedCosts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return view('work_costs.form', compact('workCost'));
    }

    public function update(StoreWorkCostRequest $request, WorkCost $workCost)
    {
        $validatedData = $request->validated();

        $costsArrayFromRequest = json_decode($validatedData['costs'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['costs' => 'O formato do JSON de custos é inválido.'])->withInput();
        }

        $costsToStore = $this->parseCostsForStorage($costsArrayFromRequest);

        $currentHistory = $workCost->change_history ?? [];
        $newHistoryEntry = [
            'user_id' => auth()->id() ?? 'system',
            'date' => Carbon::now()->toDateTimeString(),
            'action' => 'updated',
            'previous_costs' => $this->getCostsAsArray($workCost)
        ];
        $updatedHistory = array_merge([$currentHistory], [$newHistoryEntry]);

        $workCost->update([
            'classification' => $validatedData['classification'],
            'costs' => $costsToStore,
            'change_history' => $updatedHistory,
        ]);

        return redirect()->route('work_costs.index')->with('success', 'Custo atualizado com sucesso!');
    }

    private function getCostsAsArray(WorkCost $workCost): array
    {
        $costs = $workCost->costs;
        // Se for uma string (JSON duplo), decodifica. Se já for array, usa diretamente.
        if (is_string($costs)) {
            return json_decode($costs, true) ?? [];
        }
        return $costs ?? [];
    }

    private function formatCostsForDisplay(array $costs): array
    {
        $formattedCosts = [];
        foreach ($costs as $key => $value) {
            if ((str_contains($key, '_percentage') || str_contains($key, '_fee')) && is_numeric($value)) {
                $formattedCosts[$key] = rtrim(rtrim(number_format($value * 100, 2, ',', '.'), '0'), ',') . '%';
            } else {
                $formattedCosts[$key] = $value;
            }
        }
        return $formattedCosts;
    }

    private function parseCostsForStorage(array $costsArray): array
    {
        $parsedCosts = [];
        foreach ($costsArray as $key => $value) {
            if ((str_contains($key, '_percentage') || str_contains($key, '_fee')) && is_string($value) && str_ends_with($value, '%')) {
                $numericValue = str_replace(['.', ','], ['', '.'], rtrim($value, '%'));
                $parsedCosts[$key] = (float) $numericValue / 100;
            } else {
                $parsedCosts[$key] = $value;
            }
        }
        return $parsedCosts;
    }
}
