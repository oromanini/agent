<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Client;
use App\Models\PreInspection;
use App\Models\Proposal;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class ProposalService
{
    private $incidenceService;
    private $valueHistoryService;

    public function __construct(SolarIncidenceService $incidenceService, ProposalValueHistoryService $valueHistoryService)
    {
        $this->incidenceService = $incidenceService;
        $this->valueHistoryService = $valueHistoryService;
    }

    public function store($data, bool $isManual = false): Proposal
    {
        $proposal = null;

        $address = Client::find($data['client'])->addresses()->first();
        $incidence = $this->incidenceService->getSolarIncidence($address->city);

        $proposal = $this->fillObject($data, $isManual, $incidence);

        $preInspection = new PreInspection();

        DB::transaction(function () use ($preInspection) {
            $preInspection->save();
        });

        $proposal->pre_inspection_id = $preInspection->id;

        DB::transaction(function () use ($proposal) {
            $proposal->save();
        });

        return $proposal;
    }

    public function fillObject(array $data, bool $isManual = false, ?object $incidence = null, $sumKits = null): object
    {
        $proposal = new Proposal();

        if ($isManual) {
            $proposal->is_manual = true;
            $proposal->kit_uuid = Uuid::uuid6();
            $proposal->components = json_encode(explode(PHP_EOL, $data['components']));
            $proposal->client_id = (int)$data['client'];
            $proposal->agent_id = (int)$data['agent'];

            $proposal->manual_data = json_encode([
                'panel_brand' => $data['panel_brand'],
                'panel_model' => $data['panel_model'],
                'panel_power' => $data['panel_power'],
                'panel_warranty' => $data['panel_warranty'],
                'inverter_brand' => $data['inverter_brand'],
                'inverter_model' => $data['inverter_model'],
                'inverter_power' => $data['inverter_power'],
                'inverter_warranty' => $data['inverter_warranty'],
                'inverter_quantity' => $data['inverter_quantity']
            ]);

        } else {
            $proposal->is_manual = false;
            $proposal->agent_id = auth()->user()->id;

            $uuids = $data['kit_id'];
            $sumKits = json_decode(Http::get(env('KITS_URL') . 'getInventoryKitsByCodes/' . $uuids)->body(), true);
            $proposal->kit_uuid = json_encode($uuids);
            $proposal->manual_data = json_encode([]);
            $proposal->components = json_encode($sumKits['components']);
        }

        $proposal->uuid = Uuid::uuid6();
        $proposal->type = 'normal';
        $proposal->kwp = $isManual ? (float)$data['kwp'] : (float)$sumKits['kwp'];
        $proposal->number_of_panels = $isManual ? (int)$data['panel_quantity'] : (float)$sumKits['panel_count'];

        $proposal->estimated_generation = $this->calculateEstimatedGeneration($proposal->kwp, $incidence)['average'];

        $proposal->average_consumption = (float)$data['average_consumption'];
        $proposal->tension_pattern = formatTension($data['tension_pattern']);
        $proposal->roof_structure = (int)$data['roof_structure'];
        $proposal->kw_price = (float)str_replace(',', '.', $data['kw_price']);
        $proposal->client_id = (int)$data['client'];
        $data['sumKits'] = $isManual ? null : $sumKits;
        $proposal->value_history_id = $this->valueHistoryService->store($data, $isManual);


        $roofOrientation = [];

        foreach ($data['orientation'] as $orientation => $value) {
            $roofOrientation[] = $value;
        }

        $proposal->roof_orientation = json_encode($roofOrientation);


        return $proposal;
    }

    public function calculateEstimatedGeneration($kwp, $incidence): array
    {
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
