<?php

namespace App\Services\Odex;

use App\Enums\DistributorsEnum;
use App\Enums\PanelBrands;
use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\ActiveKit;
use App\Models\Kit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class OdexKitsImportService
{
    public function importMicroInverterKits(int $limit): void
    {
        $this->inactiveAllMicroInverterKits();
        $this->mountMicroInverterKits($limit);

        Log::info('ODEX kits import finished!');
    }

    public function importStringMono220InverterKits(): void
    {
        $this->mountStringMono220InverterKits();
        Log::info('ODEX string kits import finished!');
    }

    private function mountMicroInverterKits(int $limit): void
    {
        $csvFilePath = 'resources/kits/odex/microinverter/kits_odex.csv';
        $microinverterData = $this->getMicroInverterDataFromCsvFile($csvFilePath);
        $microInverterConfigs = $this->setMicroInverterConfigs();

        $this->storeMicroInverterKits($microinverterData, $microInverterConfigs, $limit);
    }

    private function mountStringMono220InverterKits(): void
    {
        $componentsCsvFilePath = 'resources/kits/odex/string_inverter/kits_string.csv';
        $invertersCsvFilePath = 'resources/kits/odex/string_inverter/inversores_string.csv';
        $data = $this->getStringMono220InverterDataFromCsvFile($componentsCsvFilePath, $invertersCsvFilePath);

        $this->storeStringMono220InverterKits($data);
    }

    private function getMicroInverterDataFromCsvFile(string $microinverterCsvPath): array
    {
        $fileHandle = fopen($microinverterCsvPath, 'r');

        try {
            $headers = fgetcsv($fileHandle);

            $data = [];

            while (($row = fgetcsv($fileHandle)) !== false) {
                $rowData = array_combine($headers, $row);
                $data[] = $rowData;
            }

            fclose($fileHandle);
            return $data;

        } catch (\Throwable $exception) {
            throw new \Exception("Não foi possível abrir o arquivo CSV: {$exception->getMessage()}");
        }
    }

    private function getStringMono220InverterDataFromCsvFile(string $componentsCsvFilePath, string $invertersCsvFilePath): array
    {
        $componentsFileHandle = fopen($componentsCsvFilePath, 'r');
        $invertersFileHandle = fopen($invertersCsvFilePath, 'r');

        try {
            $componentsHeader = fgetcsv($componentsFileHandle);
            $invertersHeader = fgetcsv($invertersFileHandle);

            $componentsData = [];
            $invertersData = [];

            while (($row = fgetcsv($componentsFileHandle)) !== false) {
                $rowData = array_combine($componentsHeader, $row);
                $componentsData[] = $rowData;
            }

            while (($row = fgetcsv($invertersFileHandle)) !== false) {
                $rowData = array_combine($invertersHeader, $row);
                $invertersData[] = $rowData;
            }

            fclose($componentsFileHandle);
            fclose($invertersFileHandle);

            return ['components' => $componentsData[0], 'inverters' => $invertersData];

        } catch (\Throwable $exception) {
            throw new \Exception("Não foi possível abrir o arquivo CSV: {$exception->getMessage()}");
        }
    }

    private function setMicroInverterConfigs(): array
    {
        return [
            'panel_specs' => [
                'power' => 555,
                'brand' => 'ERA',
                'model' => 'ESPHSC555-M',
                'logo' => '/img/panel_brands/era.png',
                'warranty' => 15
            ],
            'inverter_specs' => [
                'power' => 2.25,
                'brand' => 'SAJ Microinverter',
                'model' => 'M2-2.25K-S4',
                'logo' => '/img/inverter_brands/saj_micro.png',
                'warranty' => 10

            ],
            'screw_per_panel' => 2,
            'connector_per_inverter' => 4
        ];
    }

    private function setStringInverterConfigs(array $inverter): array
    {
        return [
            'panel_specs' => [
                'power' => 555,
                'brand' => 'ERA',
                'model' => 'ESPHSC555-M',
                'logo' => '/img/panel_brands/era.png',
                'warranty' => 15
            ],
            'inverter_specs' => [
                'power' => (float)$inverter['potencia'],
                'brand' => 'SAJ',
                'model' => $inverter['modelo'],
                'logo' => '/img/inverter_brands/saj.png',
                'warranty' => 10

            ],
            'connector_per_mppt' => 2
        ];
    }

    private function storeMicroInverterKits(array $microinverterData, array $microInverterConfigs, int $limit): void
    {
        $panelCount = 4;

        while ($panelCount <= $limit) {

            foreach (RoofStructure::cases() as $structure) {
                if ($structure->value == RoofStructure::SOLO->value) {
                    continue;
                }

                $kwp = $this->getKwp($microInverterConfigs, $panelCount);
                $cost = $this->setCost($panelCount, $microinverterData[0]);

                $kitParams = [
                    'description' => "Kit gerador {$kwp} kWP microinversor SAJ/ Era 555W",
                    'kwp' => $kwp,
                    'cost' => $cost,
                    'roof_structure' => $structure->value,
                    'tension_pattern' => TensionPattern::MONOFASICO_220V->value,
                    'components' => json_encode($this->setComponents($panelCount, $structure->name)),
                    'panel_specs' => json_encode($microInverterConfigs['panel_specs']),
                    'inverter_specs' => json_encode($microInverterConfigs['inverter_specs']),
                    'distributor_name' => DistributorsEnum::ODEX->value,
                    'distributor_code' => 'N/A',
                    'availability' => (new Carbon('2024-04-24'))->toDateTimeString(),
                    'is_active' => true,
                ];

                $kit = new Kit($kitParams);
                $this->saveOrUpdateMicroInverterKit($kit, $kitParams);
            }

            $panelCount++;
        }
    }

    private function storeStringMono220InverterKits(array $stringInverterData): void
    {
        $componentsData = $stringInverterData['components'];
        $invertersData = $stringInverterData['inverters'];

        foreach ($invertersData as $inverter) {
            $specs = $this->setStringInverterConfigs($inverter);
            $panelPower = $specs['panel_specs']['power'];
            $minCountPanels = $this->getMinCountPanelsByInverterPower($inverter['potencia'], $panelPower);
            $maxCountPanels = $this->getMaxCountPanelsByInverterPower($inverter['potencia'], $panelPower);

            for ($panelCount = $minCountPanels; $panelCount <= $maxCountPanels; $panelCount++) {
                foreach (RoofStructure::cases() as $structure) {
                    if ($structure->value == RoofStructure::SOLO->value) {
                        continue;
                    }
                    $kwp = $this->getKwp($specs, $panelCount);
                    $cost = $this->setCost($panelCount, $componentsData, $inverter);

                    $kitParams = $this->setKitParams(
                        kwp: $kwp,
                        cost: $cost,
                        structure: $structure,
                        panelCount: $panelCount,
                        specs: $specs,
                        inverter: $inverter
                    );

                    $kit = new Kit($kitParams);
                    $this->saveOrUpdateStringInverterKit($kit, $kitParams, $inverter);
                }
            }
        }
    }

    private function setKitParams(
        float $kwp,
        float $cost,
        mixed $structure,
        int   $panelCount,
        array $specs,
        ?array $inverter = null
    ): array
    {
        return [
            'description' => is_null($inverter)
                ? "Kit gerador {$kwp} kWP microinversor SAJ/ Era 555W"
                : "Kit gerador {$kwp} kWP inversor SAJ {$inverter['potencia']}kW / Painel ERA 555W",
            'kwp' => $kwp,
            'cost' => $cost,
            'roof_structure' => $structure->value,
            'tension_pattern' => TensionPattern::MONOFASICO_220V->value,
            'components' => json_encode($this->setComponents($panelCount, $structure->value, $inverter)),
            'panel_specs' => json_encode($specs['panel_specs']),
            'inverter_specs' => json_encode($specs['inverter_specs']),
            'distributor_name' => DistributorsEnum::ODEX->value,
            'distributor_code' => 'N/A',
            'availability' => (new Carbon('2024-04-24'))->toDateTimeString(),
            'is_active' => true,
        ];
    }

    public function getKwp(array $microInverterConfigs, int $panelCount): int|float
    {
        return ($microInverterConfigs['panel_specs']['power'] / 1000) * $panelCount;
    }

    private function setCost(int $panelCount, array $data, ?array $inverter = null): float
    {
        $panelCost = (float)$data['painel'];

        $inverterCost = is_null($inverter)
            ? (float)$data['microinversor']
            : (float)$inverter['valor'];

        $structureCost = (float)$data['estrutura'];
        $connectorCost = (float)$data['conector'];

        $screwCost = is_null($inverter) ?
            (float)$data['parafuso']
            : 0;

        $cableCost = (float)$data['cabo'];

        $microInverterQuantity = ceil($panelCount / 4);

        $cableQuantity = $microInverterQuantity;

        if (!is_null($inverter)) {
            $minCable = 30;
            $cableQuantity = ceil(($panelCount * 3) / $minCable);

            return ($panelCount * $panelCost)
                + $inverterCost
                + ($structureCost * ceil($panelCount / 4))
                + ($connectorCost * 2)
                + ($cableCost * $cableQuantity)
                ;
        }

        return ($panelCount * $panelCost)
            + ($inverterCost * $microInverterQuantity)
            + ($structureCost * $microInverterQuantity)
            + ($connectorCost * 4 * $microInverterQuantity)
            + ($screwCost * 2 * $microInverterQuantity)
            + ($cableCost * $cableQuantity);
    }

    private function setComponents(int $panelCount, string $structure, ?array $inverter = null): array
    {
        $inverterQuantity = is_null($inverter) ? ceil($panelCount / 4) : 1;
        $screwQuantity = $inverterQuantity * 2;
        $connectorQuantity = is_null($inverter) ? $inverterQuantity * 4 : 2;
        $cableQuantity = is_null($inverter) ? $inverterQuantity : ceil(($panelCount * 3) / 30);
        $structureQuantity = ceil($panelCount / 4);

        $rollSize = is_null($inverter) ? 20 : 30;

        $components = [
            "{$panelCount} PAINEL SOLAR FOTOVOLTAICO ERA SOLAR 555W 30MM 144 CELULAS MONO ESPHSC555-M",
            "{$inverterQuantity} MICROINVERSOR SAJ 2.25KW M2-2.25-S4 MONOFASICO 220V 4 MPPT",
            "{$structureQuantity} KIT ESTRUTURA 4 PAINEIS {$structure}",
            "{$connectorQuantity} CONECTOR MC4 - STAUBLI / MACHO + FEMEA 2 PARES",
            "{$cableQuantity} ROLO CABO SOLAR 6MM 1.8KV CSO6P50 {$rollSize}M PRETO + VERMELHO",
        ];

        if (is_null($inverter)) {
            $components[] = "{$screwQuantity} PARAFUSO T CABECA DE MARTELO INOX M 8X25MM";
        }

        return $components;
    }

    private function saveOrUpdateMicroInverterKit(Kit $kit, array $kitParams): void
    {
        $search = Kit::query()
            ->where('kwp', $kit->kwp)
            ->where('roof_structure', $kit->roof_structure)
            ->where('tension_pattern', $kit->tension_pattern)
            ->where('tension_pattern', $kit->tension_pattern)
            ->where('distributor_name', $kit->distributor_name)
            ->where('description', "Kit gerador {$kit->kwp} kWP microinversor SAJ/ Era 555W")
            ->first();


        if (is_null($search)) {
            $kit->distributor_code = Uuid::uuid4();
            $kit->save();
        } else {
            $search->update($kitParams);
        }
    }

    private function saveOrUpdateStringInverterKit(Kit $kit, array $kitParams, array $inverter): void
    {
        $search = Kit::query()
            ->where('kwp', $kit->kwp)
            ->where('roof_structure', $kit->roof_structure)
            ->where('tension_pattern', $kit->tension_pattern)
            ->where('tension_pattern', $kit->tension_pattern)
            ->where('distributor_name', $kit->distributor_name)
            ->where('description', "Kit gerador {$kit->kwp} kWP inversor SAJ {$inverter['potencia']}kW / Painel ERA 555W")
            ->first();


        if (is_null($search)) {
            $kit->distributor_code = Uuid::uuid4();
            $kit->save();
        } else {
            $search->update($kitParams);
        }
    }

    private function getMinCountPanelsByInverterPower(float $inverterPower, int $panelPower): int
    {
        $inverterMinimumPower = $inverterPower * 0.8;
        $panelPowerInWatts = $panelPower / 100;
        $min = ceil($inverterMinimumPower / $panelPowerInWatts);

        return max($min, 5);
    }

    private function getMaxCountPanelsByInverterPower(float $inverterPower, int $panelPower): int
    {
        $inverterMaximumPower = $inverterPower * 1.4;
        $panelPowerInWatts = $panelPower / 1000;

        return ceil($inverterMaximumPower / $panelPowerInWatts);
    }

    private function inactiveAllMicroInverterKits(): void
    {
        $kits = Kit::query()
            ->where('distributor_name', DistributorsEnum::ODEX->name)
            ->where('description', 'like', "%microinversor%")
            ->get();

        $kits->each(function ($kit) {
            $kit->is_active = false;
            $kit->update();
        });

        $activeKit = ActiveKit::query()
            ->where('inverter_brand', 'SAJ Microinverter')
            ->first();

        $activeKit->is_active = false;
        $activeKit->update();
    }
}
