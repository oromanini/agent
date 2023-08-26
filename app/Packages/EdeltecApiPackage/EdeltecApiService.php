<?php

namespace App\Packages\EdeltecApiPackage;

use App\Packages\EdeltecApiPackage\Enums\Category;
use App\Packages\EdeltecApiPackage\Enums\InverterBrand;
use App\Packages\EdeltecApiPackage\Enums\PanelBrand;
use App\Packages\EdeltecApiPackage\Enums\StructureType;
use App\Packages\EdeltecApiPackage\Enums\TensionPattern;
use App\Packages\KitResource;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class EdeltecApiService extends KitResource
{
    const PAGE = 1;
    const MAX_API_TRIES = 3;
    const DISTRIBUTOR = 'EDELTEC';
    private Client $client;
    private array $compatibleKit;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function searchKits(
        InverterBrand $inverterBrand,
        PanelBrand    $panelBrand,
        StructureType $structureType,
        Category      $category,
        float         $kwp,
        string        $tensionPattern,
        string        $apiToken,
    ): array|null
    {
        $productUrl = "https://api.edeltecsolar.com.br/produtos/integration?";
        $i = 0;
        $tries = 0;
        do {
            $queryParams = $this->setQueryParams(
                category: $category,
                inverterBrand: $inverterBrand,
                panelBrand: $panelBrand,
                structureType: $structureType,
                power: $kwp + (0.5 * $i),
                tension: $tensionPattern
            );

            try {

                $response = $this->client->get($productUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiToken,
                        'Content-Type' => 'application/json',
                    ],
                    'query' => $queryParams,
                ]);

            } catch (\Throwable $e) {
                $message = 'Erro ao buscar kit Edeltec: ';
                $exception = new \Exception($message . $e);
                Log::error($message . $exception);
            }
            $i++;
            $tries++;

            if ($tries === self::MAX_API_TRIES) {
                return null;
            }

        } while (!$this->isCompatibleKit(response: $response, initialKwp: $kwp, tensionPattern: $tensionPattern));

        $kit = $this->compatibleKit;

        return self::sanitizeToDefaultProperties(
            description: $kit['titulo'],
            cost: $kit['precoDoIntegrador'],
            roof_structure: $kit['estrutura'],
            distributor_name: self::DISTRIBUTOR,
            distributor_code: $kit['id'],
            availability: new Carbon($kit['dataPrevistaParaDisponibilidade']),
            kwp: $kit['potenciaGerador'],
            panel_model: EdeltecApiHelper::getPanelModel($kit['caracteristicasModulo']),
            panel_brand: $kit['marca'],
            panel_power: $kit['potenciaModulo'],
            panel_warranty: EdeltecApiHelper::getPanelWarranty($panelBrand),
            panel_efficiency: EdeltecApiHelper::getPanelEfficiency($kit['caracteristicasModulo']),
            panel_logo: EdeltecApiHelper::getPanelLogo($kit['marca']),
            panel_linear_warranty: EdeltecApiHelper::getPanelLinearWarranty($panelBrand),
            inverter_model: EdeltecApiHelper::getInverterModel($kit['caracteristicasInversor']),
            inverter_brand: $kit['fabricante'],
            inverter_power: $kit['potenciaInversor'],
            inverter_warranty: EdeltecApiHelper::getInverterWarranty($inverterBrand),
            inverter_logo: EdeltecApiHelper::getInverterLogo($kit['fabricante']),
            inverter_tension: $kit['fase'] . ' ' . $kit['tensaoSaida'],
            components: EdeltecApiHelper::getComponents($kit['componentes']),
        );
    }

    private function isCompatibleKit(ResponseInterface $response, float $initialKwp, string $tensionPattern): bool
    {
        $kits = EdeltecApiHelper::decodeResponse($response);

        if (isset($kits['items'])) {
            foreach ($kits['items'] as $kit) {
                $isCompatibleKwp = $kit['potenciaGerador'] >= $initialKwp;

                $isCompatibleTension = TensionPattern::isCompatibleTension(
                    possiblePattern: $tensionPattern,
                    phase: $kit['fase'],
                    tension: $kit['tensaoSaida']
                );

                if ($isCompatibleKwp && $isCompatibleTension && EdeltecApiHelper::isAvailable($kit)) {
                    $this->compatibleKit = $kit;
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    public static function setApiToken(): string
    {
        $headers = ['Content-Type' => 'application/json'];
        $jsonBody = [
            "apiKey" => '72043154-b241-4e91-86cf-4e5ccf39a1e2',
            "secret" => 'KHd4Iqqky+0lSBbAV76UsTSFnwcotRt1crfltFW7RIc='
        ];

        try {
            $response = (new Client())->post('https://api.edeltecsolar.com.br/api-access/token', [
                'headers' => $headers,
                'json' => $jsonBody,
            ]);

        } catch (\Throwable $error) {
            throw new \Exception('[EDELTEC] Erro ao retornar token: ' . $error);
        }

        return $response->getBody()->getContents();
    }

    private function setQueryParams(
        Category      $category,
        InverterBrand $inverterBrand,
        PanelBrand    $panelBrand,
        StructureType $structureType,
        float         $power,
        string        $tension
    ): array
    {
        return [
            "tipo" => $category->value,
            "fabricante" => $inverterBrand->value,
            "marca" => $panelBrand->value,
            "estrutura" => $structureType->value,
            "q" => $power,
            "limit" => 15,
            "page" => self::PAGE,
            "sort" => self::setSort($tension, $power)
        ];
    }

    public static function setSort(string $tension, float $power): int
    {
        $filterPriceDesc = in_array($tension, ['Monofásico 220V', 'Bifásico 220V', 'Trifásico 220V']) && $power > 13.5;
        $filterPriceAsc = $tension[0] == 'Trifásico 380V' && $power > 13.5;

        if ($filterPriceDesc) {
            return 2;
        } elseif ($filterPriceAsc) {
            return 1;
        } else {
            return 0;
        }
    }
}
