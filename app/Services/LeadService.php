<?php

namespace App\Services;

use App\Enums\PaymentTypeEnum;
use App\Http\Controllers\ProposalController;
use App\Models\Kit;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class LeadService implements BaseService
{
    public function __construct(private readonly ProposalController $proposalController)
    {}

    private const INITIAL_STATUS = 'Tratativa inicial';

    public function delete(int $id): array
    {
        $lead = Lead::find($id);
        $lead->delete();

        return ['success' => 'deletado com sucesso!'];
    }

    public function store(array $data): array
    {
        $lead = $this->fillObject($data);

        DB::transaction(function () use ($lead) {
            $lead->save();
        });

        return ['success' => 'Lead criado com sucesso!'];
    }

    public function update($id, $data): array
    {
        // TODO: Implement update() method.
    }

    public function fillObject(array $data, ?object $incidence = null): object
    {
        $lead = new Lead();

        $lead->average_consumption = $data['average_consumption'];
        $lead->kwh_price = $data['kwh_price'];
        $lead->tension_pattern = $data['tension_pattern'];
        $lead->phone_number = $data['phone_number'];
        $lead->city_id = $data['city'];
        $lead->roof_structure = $data['roof_structure'];
        $lead->name = $data['lead_name'];
        $lead->uuid = Uuid::uuid4();
        $lead->status = self::INITIAL_STATUS;

        $kit = Kit::query()
            ->where('distributor_code', $data['kit_id'])
            ->first();

        $lead->kit_data = json_encode($kit->attributesToArray());
        $lead->discount_data = json_encode([]);
        $lead->manual_data = json_encode([]);
        $lead->user_id = auth()->user()->id;

        $lead->pricing_data = $this->fillKitPricing($lead);

        return $lead;
    }

    private function fillKitPricing(Lead $lead): string
    {
        $leadKitData = $lead->kit();

        $kit = Kit::query()->where('distributor_code', $leadKitData['distributor_code'])->first();
        $kitSpecs = $kit->kitSpecs();
        $panelCount = $kit->kwp / ($kitSpecs['panel']['power'] / 1000);

        $finalPrice = (new PricingService())
            ->calculateFinalPrice(
                cost: $kit->cost,
                kwp: $kit->kwp,
                panelCount: $panelCount,
                panelBrand: $kitSpecs['panel']['brand'],
                panelPower: $kitSpecs['panel']['power'],
                inverterBrand: $kitSpecs['inverter']['brand'],
                roofStructure: $lead->roof_structure,
                finalValue: $kit->cost * 1.6,
                paymentType: PaymentTypeEnum::FINANCING,
                isLead: true
            );

        return json_encode([
            'kit_cost' => $kit->cost,
            'final_price' => $finalPrice
        ]);
    }

    public function updateStatus(array $data): array
    {
        Lead::find($data['lead_id'])->update(['status' => $data['status']]);
        return ['success' => 'Status atualizado com sucesso!'];
    }

    public function getLeadPanelQuantity(Lead $lead): int
    {
        $kit = $lead->kit();
        $power = jsonToArray($kit['panel_specs'])['power'] / 1000;
        $kwp = $kit['kwp'];

        return $kwp / $power;
    }
}
