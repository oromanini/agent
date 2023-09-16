<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Kit;
use App\Models\PreInspection;
use App\Models\Proposal;
use App\Models\SolarIncidence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class ProposalService
{
    private array $data;

    public function __construct(
        private readonly SolarIncidenceService $incidenceService,
        private readonly ProposalValueHistoryService $valueHistoryService
    ) {}

    public function store($data, bool $isManual = false): object
    {
        $this->data = $data;
        $incidence = $this->getIncidence($this->data['client']);

        $proposal = $this->fillObject(
            isManual: $isManual,
            incidence: $incidence,
        );
        $proposal->pre_inspection_id = $this->createPreInspection()->id;

        DB::transaction(function () use ($proposal) {
            $proposal->save();
        });

        return $proposal;
    }

    public function fillObject(
        bool $isManual,
        SolarIncidence $incidence
    ): object {
        $proposal = new Proposal();
        $proposal->uuid = Uuid::uuid6();

        $isManual
            ? $proposal = $this->fillManualProposal($proposal)
            : $proposal = $this->fillDefaultProposal($proposal);

        return $this->fillCommonFields($proposal, $isManual, $incidence);
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

        foreach ($months as $val) {
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

    private function createPreInspection(): PreInspection
    {
        $preInspection = new PreInspection();

        DB::transaction(function () use ($preInspection) {
            $preInspection->save();
        });

        return $preInspection;
    }

    private function fillManualProposal(Proposal $proposal): Proposal
    {
        $proposal->is_manual = true;
        $proposal->kit_uuid = Uuid::uuid6();
        $proposal->components = json_encode(explode(PHP_EOL, $this->data['components']));
        $proposal->client_id = (int)$this->data['client'];
        $proposal->agent_id = (int)$this->data['agent'];
        $proposal->type = 'normal';
        $proposal->kwp = (float) $this->data['kwp'];
        $proposal->number_of_panels = (float) $this->data['panel_quantity'];

        $proposal->manual_data = json_encode([
            'panel_brand' => $this->data['panel_brand'],
            'panel_model' => $this->data['panel_model'],
            'panel_power' => $this->data['panel_power'],
            'panel_warranty' => $this->data['panel_warranty'],
            'inverter_brand' => $this->data['inverter_brand'],
            'inverter_model' => $this->data['inverter_model'],
            'inverter_power' => $this->data['inverter_power'],
            'inverter_warranty' => $this->data['inverter_warranty'],
            'inverter_quantity' => $this->data['inverter_quantity']
        ]);

        return $proposal;
    }

    private function fillDefaultProposal(Proposal $proposal): Proposal
    {
        $kit = Kit::query()->where('distributor_code', $this->data['kit_id'])->first();
        $panel_power = json_decode($kit->panel_specs, true)['power'];

        $proposal->kwp = $kit->kwp;
        $proposal->is_manual = false;
        $proposal->agent_id = $this->data['agent'] ?? auth()->user()->id;
        $proposal->kit_uuid = $this->data['kit_id'];
        $proposal->manual_data = json_encode([]);
        $proposal->components = $kit->components;
        $proposal->type = 'normal';

        $this->data['cost'] = $kit->cost;
        $this->data['kwp'] = $kit->kwp;
        $this->data['panel_count'] = roundOrFloorDecimalNumber($kit->kwp / ($panel_power / 1000));

        $proposal->number_of_panels = $this->data['panel_count'];

        return $proposal;
    }

    private function setRoofOrientations(array $data): array
    {
        $roofOrientation = [];

        foreach ($data['orientation'] as $value) {
            $roofOrientation[] = $value;
        }

        return $roofOrientation;
    }

    private function fillCommonFields(
        Proposal $proposal,
        bool $isManual,
        SolarIncidence $incidence
    ): Proposal {
        $proposal->estimated_generation = $this->calculateEstimatedGeneration($proposal->kwp, $incidence)['average'];
        $proposal->average_consumption = (float) $this->data['average_consumption'];
        $proposal->tension_pattern = (int) $this->data['tension_pattern'];
        $proposal->roof_structure = (int) $this->data['roof_structure'];
        $proposal->kw_price = commaFloatToDotFloat($this->data['kw_price']);
        $proposal->client_id = (int) $this->data['client'];
        $proposal->roof_orientation = json_encode($this->setRoofOrientations($this->data));

        $proposal->value_history_id = $this->valueHistoryService->store($this->data, $isManual);

        return $proposal;
    }
}
