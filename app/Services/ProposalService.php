<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ProposalService
{

    public function store($data): Proposal
    {
        $proposal = new Proposal();
        $proposal->uuid = Uuid::uuid6();


        DB::transaction(function () use ($proposal) {
            $proposal->save();
        });
        return $proposal;
    }

    public function fillObject(array $data, ?object $incidence = null): object
    {
        $proposal = new Proposal();
        $proposal->is_manual = true;

        $proposal->uuid = Uuid::uuid6();
        $proposal->kit_uuid = Uuid::uuid6();

        $proposal->type = 'normal';
        $proposal->estimated_generation = $this->calculateEstimatedGeneration($data, $incidence)['average'];
        $proposal->average_consumption = (float)$data['average_consumption'];
        $proposal->tension_pattern = formatTension($data['tension_pattern']);
        $proposal->roof_structure = (int)$data['tension_pattern'];
        $proposal->number_of_panels = (int)$data['panel_quantity'];
        $proposal->kw_price = stringMoneyToFloat($data['kw_price']);
        $proposal->components = json_encode(explode(PHP_EOL, $data['components']));
        $proposal->client_id = (int)$data['client'];
        $proposal->agent_id = (int)$data['agent'];
        $proposal->kwp = (float)$data['kwp'];

        $roofOrientation = [];

        foreach ($data['orientation'] as $orientation => $value) {
            $roofOrientation[] = $value;
        }

        $proposal->roof_orientation = json_encode($roofOrientation);

        $proposal->manual_data = json_encode([
            'panel_brand' => $data['panel_brand'],
            'panel_model' => $data['panel_model'],
            'panel_power' => $data['panel_power'],
            'panel_warranty' => $data['panel_warranty'],
            'inverter_brand' => $data['inverter_brand'],
            'inverter_model' => $data['inverter_model'],
            'inverter_power' => $data['inverter_power'],
            'inverter_warranty' => $data['inverter_warranty'],
        ]);

        return $proposal;
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
