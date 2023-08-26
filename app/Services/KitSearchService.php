<?php

namespace App\Services;

use App\Enums\DistributorsEnum;
use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\ActiveKit;
use App\Packages\EdeltecApiPackage\EdeltecApiService;
use App\Packages\EdeltecApiPackage\Enums\StructureType;
use App\Packages\KitResource;
use GuzzleHttp\Client;


class KitSearchService
{

    public function kitSearch(float $kwp, int $roof, string $tension): array
    {
        $distributors = DistributorsEnum::cases();
        $combinations = $this->fetchCombinations($distributors);
        $roofName = strtoupper(RoofStructure::from($roof)->name);

        return $this->generateKits($combinations, $roofName, $kwp, $tension);
    }


    protected function fetchCombinations(array $distributors): array
    {
        $distributorsNameList = array_column($distributors, 'value');

        $combinations = ActiveKit::query()
            ->where('is_active', true)
            ->whereIn('distributor', $distributorsNameList)
            ->get(['panel_brand', 'inverter_brand', 'distributor'])
            ->toArray();

        $result = [];

        foreach ($combinations as $item) {
            $distributor = $item['distributor'];
            unset($item['distributor']);

            $result[$distributor][] = $item;
        }

        return array_map(function ($kits) {
            return array_values($kits);
        }, $result);
    }

    protected function generateKits(array $combinations, string $roofName, float $kwp, string $tension): array
    {
        $kits = [];
        $edeltecApiToken = self::getGatewayApiToken('EDELTEC');

        foreach ($combinations as $distributor => $combination) {
            if (!empty($combination)) {
                foreach ($combination as $item) {
                    $kit = $this->setKit($distributor, $item, $roofName, $kwp, $tension, $edeltecApiToken);
                    $kits[] = $kit;
                }
            }
        }
        return $kits;
    }

    private function setKit(
        string $distributor,
        array  $item,
        string $roof,
        float  $kwp,
        string $tension,
        string $loginToken
    ): ?array
    {
        $inverter = constant(self::getGatewayPath($distributor) . '\Enums\InverterBrand::' . $item['inverter_brand']);
        $panel = constant(self::getGatewayPath($distributor) . '\Enums\PanelBrand::' . $item['panel_brand']);
        $structure = self::getGatewayStructureType($distributor, $roof);

        return self::getGateway($distributor)->searchKits(
            inverterBrand: $inverter,
            panelBrand: $panel,
            structureType: $structure,
            category: (self::getGatewayCategory($distributor)),
            kwp: $kwp,
            tensionPattern: TensionPattern::setTensionPattern($tension),
            apiToken: $loginToken,
        );
    }

    public static function getGateway(string $distributor): KitResource
    {
        if ($distributor == DistributorsEnum::EDELTEC->value) {
            return new EdeltecApiService(new Client());
        }
    }

    public static function getGatewayPath(string $distributor): string
    {
        if ($distributor == DistributorsEnum::EDELTEC->value) {
            return (new \ReflectionClass(
                new EdeltecApiService(new Client())
            ))->getNamespaceName();
        }
    }

    public static function getGatewayCategory(string $distributor)
    {
        if ($distributor == DistributorsEnum::EDELTEC->value) {
            return \App\Packages\EdeltecApiPackage\Enums\Category::ONGRID;
        }
    }

    public static function getGatewayStructureType(string $distributor, string $roof)
    {
        if ($distributor == DistributorsEnum::EDELTEC->value) {
            return StructureType::matchRoof($roof);
        }
    }

    private static function getGatewayApiToken(string $distributor)
    {
        if ($distributor == DistributorsEnum::EDELTEC->value) {
            return EdeltecApiService::setApiToken();
        }
    }
}
