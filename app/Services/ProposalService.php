<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class ProposalService implements BaseService
{

    public function store($data): array
    {
        $proposal = $data;

        DB::transaction(function () use ($proposal){
            $proposal->save();
        });
        return ['success', 'Proposta cadastrada com sucesso!'];
    }

    public function update($id, $data): array
    {
        // TODO: Implement update() method.
    }

    public function delete($id): array
    {
        // TODO: Implement delete() method.
    }

    public function fillObject($data): object
    {
        // TODO: Implement fillObject() method.
    }

    public function calculateEstimatedGeneration($data, $incidence): array
    {
        $kwp = $data['kwp'];
        $generationLost = env('GENERATION_LOST');
        $ordinaryAverage = (float)str_replace(',', '.', $incidence->average);

        $months = [
            'jan' => $kwp * 30 * (((float)$incidence->jan / 1000) - $generationLost),
            'feb' => $kwp * 30 * (((float)$incidence->feb / 1000) - $generationLost),
            'mar' => $kwp * 30 * (((float)$incidence->mar / 1000) - $generationLost),
            'apr' => $kwp * 30 * (((float)$incidence->apr / 1000) - $generationLost),
            'may' => $kwp * 30 * (((float)$incidence->may / 1000) - $generationLost),
            'jun' => $kwp * 30 * (((float)$incidence->jun / 1000) - $generationLost),
            'jul' => $kwp * 30 * (((float)$incidence->jul / 1000) - $generationLost),
            'aug' => $kwp * 30 * (((float)$incidence->aug / 1000) - $generationLost),
            'sep' => $kwp * 30 * (((float)$incidence->sep / 1000) - $generationLost),
            'oct' => $kwp * 30 * (((float)$incidence->oct / 1000) - $generationLost),
            'nov' => $kwp * 30 * (((float)$incidence->nov / 1000) - $generationLost),
            'dec' => $kwp * 30 * (((float)$incidence->dec / 1000) - $generationLost),
        ];

        $sum = 0;

        foreach ($months as $key => $val) {
            $sum += $val;
        }

        return [
            'months' => $months,
            'average' => $sum / 12,
            'ordinaryAverage' => $kwp * 30 * ($ordinaryAverage - $generationLost)
        ];

    }
}
