<?php

namespace App\Services;

use App\Models\Client;
use App\Models\PreInspection;
use App\Models\Proposal;
use App\Models\SolarIncidence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class ProposalService
{
    private SolarIncidenceService $incidenceService;
    private ProposalValueHistoryService $valueHistoryService;

    public function __construct(
        SolarIncidenceService       $incidenceService,
        ProposalValueHistoryService $valueHistoryService
    ) {
        $this->incidenceService = $incidenceService;
        $this->valueHistoryService = $valueHistoryService;
    }

    public function store($data, bool $isManual = false): object
    {
        dd($data);

        $incidence = $this->getIncidence($data['client']);

        $proposal = $this->fillObject(
            data: $data,
            isManual: $isManual,
            incidence: $incidence,
        );

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

    public function fillObject(
        array $data,
        bool $isManual,
        SolarIncidence $incidence
    ): object {
        $proposal = new Proposal();
        $kit = null;

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
            $proposal->agent_id = $data['agent'] ?? auth()->user()->id;

            $uuids = $data['kit_id'];
            $sumKits = json_decode(Http::get(env('KITS_URL') . 'getInventoryKitsByCodes/' . $uuids)->body(), true);
            $proposal->kit_uuid = json_encode($uuids);
            $proposal->manual_data = json_encode([]);
            $proposal->components = json_encode($sumKits['components']);
            $kit = kitByUuid(getKitCodesFromProposal($proposal)[0])['technical_description'];
            $data['sumKits'] = $isManual ? null : $sumKits;
            $data['panelBrand'] = $kit['panel_specs']['panel_brand'];
            $data['panelPower'] = $kit['panel_specs']['panel_power'];
            $data['inverterBrand'] = $kit['inverter_brand'];
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

        $proposal->value_history_id = $this->valueHistoryService->store($data, $isManual);

        $roofOrientation = [];

        foreach ($data['orientation'] as $value) {
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
            'jan' => $this->setGeneration(month: 'jan', kwp: $kwp, incidence: $incidence, generationLost: $generationLost),
            'feb' => $this->setGeneration(month: 'feb', kwp: $kwp, incidence: $incidence, generationLost: $generationLost),
            'mar' => $this->setGeneration(month: 'mar', kwp: $kwp, incidence: $incidence, generationLost: $generationLost),
            'apr' => $this->setGeneration(month: 'apr', kwp: $kwp, incidence: $incidence, generationLost: $generationLost),
            'may' => $this->setGeneration(month: 'may', kwp: $kwp, incidence: $incidence, generationLost: $generationLost),
            'jun' => $this->setGeneration(month: 'jun', kwp: $kwp, incidence: $incidence, generationLost: $generationLost),
            'jul' => $this->setGeneration(month: 'jul', kwp: $kwp, incidence: $incidence, generationLost: $generationLost),
            'aug' => $this->setGeneration(month: 'aug', kwp: $kwp, incidence: $incidence, generationLost: $generationLost),
            'sep' => $this->setGeneration(month: 'sep', kwp: $kwp, incidence: $incidence, generationLost: $generationLost),
            'oct' => $this->setGeneration(month: 'oct', kwp: $kwp, incidence: $incidence, generationLost: $generationLost),
            'nov' => $this->setGeneration(month: 'nov', kwp: $kwp, incidence: $incidence, generationLost: $generationLost),
            'dec' => $this->setGeneration(month: 'dec', kwp: $kwp, incidence: $incidence, generationLost: $generationLost),
        ];

        $sum = 0;

        foreach ($months as $key => $val) {
            $sum += $val;
        }

        return [
            'months' => $months,
            'average' => $sum / 12,
            'ordinaryAverage' => ($kwp / (1 + $generationLost)) * 30 * $ordinaryAverage
        ];
    }

    private function setGeneration(
        string $month,
        float $kwp,
        object $incidence,
        float $generationLost
    ): float {

        return
            ($kwp / (1 + $generationLost))
            * 30
            * ((float)$incidence->{$month} / 1000);
    }

    private function getIncidence(int $client): SolarIncidence
    {
        $address = Client::find($client)
            ->addresses()
            ->first();

        return $this->incidenceService->getSolarIncidence($address->city);
    }
}
