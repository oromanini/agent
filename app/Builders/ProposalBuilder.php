<?php

namespace App\Builders;

use App\Models\Client;
use App\Models\PreInspection;
use App\Models\Proposal;
use App\Models\ProposalValueHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ProposalBuilder implements Builder
{
    private Proposal $proposal;

    public function __construct()
    {
        $this->proposal = new Proposal();
        $this->proposal->uuid = Uuid::uuid4();
        $this->proposal->send_date = null;
        $this->proposal->contract_id = null;
        $this->proposal->financing_id = null;
        $this->proposal->inspection_id = null;
    }

    public function withType(?string $type = 'normal'): static
    {
        $this->proposal->type = $type;
        return $this;
    }

    public function withEstimatedGeneration(?float $estimatedGeneration = 1000): static
    {
        $this->proposal->estimated_generation = $estimatedGeneration;
        return $this;
    }

    public function withAverageConsumption(?float $averageConsumption = 950): static
    {
        $this->proposal->average_consumption = $averageConsumption;
        return $this;
    }

    public function withTensionPattern(?string $tensionPattern = 'MONO-220V'): static
    {
        $this->proposal->tension_pattern = $tensionPattern;
        return $this;
    }

    public function withRoofStructure(?int $roofStructure = 1): static
    {
        $this->proposal->roof_structure = $roofStructure;
        return $this;
    }

    public function withPanelQuantity(?int $panelQuantity = 12): static
    {
        $this->proposal->number_of_panels = $panelQuantity;
        return $this;
    }

    public function withKwhPrice(?float $kwhPrice = 0.92): static
    {
        $this->proposal->kw_price = $kwhPrice;
        return $this;
    }

    public function withComponents(?array $components = []): static
    {
        $this->proposal->components = json_encode($components);
        return $this;
    }

    public function withClient(Client $client): static
    {
        $this->proposal->client_id = $client->id;
        return $this;
    }

    public function withAgent(User|Model $user): static
    {
        $this->proposal->agent_id = $user->id;
        return $this;
    }

    public function withKitUuid(?string $kitUuid = Uuid::MAX): static
    {
        $this->proposal->kit_uuid = $kitUuid;
        return $this;
    }

    public function withPreInspection(PreInspection $preInspection): static
    {
        $this->proposal->pre_inspection_id = $preInspection->id;
        return $this;
    }

    public function withValueHistory(ProposalValueHistory $valueHistory): static
    {
        $this->proposal->value_history_id = $valueHistory->id;

        return $this;
    }

    public function isManual(bool $isManual): static
    {
        $this->proposal->is_manual = $isManual;
        return $this;
    }

    public function withManualData(array|null $manualData): static
    {
        $this->proposal->manual_data = !is_null($manualData) ? json_encode($manualData) : json_encode([]);
        return $this;
    }

    public function withKwp(float $kwp): static
    {
        $this->proposal->kwp = $kwp;
        return $this;
    }

    public function withRoofOrientation(?array $roofOrientation = ['norte']): static
    {
        $this->proposal->roof_orientation = json_encode($roofOrientation);
        return $this;
    }

    public function withInspection(?int $inspection_id = null): static
    {
        $this->proposal->inspection_id = $inspection_id;
        return $this;
    }

    public function withFinancing(?int $financing_id = null): static
    {
        $this->proposal->financing_id = $financing_id;
        return $this;
    }

    public function withContract(?int $contract_id = null): static
    {
        $this->proposal->contract_id = $contract_id;
        return $this;
    }

    public function build(): Proposal
    {
        $this->proposal->save();
        return $this->proposal;
    }
}
