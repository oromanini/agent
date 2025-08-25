<?php

namespace App\Packages\SoolarApiPackage;

use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\Kit;
use App\Packages\SoolarApiPackage\Contracts\KitsManagerInterface;
use App\Packages\SoolarApiPackage\Models\Inverter;
use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\Module;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;
use App\Packages\SoolarApiPackage\Services\CableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class KitsManager implements KitsManagerInterface
{
    const MINIMUM_OVERLOAD = 0.8;
    const PRETO = 'PRETO';
    const VERMELHO = 'VERMELHO';

    public function __construct(
        private readonly SoollarApiRepository $repository,
        private readonly CableService $cableService
    ) {}

    function handle(): int
    {
        $this->repository->deactivateAllKits();
        $moduleBands = $this->repository->getModuleBrands();
        $inverterBands = $this->repository->getInverterBrands();

        foreach ($moduleBands as $moduleBand) {
            foreach ($inverterBands as $inverterBand) {

                $modules = $this->repository->findModulesByBrand($moduleBand);
                $inverters = $this->repository->findInvertersByBrand($inverterBand);

                foreach ($modules as $module) {
                    foreach ($inverters as $inverter) {

                        $overload = $this->getInverterOverload($inverterBand);
                        $minimumPanels = $this->getInverterMinimumPanels($inverter, $module);
                        $maximumPanels = $this->getInverterMaximumPanels($inverter, $module, $overload);

                        for (
                            $modulesQuantity = $minimumPanels;
                            $modulesQuantity <= $maximumPanels;
                            $modulesQuantity++
                        ) {
                            $this->mountAndSaveKit(
                                modulesQuantity: $modulesQuantity,
                                module: $module,
                                inverter: $inverter,
                            );
                        }
                    }
                }
            }
        }

        $total = $this->repository->countActiveKits();

        return $total;
    }

    private function mountAndSaveKit(
        int $modulesQuantity,
        Module $module,
        Inverter $inverter
    ): void {
        foreach (RoofStructure::cases() as $roofStructure) {
            try {
                $panelSpecs = $this->setPanelSpecs($module);
                $inverterSpecs = $this->setInverterSpecs($inverter);
                $structureSpecs = $this->setStructureSpecs($roofStructure, $modulesQuantity);
                $cables = $this->calculateCable($modulesQuantity);
                $connectors = $this->calculateConnectors($modulesQuantity);
                $kwp = $this->calculateKwp($modulesQuantity, (float)$module->power);

                $components = $this->setComponents(
                    $module,
                    $inverter,
                    $structureSpecs,
                    $modulesQuantity,
                    $cables,
                    $connectors
                );

                $cost = $this->calculateCost($module, $modulesQuantity, $inverter, $structureSpecs, $cables, $connectors);

                $tensionPattern = match ((string)$inverter->voltage) {
                    '220V' => TensionPattern::MONOFASICO_220V->value,
                    '380V' => TensionPattern::TRIFASICO_380V->value,
                    default => TensionPattern::MONOFASICO_220V->value,
                };

                $kit = new Kit();
                $kit->fillFromAttributes(
                    description: $this->setKitDescription($kwp, $roofStructure, $inverter, $panelSpecs),
                    kwp: $kwp,
                    cost: $cost,
                    roof_structure: $roofStructure->value,
                    tension_pattern: $tensionPattern,
                    components: $components,
                    panel_specs: $panelSpecs,
                    inverter_specs: $inverterSpecs,
                    distributor_name: "SOOLLAR",
                    distributor_code: Uuid::uuid4()->toString(),
                    availability: now()->toDateString(),
                    is_active: true,
                );

                $this->saveOrUpdateKit($kit);
            } catch (\Throwable $e) {
                Log::error('Erro ao montar kit: ' . $e->getMessage() . ' - Inversor: ' . $inverter->id . ' Módulo: ' . $module->id);
            }
        }
    }

    public function saveOrUpdateKit(Kit $kit): void
    {
        $existingKit = $this->repository->getKitByDescription($kit->description);

        if ($existingKit) {
            $existingKit->update($kit->toArray());
            $existingKit->update(['is_active' => true]);
            return;
        }

        $kit->save();
    }

    private function setKitDescription(float $kwp, RoofStructure $roofStructure, Inverter $inverter, array $panelSpecs): string
    {
        $formattedKwp = number_format($kwp, 2, ',', '.');
        return "Kit {$formattedKwp} kWP {$roofStructure->name} {$inverter->brand} {$inverter->power}KW e {$panelSpecs['brand']} {$panelSpecs['power']}W";
    }

    private function calculateKwp(int $modulesQuantity, float $panelPower): float
    {
        return $modulesQuantity * ($panelPower / 1000);
    }

    private function setComponents(
        Module $module,
        Inverter $inverter,
        array $structureSpecs,
        int $modulesQuantity,
        array $cables,
        array $connectors
    ): array {
        return [
            "{$modulesQuantity} módulos {$module->brand} {$module->power}W",
            "1 inversor {$inverter->brand} {$inverter->voltage}",
            $this->getStructureDescription($structureSpecs),
            $this->getStructureProfileDescription($structureSpecs),
            ...$cables['description'],
            "{$connectors['quantity']} par - {$connectors['connectors']->name}",
        ];
    }

    public function getStructureDescription(array $structureSpecs): string
    {
        return "{$structureSpecs['description']} - {$structureSpecs['components']['structures']['quantity']} UND - {$structureSpecs['components']['structures']['name']}";
    }

    public function getStructureProfileDescription(array $structureSpecs): string
    {
        if (!isset($structureSpecs['components']['profiles'])) {
            return '';
        }
        return "{$structureSpecs['components']['profiles']['quantity']} UND - {$structureSpecs['components']['profiles']['name']} ";
    }

    private function getInverterOverload(InverterBrand $inverterBand): float
    {
        $overload = $inverterBand->overload ?? 0.50;

        return max(self::MINIMUM_OVERLOAD, $overload);
    }

    private function getInverterMinimumPanels(Inverter $inverter, Module $module): int
    {
        $inverterPower = (float) $inverter->power;
        $moduleKw = (float) $module->power / 1000;
        return (int) ceil(($inverterPower * self::MINIMUM_OVERLOAD) / $moduleKw);
    }

    private function getInverterMaximumPanels(Inverter $inverter, Module $module, float $overload): int
    {
        $inverterPower = (float) $inverter->power;
        $overload = 1 + ($overload / 100);
        $moduleKw = (float) $module->power / 1000;

        return (int) floor(
            ($inverterPower * $overload)
            / $moduleKw
        );
    }

    private function setPanelSpecs(Module $module): array
    {
        return [
            'id' => $module->id,
            'name' => $module->name,
            'brand' => $module->brand,
            'power' => (float)$module->power,
            'category' => $module->category,
            'stock' => $module->stock,
            'price' => $module->price
        ];
    }

    private function setInverterSpecs(Inverter $inverter): array
    {
        return [
            'id' => $inverter->id,
            'name' => $inverter->name,
            'brand' => $inverter->brand,
            'power' => (float)$inverter->power,
            'voltage' => $inverter->voltage,
            'category' => $inverter->category,
            'stock' => $inverter->stock,
            'price' => $inverter->price
        ];
    }

    private function setStructureSpecs(RoofStructure $roofStructure, int $modulesQuantity): array
    {
        $structuresQuantity = ceil($modulesQuantity / 4);

        $structure = match ($roofStructure) {
            RoofStructure::METALICO => $this->repository->getStructureByModelName('mini-trilho'),
            RoofStructure::COLONIAL => $this->repository->getStructureByModelName('cerâmica'),
            default => $this->repository->getStructureByModelName('cerâmica')
        };

        $profile = [
            'name' => 'PERFIL SOOLLAR ALUMINIO 2,4MT FIBROCIMENTO/CERAMICA',
            'model' => 'PERFIL ALUMINIO 2,4MT FIBROCIMENTO/CERAMICA',
            'price' => 32.9,
            'stock' => 'ready delivery',
            'category' => 'Estruturas-Inox',
        ];

        $totalCost = ($structure->price * $structuresQuantity);

        $components = [
            'structures' => [
                'name' => $structure->name,
                'quantity' => $structuresQuantity,
                'price' => $structure->price,
            ],
            'profiles' => null
        ];

        if ($roofStructure !== RoofStructure::METALICO) {
            $profilesQuantity = $modulesQuantity;
            $totalCost += ($profile['price'] * $profilesQuantity);
            $components['profiles'] = [
                'name' => $profile['name'],
                'quantity' => $profilesQuantity,
                'price' => $profile['price']
            ];
        }

        return [
            'description' => 'Estrutura para ' . $roofStructure->name,
            'cost' => $totalCost,
            'components' => $components
        ];
    }

    private function calculateCable(int $modulesQuantity): array
    {
        $positiveCable = $this->cableService->getBestCableOption($modulesQuantity, self::VERMELHO);
        $negativeCable = $this->cableService->getBestCableOption($modulesQuantity, self::PRETO);

        return [
            'cost' => $positiveCable['cost'] + $negativeCable['cost'],
            'description' => [
                $positiveCable['description'],
                $negativeCable['description'],
            ],
            'quantity' => $positiveCable['quantity'] + $negativeCable['quantity'],
        ];
    }

    private function calculateConnectors(int $modulesQuantity): array
    {
        $quantity = (int)ceil($modulesQuantity / 5);

        return [
            'connectors' => $this->repository->getConnectors(),
            'quantity' => $quantity,
        ];
    }

    private function calculateCost(Module $module, int $modulesQuantity, Inverter $inverter, array $structureSpecs, array $cables, array $connectors): float
    {
        $moduleCost = $module->price * $modulesQuantity;
        $inverterCost = $inverter->price;
        $structureCost = $structureSpecs['cost'];
        $cablesCost = $cables['cost'];
        $connectorsCost = $connectors['connectors']->price * $connectors['quantity'];

        return $moduleCost + $inverterCost + $structureCost + $cablesCost + $connectorsCost;
    }
}
