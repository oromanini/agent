<?php

namespace App\Packages\EdeltecApiPackage;

use App\Enums\DistributorsEnum;
use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Jobs\InactiveKitsJob;
use App\Models\ActiveKit;
use App\Models\Kit;
use App\Packages\EdeltecApiPackage\Enums\Category;
use App\Packages\EdeltecApiPackage\Enums\InverterBrand;
use App\Packages\EdeltecApiPackage\Enums\PanelBrand;
use App\Packages\KitResource;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class EdeltecApiService
{
    const KITS_URI = "/produtos/integration?";
    const UNAUTHORIZED = 401;
    const ITEMS_LIMIT = 30;
    private Client $client;
    private EdeltecCredentials $credentials;

    public function __construct(private readonly EdeltecApiRepositoryInterface $edeltecApiRepository)
    {
        $this->client = new Client();
        $this->credentials = new EdeltecCredentials();
    }

    public function importKitsFromApi(): void
    {
        $start_time = time();
        $this->inactiveAllKitsBeforeUpdate();

        foreach (InverterBrand::cases() as $inverterBrand) {
            foreach (PanelBrand::cases() as $panelBrand) {

                $combination = $this->searchCombination($panelBrand, $inverterBrand);

                if (!$combination->is_active) {
                   continue;
                }

                $page = 1;
                $finished = false;

                while (!$finished) {

                    is_null($this->credentials->bearerToken) && $this->credentials->setOrRenewApiToken();

                    $response = self::responseToArray(
                        $this->sendRequest(
                            page: $page,
                            panel: $panelBrand->value,
                            inverter: $inverterBrand->value,
                            bearerToken: $this->credentials->bearerToken
                        )
                    );

                    $totalPages = $response["meta"]["totalPages"];
                    $currentPage = $response["meta"]["currentPage"];

                    if ($totalPages == 0) {
                        $finished = true;
                        continue;
                    }

                    if (isset($response['statusCode']) && $response['statusCode'] === self::UNAUTHORIZED) {
                        $this->credentials->setOrRenewApiToken();
                    }

                    $this->storeKits($response['items']);

                    $progress = $this->setProgress(page: $currentPage, totalPages: $totalPages);

                    Log::info($this->setLogMessage(
                        panelBrand: $panelBrand->value,
                        inverterBrand: $inverterBrand->value,
                        progress: $progress,
                        page: $currentPage,
                        totalPages: $totalPages,
                    )->progress,
                        [
                            'import' => [
                                'kits' => [
                                    'distributor' => DistributorsEnum::EDELTEC->value,
                                ]
                            ]
                        ]);
                    $page++;

                    if ($currentPage == $totalPages) {
                        $finished = true;
                        $page = 1;
                    }
                }
                $this->updateCombination($panelBrand, $inverterBrand, $combination);
            }
        }

        Log::notice('Kits atualizados com sucesso! (' . $this->setConclusionTime($start_time) . ')');
    }

    private function sendRequest(int $page, string $panel, string $inverter, string $bearerToken): ResponseInterface
    {
        try {
            return $this->client->get(EdeltecApiHelper::BASE_API_URL . self::KITS_URI, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $bearerToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'marca' => $panel,
                    'fabricante' => $inverter,
                    'page' => $page,
                    'tipo' => Category::ONGRID->value,
                    'limit' => self::ITEMS_LIMIT
                ],
            ]);

        } catch (\Throwable $e) {
            $message = 'Erro ao buscar kit Edeltec: ';
            Log::warning($message . $e);

        }
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
            $kit->update($this->setKitParams($item));
            $kit->cost = $item['precoDoIntegrador'];
            $kit->availability = $item['dataPrevistaParaDisponibilidade'] ?? now()->toDateString();
            $kit->is_active = true;
            $kit->update();
        } else {
            try {
                Kit::create($this->setKitParams($item));
            } catch (\Throwable $e) {
                throw new \Exception('Erro ao criar novo kit: ' . $e->getMessage());
            }
        }
    }

    private function setKitParams(array $item): array
    {
        $tension_pattern = TensionPattern::translateExternalTension($item['fase'] . ' ' . $item['tensaoSaida']);
        $structure = RoofStructure::translateExternalRoof($item['estrutura'])->value;
        $availability = new Carbon($item['dataPrevistaParaDisponibilidade']);

        return [
            'description' => $item['titulo'],
            'kwp' => $item['potenciaGerador'],
            'cost' => $item['precoDoIntegrador'],
            'roof_structure' => $structure,
            'tension_pattern' => $tension_pattern,
            'components' => EdeltecApiHelper::getComponents($item['componentes']),
            'panel_specs' => EdeltecApiHelper::setPanelSpecs($item),
            'inverter_specs' => EdeltecApiHelper::setInverterSpecs($item),
            'distributor_name' => DistributorsEnum::EDELTEC->value,
            'distributor_code' => $item['id'],
            'availability' => $availability,
            'is_active' => true,
        ];
    }

    private function updateCombination(PanelBrand $panelBrand, InverterBrand $inverterBrand, ActiveKit|null $combination): void
    {
        if (!$combination) {
            ActiveKit::create([
                'panel_brand' => $panelBrand->value,
                'inverter_brand' => $inverterBrand->value,
                'is_active' => true,
                'last_updated_time' => new Carbon(),
                'distributor' => DistributorsEnum::EDELTEC->value
            ]);
        } else {
            $combination->last_updated_time = new Carbon();
            $combination->update();
        }
    }

    private function searchCombination(PanelBrand $panelBrand, InverterBrand $inverterBrand): Model|null
    {
        return ActiveKit::query()
            ->where('panel_brand', $panelBrand->value)
            ->where('inverter_brand', $inverterBrand->value)
            ->first();
    }

    public function setProgress(int $page, $totalPages): float
    {
        return round(($page / $totalPages) * 100, 2);
    }

    private function setLogMessage(
        string $panelBrand,
        string $inverterBrand,
               $progress = null,
               $page = null,
               $totalPages = null,
               $combination = null
    ): \stdClass
    {
        $message = new \stdClass();

        $message->progress = 'Progresso de importação: '
            . '[' . $panelBrand . '/' . $inverterBrand . '] '
            . $page . ' de ' . $totalPages
            . ' (' . $progress . ' %)';

        if (!is_null($combination)) {
            $message->alreadyUpdated =
                'A combinação '
                . $panelBrand . '/' . $inverterBrand
                . ' Já foi atualizada há '
                . $this->getCombinationDiffInDays($combination)
                . ' dias.';
        }

        return $message;
    }

    private function getCombinationDiffInDays(Model $combination): int
    {
        return $combination->last_updated_time->diffInDays(now());
    }

    private function setConclusionTime(int $start_time): string
    {
        $secondsToFinish = time() - $start_time;

        return CarbonInterval::seconds($secondsToFinish)->cascade()->forHumans();
    }

    private static function responseToArray(ResponseInterface $response)
    {
        return json_decode(
            json: $response->getBody()->getContents(),
            associative: true
        );
    }

    private function inactiveKitsAndGoToNext(?ActiveKit $combination): int
    {
        if ($combination) {
            $combination->is_active = false;
            $combination->update();
        }

        return 0;
    }

    private function inactiveAllKitsBeforeUpdate(): void
    {
        $kits = $this->edeltecApiRepository->getAllActiveKits();
        InactiveKitsJob::dispatch($kits);
    }
}
