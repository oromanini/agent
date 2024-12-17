<?php

namespace App\Services;

use App\Enums\DistributorsEnum;
use App\Enums\TensionPattern;
use App\Exceptions\DistributorNotFoundException;
use App\Models\ActiveKit;
use App\Models\Kit;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class KitSearchService
{
    public function __construct(
        private readonly float  $kwp,
        private string $roof,
        private readonly int $tension
    ) {
        is_string($this->roof) && $this->roof = (int) $this->roof;
    }

    public function kitSearch(): array
    {
        $kits = [];

        foreach (DistributorsEnum::cases() as $distributor) {
            $kits[$distributor->value] = $this
                ->searchKitsByDistributor($distributor->value);
        }
        return $kits;
    }

    /** @throws Exception */
    private function searchKitsByDistributor(string $distributor): Collection
    {
        $compatibleKits = new Collection();

        $panels = $this->setPanelsByDistributor($distributor);
        $inverters = $this->setInvertersByDistributor($distributor);

        foreach ($inverters as $inverter) {
            foreach ($panels as $panel) {

                $combination = ActiveKit::query()
                    ->where('panel_brand', $panel->value)
                    ->where('inverter_brand', $inverter->value)
                    ->where('distributor', $distributor)
                    ->where('is_active', '=', 1)
                    ->first();

                $tensionPossibilities = TensionPattern::setTensionPossibilities(tension: $this->tension);
                if (!is_null($combination) && $combination->is_active) {

                    $kit = Kit::query()
                        ->where('is_active', true)
                        ->where('kwp', '>=', $this->kwp)
                        ->where('distributor_name', $distributor)
                        ->where('roof_structure', $this->roof)
                        ->whereIn('tension_pattern', $tensionPossibilities)
                        ->whereJsonContains('panel_specs->brand', $panel->value)
                        ->whereJsonContains('inverter_specs->brand', $inverter->value)
                        ->orderBy('kwp')
                        ->first();

                    !is_null($kit) && $compatibleKits->push($kit);
                }
            }
        }
        return $compatibleKits;
    }

    private function setPanelsByDistributor(string $distributor): array
    {
        return match ($distributor) {
            'EDELTEC' => \App\Packages\EdeltecApiPackage\Enums\PanelBrand::cases(),
            'ODEX' => \App\Services\Odex\PanelBrandEnum::cases(),
            'FOTUS' => \App\Services\Fotus\PanelBrandEnum::cases(),
            default => throw new DistributorNotFoundException('Distribuidor não encontrado!')
        };
    }

    private function setInvertersByDistributor(string $distributor): array
    {
        return match ($distributor) {
            'EDELTEC' => \App\Packages\EdeltecApiPackage\Enums\InverterBrand::cases(),
            'ODEX' => \App\Services\Odex\InverterBrandEnum::cases(),
            'FOTUS' => \App\Services\Fotus\InverterBrandEnum::cases(),
            default => throw new DistributorNotFoundException('Distribuidor não encontrado!')
        };
    }
}
