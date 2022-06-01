<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Client;
use App\Models\PreInspection;
use App\Models\Proposal;
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

    public function store($data): Proposal
    {
        $client = Client::find($data['client']);
        $address = Address::find($data['installation_address']);
        $incidence = $this->incidenceService->getSolarIncidence($address->city);

        $uuids = $data['kit_id'];

        $sumKits = json_decode(Http::get(env('KITS_URL') . 'getInventoryKitsByCodes/' . $uuids)->body(), true);

        $proposal = new Proposal();
        $preInspection = new PreInspection();

        DB::transaction(function () use ($preInspection) {
            $preInspection->save();
        });

        $proposal->uuid = Uuid::uuid6();
        $proposal->type = 'normal';

        $proposal->kwp = (float)$sumKits['kwp'];
        $proposal->number_of_panels = (float)$sumKits['panel_count'];
        $proposal->components = json_encode($sumKits['components']);

        $data['kwp'] = (float)$sumKits['kwp'];
        $data['cost'] = (float)$sumKits['cost'];
        $data['panel_count'] = (float)$sumKits['panel_count'];

        $proposal->estimated_generation = $this->calculateEstimatedGeneration($data, $incidence)['average'];
        $proposal->average_consumption = (float)$data['average_consumption'];
        $proposal->tension_pattern = formatTension($data['tension_pattern']);
        $proposal->roof_structure = (int)$data['roof_structure'];
        $proposal->kw_price = (float)str_replace(',', '.', $data['kw_price']);
        $proposal->client_id = (int)$data['client'];
        $proposal->agent_id = auth()->user()->id;
        $proposal->kit_uuid = json_encode($uuids);
        $proposal->pre_inspection_id = $preInspection->id;
        $proposal->is_manual = false;
        $proposal->roof_orientations = json_encode($data['orientation']);
        $proposal->manual_data = json_encode([]);

        $proposal->value_history_id = $this->valueHistoryService->store($data, false);

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
        if (isset($data['kwp'])) {
            $kwp = $data['kwp'];
        } else {
            $kwp = $data['kit']['kwp'];
        }


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
