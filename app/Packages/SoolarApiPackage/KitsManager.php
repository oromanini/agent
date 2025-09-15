<?php

namespace App\Packages\SoolarApiPackage;

use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\Kit;
use App\Packages\SoolarApiPackage\Contracts\KitsManagerInterface;
use App\Packages\SoolarApiPackage\Models\Inverter;
use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\Module;
use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;
use App\Packages\SoolarApiPackage\Services\CableService;
use Ramsey\Uuid\Uuid;

class KitsManager implements KitsManagerInterface
{
    const MINIMUM_OVERLOAD = 0.8;
    const PRETO = 'PRETO';
    const VERMELHO = 'VERMELHO';

    private int $createdKitsCount;
    private int $updatedKitsCount;

    public function __construct(
        private readonly SoollarApiRepository $repository,
        private readonly CableService $cableService
    ) {
        $this->createdKitsCount = 0;
        $this->updatedKitsCount = 0;
    }

    function handle(): void
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
                        $this->createKits($module, $inverter);
                    }
                }
            }
        }
    }

    private function createKits(Module $module, Inverter $inverter): void
    {
        $kWp = ($inverter->output_power / self::MINIMUM_OVERLOAD) / 1000;
        $modulesQuantity = (int)ceil(($kWp * 1000) / $module->power);
        $invertersQuantity = (int)ceil(($module->power * $modulesQuantity * 1.25) / $inverter->input_power);

        if ($invertersQuantity > 1) {
            return;
        }

        $kitDescription = $modulesQuantity . ' Módulos de ' . $module->power . 'W ' . $module->brand . ' + ' . $invertersQuantity . ' Inversor ' . $inverter->output_power . 'W ' . $inverter->brand;
        $kitKwp = round(($module->power * $modulesQuantity) / 1000, 2);

        $structure = $this->calculateStructure($modulesQuantity);
        $cables = $this->calculateCable($modulesQuantity);
        $connectors = $this->calculateConnectors($modulesQuantity);

        $kitCost = $this->calculateCost($module, $modulesQuantity, $inverter, $invertersQuantity, $structure, $cables, $connectors);
        $kitHash = Uuid::uuid4()->toString();

        $kit = [
            'kit_hash' => $kitHash,
            'description' => $kitDescription,
            'kwp' => $kitKwp,
            'cost' => $kitCost,
            'roof_structure' => $structure['structure']->roof_structure,
            'tension_pattern' => $inverter->tension_pattern,
            'is_active' => true,
            'distributor_name' => 'SOOLLAR',
            'components' => [
                'module' => [
                    'quantity' => $modulesQuantity,
                    'specs' => $module->only('id', 'name', 'model', 'power', 'price', 'brand')
                ],
                'inverter' => [
                    'quantity' => $invertersQuantity,
                    'specs' => $inverter->only('id', 'name', 'model', 'power', 'price', 'brand')
                ],
                'structure' => [
                    'quantity' => $structure['quantity'],
                    'specs' => $structure['structure']->only('id', 'name', 'model', 'price')
                ],
                'cables' => [
                    'quantity' => $cables['quantity'],
                    'description' => $cables['description'],
                    'cost' => $cables['cost'],
                ],
                'connectors' => [
                    'quantity' => $connectors['quantity'],
                    'specs' => $connectors['connectors']->only('id', 'name', 'price', 'type'),
                ]
            ],
            'module_specs' => $module->toArray(),
            'inverter_specs' => $inverter->toArray(),
        ];

        $this->createOrUpdateKit($kit);
    }

    private function createOrUpdateKit(array $kitData): void
    {
        $existingKit = $this->repository->getKitByDescription($kitData['description']);

        if ($existingKit) {
            $existingKit->update($kitData);
            $this->updatedKitsCount++;
        } else {
            Kit::query()->create($kitData);
            $this->createdKitsCount++;
        }

        // Verifica se a soma dos contadores é um múltiplo de 1000 e atualiza o processo.
        $total = $this->createdKitsCount + $this->updatedKitsCount;
        if ($total > 0 && $total % 1000 === 0) {
            SoollarImportHistory::updateProcess(
                createdKits: $this->createdKitsCount,
                updatedKits: $this->updatedKitsCount,
            );
        }
    }

    private function calculateStructure(int $modulesQuantity): array
    {
        $roofStructure = RoofStructure::SOLO->value;

        return [
            'structure' => $this->repository->getStructureByModelName($roofStructure),
            'quantity' => (int)ceil($modulesQuantity / 2),
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

    private function calculateCost(Module $module, int $modulesQuantity, Inverter $inverter, int $invertersQuantity, array $structureSpecs, array $cables, array $connectors): float
    {
        $moduleCost = $module->price * $modulesQuantity;
        $inverterCost = $inverter->price * $invertersQuantity;
        $structureCost = $structureSpecs['structure']->price * $structureSpecs['quantity'];
        $cablesCost = $cables['cost'];
        $connectorsCost = $connectors['connectors']->price * $connectors['quantity'];

        return $moduleCost + $inverterCost + $structureCost + $cablesCost + $connectorsCost;
    }
}
