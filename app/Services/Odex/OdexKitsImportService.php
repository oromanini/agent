<?php

namespace App\Services\Odex;

use App\Enums\DistributorsEnum;
use App\Enums\PanelBrands;
use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\Kit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OdexKitsImportService
{
    public function importMicroInverterKits(int $limit): void
    {
        $this->mountMicroInverterKits($limit);
        Log::info('ODEX kits import finished!');
    }

    private function mountMicroInverterKits(int $limit): void
    {
        $csvFilePath = 'resources/kits/odex/microinverter/kits_odex.csv';
        $microinverterData = $this->getMicroInverterDataFromCsvFile($csvFilePath);
        $microInverterConfigs = $this->setMicroInverterConfigs();

        $this->storeMicroInverterKits($microinverterData, $microInverterConfigs, $limit);
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

    private function setMicroInverterConfigs(): array
    {
        return [
            'panel_specs' => [
                'power' => 555,
                'brand' => 'ERA',
                'model' => 'ESPHSC555-M',
                'logo' => '/img/panel_brands/era.png',
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

    private function storeMicroInverterKits(array $microinverterData, array $microInverterConfigs, int $limit)
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
                    'components' => json_encode($this->setComponents($panelCount, $structure->value)),
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

    public function getKwp(array $microInverterConfigs, int $panelCount): int|float
    {
        return ($microInverterConfigs['panel_specs']['power'] / 1000) * $panelCount;
    }

    private function setCost(int $panelCount, array $microinverterData): float
    {
        $panelCost = (float)$microinverterData['Painel'];
        $microinverterCost = (float)$microinverterData['Microinversor'];
        $structureCost = (float)$microinverterData['Estrutura'];
        $connectorCost = (float)$microinverterData['conector'];
        $screwCost = (float)$microinverterData['parafuso'];
        $cableCost = (float)$microinverterData['Cabo'];

        $microInverterQuantity = ceil($panelCount / 4);

        $cableQuantity = $microInverterQuantity;

        return ($panelCount * $panelCost)
            + ($microinverterCost * $microInverterQuantity)
            + ($structureCost * $microInverterQuantity)
            + ($connectorCost * 4 * $microInverterQuantity)
            + ($screwCost * 2 * $microInverterQuantity)
            + ($cableCost * $cableQuantity);
    }

    private function setComponents(int $panelCount, string $structure): array
    {
        $microInverterQuantity = ceil($panelCount / 4);
        $screwQuantity = $microInverterQuantity * 2;
        $connectorQuantity = $microInverterQuantity * 4;
        $cableQuantity = $microInverterQuantity;

        return [
            "{$panelCount} PAINEL SOLAR FOTOVOLTAICO ERA SOLAR 555W 30MM 144 CELULAS MONO ESPHSC555-M",
            "{$microInverterQuantity} MICROINVERSOR SAJ 2.25KW M2-2.25-S4 MONOFASICO 220V 4 MPPT",
            "{$microInverterQuantity} KIT ESTRUTURA 4 PAINEIS {$structure}",
            "{$microInverterQuantity} KIT ESTRUTURA 4 PAINEIS {$structure}",
            "{$screwQuantity} PARAFUSO T CABECA DE MARTELO INOX M 8X25MM",
            "{$connectorQuantity} CONECTOR MC4 - STAUBLI / MACHO + FEMEA 2 PARES",
            "{$cableQuantity} CABO SOLAR 6MM 1.8KV CSO6P50 20M PRETO + VERMELHO",
        ];
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
            $kit->save();
        } else {
         $search->update($kitParams);
        }
    }
}
