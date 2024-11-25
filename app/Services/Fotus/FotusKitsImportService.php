<?php

namespace App\Services\Fotus;

use App\Enums\DistributorsEnum;
use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\Kit;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\File;

class FotusKitsImportService
{
    private string $kitsCsvFilePath;
    public function __construct(?string $path = null)
    {

        $this->kitsCsvFilePath =  is_null($path)
            ? 'resources/kits/fotus/string_inverter/kits.csv'
            : $path;
    }

    public function importStringMonoInverterKits(): void
    {
        Log::info('FOTUS string kits import STARTED!');
        $this->mountStringMono220InverterKits();
        Log::info('FOTUS string kits import FINISHED!');
    }

    private function mountStringMono220InverterKits(): void
    {
        $kitsCsvFilePath = $this->kitsCsvFilePath;
        $data = $this->csvToArray($kitsCsvFilePath);

        $this->storeStringMono220InverterKits($data);
    }

    private function csvToArray(string $kitsCsvFilePath): array
    {
        if (!File::exists($kitsCsvFilePath)) {
            throw new \Exception("O arquivo CSV não foi encontrado no caminho especificado.");
        }

        $file = fopen($kitsCsvFilePath, 'r');

        try {
            $kitsHeader = fgetcsv($file);
            $kitsData = [];

            if (!$kitsHeader) {
                throw new \Exception("O arquivo CSV está vazio ou inválido.");
            }

            while (($row = fgetcsv($file)) !== false) {
                $rowData = array_combine($kitsHeader, $row);
                $kitsData[] = $rowData;
            }

            fclose($file);

            return $kitsData;

        } catch (\Throwable $exception) {
            throw new \Exception("Não foi possível abrir o arquivo CSV: {$exception->getMessage()}");
        }
    }

    private function setStringInverterConfigs(array $kit): array
    {
        return [
            'panel_specs' => [
                'power' => $kit["painel_potencia"],
                'brand' => $kit["painel_marca"],
                'model' => $kit["painel_modelo"],
                'warranty' => $kit["painel_garantia"],
            ],
            'inverter_specs' => [
                'power' => (float) $kit['inversor_potencia'],
                'brand' => $kit["inversor_marca"],
                'model' => $kit['inversor_modelo'],
                'warranty' => $kit["inversor_garantia"]
            ],
            'connector_per_mppt' => 2
        ];
    }

    private function storeStringMono220InverterKits(array $kits): void
    {

        foreach ($kits as $kit) {
            $specs = $this->setStringInverterConfigs($kit);

            foreach (RoofStructure::cases() as $structure) {

                    if ($structure->value == RoofStructure::SOLO->value) {
                        continue;
                    }

                    $kwp = $this->getKwp($kit['painel_potencia'], $kit['painel_quantidade']);
                    $kitParams = $this->setKitParams($kit, $kwp, $structure, $specs);
                    $newKit = new Kit($kitParams);

                    $this->saveOrUpdateStringInverterKit($newKit, $kwp);
                }
            }
    }

    private function setKitParams(array $kit, float $kwp, RoofStructure $structure, array $specs): array
    {
        $description = "Kit gerador {$kwp} kWP inversor {$kit['inversor_potencia']}kW"
        . "/ Painel {$kit['painel_marca']} {$kit['painel_potencia']}}W";

        $components = json_encode(
            $this->setComponents($kit, $structure->value)
        );

        return [
            'description' => $description,
            'kwp' => $kwp,
            'cost' => (float) $kit['custo_kit'],
            'roof_structure' => $structure->value,
            'tension_pattern' => TensionPattern::MONOFASICO_220V->value,
            'components' => $components,
            'panel_specs' => json_encode($specs['panel_specs']),
            'inverter_specs' => json_encode($specs['inverter_specs']),
            'distributor_name' => DistributorsEnum::FOTUS->value,
            'availability' => now()->toDateTimeString(),
            'is_active' => true,
        ];
    }

    private function getKwp(int $panel_power, int $panelQuantity): int|float
    {
        return ($panel_power / 1000) * $panelQuantity;
    }

    private function setComponents(array $kit, string $structure): array
    {
        $inverterQuantity = 1;
        $connectorQuantity = $kit['conector_quantidade'];
        $cableQuantity = $kit['cabo_quantidade'];
        $structureQuantity = ceil($kit['painel_quantidade'] / 4);

        return [
            "{$kit['painel_quantidade']} PAINEL SOLAR {$kit['painel_marca']} {$kit['painel_potencia']}W MONO {$kit['painel_modelo']}",
            "{$inverterQuantity} INVERSOR {$kit['inversor_marca']} {$kit['inversor_potencia']}KW MONOFASICO 220V 2 MPPT {$kit['inversor_modelo']}",
            "{$structureQuantity} KIT ESTRUTURA 4 PAINEIS {$structure}",
            "{$connectorQuantity} CONECTOR MC4 / MACHO + FEMEA 2 PARES",
            "{$cableQuantity} ROLO CABO SOLAR 6MM 1.8KV CSO6P50 {$cableQuantity}M PRETO / VERMELHO",
        ];
    }

    private function saveOrUpdateStringInverterKit(Kit $kit, float $kwp): void
    {
        $description = "Kit gerador {$kwp} kWP inversor {$kit['inversor_potencia']}kW"
            . "/ Painel {$kit['painel_marca']} {$kit['painel_potencia']}}W";

        $search = Kit::query()
            ->where('kwp', $kit->kwp)
            ->where('roof_structure', $kit->roof_structure)
            ->where('tension_pattern', $kit->tension_pattern)
            ->where('distributor_name', $kit->distributor_name)
            ->where('description', $description)
            ->first();


        if (is_null($search)) {
            $kit->distributor_code = Uuid::uuid4();
            $kit->save();
        } else {
            $search->update($kit->getAttributes());
        }
    }
}
