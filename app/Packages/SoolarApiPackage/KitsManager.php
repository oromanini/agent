<?php

namespace App\Packages\SoolarApiPackage;

use App\Enums\RoofStructure;
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

    public function __construct(
        private readonly SoollarApiRepository $repository,
        private readonly CableService $cableService
    ) {}

    function handle(): JsonResponse
    {
        try {

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
        } catch (\Exception $e) {
            Log::error('SoollarKitPersistenceException: ' . $e->getMessage());
        }
    }

    private function mountAndSaveKit(
        int $modulesQuantity,
        Module $module,
        Inverter $inverter
    ): void {

        $kit = new Kit();


        foreach (RoofStructure::cases() as $roofStructure) {

            $panelSpecs = $this->setPanelSpecs($module);
            $inverterSpecs = $this->setInverterSpecs($inverter);
            $structureSpecs = $this->setStructureSpecs($roofStructure);
            $cables = $this->calculateCable($modulesQuantity);
            $connectors = $this->calculateConnectors();

            $kwp = $this->calculateKwp($modulesQuantity, $module->power);
            $components = $this->setComponents($module, $inverter, $structureSpecs, $modulesQuantity);

            $cost = $this->calculateCost($module, $modulesQuantity, $inverter, $structureSpecs, $cables, $connectors);

            $kit->fillFromAttributes(
                description: "",
                kwp: $kwp,
                cost: $cost,
                roof_structure: $roofStructure->value,
                tension_pattern: $inverter->voltage,
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
        if($this->repository->getKitByDescription($kit->description)) {
            $kit->update();
            return;
        }

        $kit->save();
    }

    private function calculateKwp(int $modulesQuantity, int $panelPower): float
    {
        return $modulesQuantity * ($panelPower / 1000);
    }

    private function getInverterOverload(InverterBrand $inverter): float
    {
        if (!empty($inverter->overload)) {
            return $inverter->overload + 1;
        }

        return 2;
    }

    private function getInverterMinimumPanels(Inverter $inverter, Module $module): int
    {
        return floor(
            ($inverter->power * self::MINIMUM_OVERLOAD) / ($module->power / 1000)
        );
    }

    private function getInverterMaximumPanels(
        Inverter $inverter,
        Module $module,
        float $overload
    ): int {
        return floor(
            ($inverter->power * $overload) / ($module->power / 1000)
        );
    }

    private function setPanelSpecs(Module $module): array
    {
        return [
            "brand" => $module->brand,
            "power" => $module->power,
            "model" => $module->model,
            "logo" => $module->getImage()['logo'],
            "warranty" => 12, //TODO: implementar
            "linear_warranty" => 25 //TODO: implementar
        ];
    }

    private function setInverterSpecs(Inverter $inverter): array
    {
        return [
            "brand" => $inverter->brand,
            "power" => $inverter->power,
            "model" => $inverter->model,
            "logo" => $inverter->getImage()['logo'],
            "warranty" => 10 //TODO: implementar
        ];
    }

    private function setStructureSpecs(RoofStructure $roofStructure): array
    {
    }

    private function calculateCable(int $moduleQuantity): array
    {
        $blackCable = $this->cableService->getBestCableOption(moduleQuantity: $moduleQuantity, color: 'black');
        $redCable = $this->cableService->getBestCableOption(moduleQuantity: $moduleQuantity, color: 'red');

        return [
            "description" => [$redCable->description, $blackCable->description],
            "cost" => ($redCable->cost) + ($blackCable->cost),
        ];
    }

    private function calculateConnectors(): array
    {
    }
}
