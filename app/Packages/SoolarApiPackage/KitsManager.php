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
            $panelSpecs = $this->setPanelSpecs($module);
            $inverterSpecs = $this->setInverterSpecs($inverter);
            $structureSpecs = $this->setStructureSpecs($roofStructure, $modulesQuantity);
            $cables = $this->calculateCable($modulesQuantity);
            $connectors = $this->calculateConnectors($modulesQuantity);
            $kwp = $this->calculateKwp($modulesQuantity, $module->power);

            $components = $this->setComponents(
                $module,
                $inverter,
                $structureSpecs,
                $modulesQuantity,
                $cables,
                $connectors
            );

            $cost = $this->calculateCost($module, $modulesQuantity, $inverter, $structureSpecs, $cables, $connectors);

            $kit = new Kit();
            $kit->fillFromAttributes(
                description: $this->setKitDescription($kwp, $roofStructure, $inverter, $panelSpecs),
                kwp: $kwp,
                cost: $cost,
                roof_structure: $roofStructure->value,
                tension_pattern: $inverter->power >= 15 ? TensionPattern::TRIFASICO_220V->value : TensionPattern::MONOFASICO_220V->value,
                components: $components,
                panel_specs: $panelSpecs,
                inverter_specs: $inverterSpecs,
                distributor_name: "SOOLLAR",
                distributor_code: Uuid::uuid4()->toString(),
                availability: now()->toDateTimeString(),
                is_active: true,
            );

            $this->saveOrUpdateKit($kit);
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

    private function getKitDescription(Kit $kit): string
    {
        $roofStructure = RoofStructure::from($kit->roof_structure)->name;
        $inverterBrand = $kit->inverter_specs['brand'] ?? 'Inversor';
        $modulePower = $kit->panel_specs['power'] ?? 'N/A';
        $moduleQuantity = count($kit->components); // Isso pode ser impreciso, mas vamos supor que o primeiro componente é o módulo

        return "{$kit->kwp}kwp - {$moduleQuantity}x{$modulePower}w - {$inverterBrand} - Telhado {$roofStructure}";
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
            ...$cables['description'], // Use o operador de 'spread' para juntar os arrays
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

    public function setKitDescription(
        float $kwp,
        RoofStructure $roofStructure,
        Inverter $inverter,
        $panelSpecs
    ): string {
        return "Kit {$kwp} kWP {$roofStructure->name} {$inverter->brand} {$inverter->power}KW e {$panelSpecs['brand']} {$panelSpecs['power']}W";
    }

    private function getInverterOverload(InverterBrand $inverterBand): float
    {
        $overload = $inverterBand->overload ?? 0.50;

        return max(self::MINIMUM_OVERLOAD, $overload);
    }

    private function getInverterMinimumPanels(Inverter $inverter, Module $module): int
    {
        $inverterPower = $inverter->power * 1000;

        return (int)ceil(($inverterPower * self::MINIMUM_OVERLOAD) / $module->power);
    }

    private function getInverterMaximumPanels(Inverter $inverter, Module $module, float $overload): int
    {
        $inverterPower = $inverter->power * 1000;

        return (int)floor(($inverterPower * (1 + $overload)) / $module->power);
    }

    private function setPanelSpecs(Module $module): array
    {
        return [
            'id' => $module->id,
            'name' => $module->name,
            'brand' => $module->brand,
            'power' => $module->power,
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
            'power' => $inverter->power,
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

    //TODO: implement it
    private function calculateCost(Module $module, int $modulesQuantity, Inverter $inverter, array $structureSpecs, array $cables, array $connectors)
    {
        return 0;
    }
}
