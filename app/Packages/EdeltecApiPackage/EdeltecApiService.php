<?php

namespace App\Packages\EdeltecApiPackage;

use App\Enums\DistributorsEnum;
use App\Enums\RoofStructure;
use App\Models\Kit;
use App\Packages\EdeltecApiPackage\Enums\Category;
use \App\Enums\TensionPattern;
use App\Packages\EdeltecApiPackage\Enums\InverterBrand;
use App\Packages\EdeltecApiPackage\Exceptions\EdeltecApiSearchFailException;
use App\Packages\KitResource;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class EdeltecApiService extends KitResource
{
    const DISTRIBUTOR = 'EDELTEC';
    const DAYS_FOR_INACTIVE = 12;
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /** @throws GuzzleException|EdeltecApiSearchFailException */
    public function importKitsFromGateway(): string
    {
        $productUrl = "https://api.edeltecsolar.com.br/produtos/integration?";
        $token = self::setApiToken();

        $finished = false;
        $page = 1;

        foreach (InverterBrand::cases() as $inverterBrand) {
            while (!$finished) {
                try {
                    $response = $this->client->get($productUrl, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token,
                            'Content-Type' => 'application/json',
                        ],
                        'query' => [
                            'tipo' => Category::ONGRID->value,
                            'page' => $page,
                            'fabricante' => $inverterBrand->value
                        ],
                    ])->getBody()->getContents();

                    $response = json_decode($response, true);

                    $this->storeKits(items: $response['items']);

                    $page === $response['meta']['totalPages'] && $finished = true;
                    $page++;

                } catch (EdeltecApiSearchFailException $e) {
                    throw new EdeltecApiSearchFailException('Erro ao buscar kits: ' . $e);
                }
            }
        }

        return 'Kits atualizados com sucesso!';
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

    private function storeKits(array $items): void
    {
        array_map(function ($item) {
            $this->storeOrUpdateKit($item);
        }, $items);
    }

    private function storeOrUpdateKit(array $item): void
    {
        $kit = Kit::query()->where('distributor_code', $item['id'])->first();
        $days_to_availability = !is_null($item['dataPrevistaParaDisponibilidade'])
            ? (new Carbon($item['dataPrevistaParaDisponibilidade']))->diffInDays(now())
            : 0;

        if (!is_null($kit)) {
            $kit->cost = $item['precoDoIntegrador'];
            $kit->update();
        } else {
            $days_to_availability <= self::DAYS_FOR_INACTIVE
            && Kit::create($this->setKitParams($item));
        }
    }

    private function setKitParams(array $item): array
    {
        $tension_pattern = TensionPattern::setTensionPattern($item['fase'] . ' ' . $item['tensaoSaida']);
        $structure = RoofStructure::matchRoof($item['estrutura'])->value;
        $availability = new Carbon($item['dataPrevistaParaDisponibilidade']);

        return [
            'description' => $item['titulo'],
            'kwp' => $item['potenciaGerador'],
            'cost' => $item['precoDoIntegrador'],
            'roof_structure' => $structure,
            'tension_pattern' => $tension_pattern,
            'components' => json_encode(EdeltecApiHelper::getComponents($item['componentes'])),
            'panel_specs' => json_encode(EdeltecApiHelper::setPanelSpecs($item)),
            'inverter_specs' => json_encode(EdeltecApiHelper::setInverterSpecs($item)),
            'distributor_name' => DistributorsEnum::EDELTEC->value,
            'distributor_code' => $item['id'],
            'availability' => $availability,
            'is_active' => true,
        ];
    }
}
