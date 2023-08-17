<?php

namespace App\Packages\EdeltecApiPackage;

use App\Packages\EdeltecApiPackage\Enums\Category;
use App\Packages\EdeltecApiPackage\Enums\InverterBrand;
use App\Packages\EdeltecApiPackage\Enums\PanelBrand;
use App\Packages\EdeltecApiPackage\Enums\StructureType;
use App\Packages\KitResource;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class EdeltecApiService extends KitResource
{
    const SORT_BY_KWP_ASC = 0;
    const PAGE = 1;
    const MAX_API_TRIES = 5;
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
        float         $kwp
    ): array
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
            );

            try {

                $response = $this->client->get($productUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->getApiToken(),
                        'Content-Type' => 'application/json',
                    ],
                    'query' => $queryParams,
                ]);

            } catch (\Throwable $e) {
                $message = 'Erro ao buscar kit Edeltec: ';
                $exception = new \Exception($message . $e);
                Log::error($message . $exception);
                throw $exception;
            }
            $i++;
            $tries++;

            $tries === self::MAX_API_TRIES
            && throw new \Exception(
                "[EDELTEC] Nenhum kit disponível em estoque!"
            );

        } while (!$this->isCompatibleKit(response: $response, initialKwp: $kwp));

        return self::sanitizeToDefaultProperties(
            description: $this->compatibleKit['titulo'],
            cost: $this->compatibleKit['precoDoIntegrador'],
            roof_structure: $this->compatibleKit['estrutura'],
            distributor_name: self::DISTRIBUTOR,
            distributor_code: $this->compatibleKit['id'],
            availability: new Carbon($this->compatibleKit['dataPrevistaParaDisponibilidade']),
            kwp: $this->compatibleKit['potenciaGerador'],
            panel_model: EdeltecApiHelper::getPanelModel($this->compatibleKit['caracteristicasModulo']),
            panel_brand: $this->compatibleKit['marca'],
            panel_power: $this->compatibleKit['potenciaModulo'],
            panel_warranty: EdeltecApiHelper::getPanelWarranty($panelBrand),
            panel_efficiency: EdeltecApiHelper::getPanelEfficiency($this->compatibleKit['caracteristicasModulo']),
            panel_logo: '',
            panel_linear_warranty: EdeltecApiHelper::getPanelLinearWarranty($panelBrand),
            inverter_model: EdeltecApiHelper::getInverterModel($this->compatibleKit['caracteristicasInversor']),
            inverter_brand: $this->compatibleKit['fabricante'],
            inverter_power: $this->compatibleKit['potenciaInversor'],
            inverter_warranty: EdeltecApiHelper::getInverterWarranty($inverterBrand),
            inverter_logo: '',
        );
    }

    private function isCompatibleKit(ResponseInterface $response, float $initialKwp): bool
    {
        $kits = EdeltecApiHelper::decodeResponse($response);

        foreach ($kits['items'] as $kit) {

            $isCompatible = $kit['potenciaGerador'] >= $initialKwp;

            if ($isCompatible && EdeltecApiHelper::isAvailable($kit)) {
                $this->compatibleKit = $kit;
                return true;
            }
        }
        return false;
    }

    private function getApiToken(): string
    {
        $headers = ['Content-Type' => 'application/json'];
        $jsonBody = [
            "apiKey" => '72043154-b241-4e91-86cf-4e5ccf39a1e2',
            "secret" => 'KHd4Iqqky+0lSBbAV76UsTSFnwcotRt1crfltFW7RIc='
        ];

        try {
            $response = $this->client->post('https://api.edeltecsolar.com.br/api-access/token', [
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
        float         $power
    ): array
    {
        return [
            "tipo" => $category->value,
            "fabricante" => $inverterBrand->value,
            "marca" => $panelBrand->value,
            "estrutura" => $structureType->value,
            "sort" => self::SORT_BY_KWP_ASC,
            "q" => $power,
            "limit" => 15,
            "page" => self::PAGE
        ];
    }
}
