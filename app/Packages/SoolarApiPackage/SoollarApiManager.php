<?php

namespace App\Packages\SoolarApiPackage;

use App\Packages\SoolarApiPackage\Enums\CommonInverterBrandsEnum;
use App\Packages\SoolarApiPackage\Enums\CommonModuleBrandsEnum;
use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;
use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class SoollarApiManager
{
    private const BASE_URL = 'https://www.soollar.com.br/';
    private const LOGIN_URI = 'login_check';
    private const LOGIN_PAGE_URI = 'customer/login';
    private Client $client;

    public function __construct(private readonly SoollarApiRepository $soollarApiRepository)
    {
        $this->client = $this->getClient();
    }

    public function handle(ProductCategoriesEnum $category, WarehouseEnum $warehouse): void
    {
        $products = $this->getProduct($category, $warehouse);
        $this->soollarApiRepository->syncProducts($category, $products['products']);
    }

    private function getProduct(ProductCategoriesEnum $category, WarehouseEnum $warehouse): array
    {
        $pathCategory = $category->value;
        $filter = null;

        if ($category === ProductCategoriesEnum::CONECTOR) {
            $pathCategory = 'Stringbox';
            $filter = 'CONECTORES|MC4';
        }

        $url = self::BASE_URL
            . "loja/"
            . $warehouse->value
            . "/produto/listar/"
            . $pathCategory;

        if ($filter) {
            $url .= "?filter=" . $filter;
        }

        $html = $this->fetchPageAndHandleLogin($url);
        file_put_contents(storage_path('app/soolar_product_page.html'), $html);

        $rawProducts = $this->parseProductsFromHtml($html);
        $structuredProducts = $this->structureProductData($rawProducts, $category, $warehouse);

        return [
            'total' => count($structuredProducts),
            'products' => $structuredProducts,
        ];
    }

    private function fetchPageAndHandleLogin(string $url): string
    {
        $response = $this->client->get($url);
        $html = (string) $response->getBody();

        if (str_contains($html, 'Acesse a sua conta') || str_contains($html, '_username')) {
            $this->login();

            $response = $this->client->get($url);
            $html = (string) $response->getBody();
        }

        return $html;
    }

    private function login(): void
    {
        $this->client->get(self::BASE_URL . self::LOGIN_PAGE_URI);
        $credentials = $this->getCredentials();
        $formParams = [
            '_username' => $credentials['username'],
            '_password' => $credentials['password'],
        ];

        $this->client->post(self::BASE_URL . self::LOGIN_URI, [
            'form_params' => $formParams,
            'allow_redirects' => true,
            'headers' => [
                'Referer' => self::BASE_URL . self::LOGIN_PAGE_URI,
                'Origin' => 'https://www.soollar.com.br',
            ],
        ]);
    }

    private function parseProductsFromHtml(string $html): array
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);

        $xpath = new DOMXPath($dom);

        $products = [];
        $items = $xpath->query("//div[contains(@class, 'variant')]");

        foreach ($items as $itemNode) {
            $xpathDoc = new DOMXPath($itemNode->ownerDocument);
            $nameNode = $xpathDoc->query(".//a[contains(@class, 'variant-product-name')]", $itemNode)->item(0);
            $priceNode = $xpathDoc->query(".//p[contains(@class, 'variant-final-price')] | .//div[contains(@class, 'price')] | .//span[contains(@class, 'price')]", $itemNode)->item(0);

            $name = $nameNode ? trim($nameNode->textContent) : null;
            $price = $priceNode ? trim($priceNode->textContent) : 'Unavailable';

            if ($name) {
                $products[] = [
                    'name' => $name,
                    'price' => $price,
                ];
            }
        }
        return $products;
    }

    private function structureProductData(array $rawProducts, ProductCategoriesEnum $category, WarehouseEnum $warehouse): array
    {
        $structuredProducts = [];
        foreach ($rawProducts as $product) {

            if (!$this->isReadyDelivery($product['name'])) {
                continue;
            }

            $structuredProduct = null;

            switch ($category) {
                case ProductCategoriesEnum::MODULO:
                    $structuredProduct = $this->parseModuleProduct($product, $warehouse, $category);
                    break;
                case ProductCategoriesEnum::INVERSOR:
                    $structuredProduct = $this->parseInverterProduct($product, $warehouse, $category);
                    break;
                case ProductCategoriesEnum::CONECTOR:
                    $structuredProduct = $this->parseConnectorProduct($product, $warehouse, $category);
                    break;
                case ProductCategoriesEnum::ESTRUTURA:
                    $structuredProduct = $this->parseStructureProduct($product, $warehouse, $category);
                    break;
                case ProductCategoriesEnum::CABO:
                    $structuredProduct = $this->parseCableProduct($product, $warehouse, $category);
                    break;
                default:
                    $structuredProduct = $this->parseDefaultProduct($product, $warehouse, $category);
                    break;
            }
            if ($structuredProduct !== null) {
                $structuredProducts[] = $structuredProduct;
            }
        }
        return $structuredProducts;
    }

    private function isReadyDelivery(string $productName): bool
    {
        $cleanName = strtolower($this->removeAccents($productName));
        $nonReadyTerms = ['previsao de entrega', 'previsao de chegada'];
        foreach ($nonReadyTerms as $term) {
            if (str_contains($cleanName, $term)) {
                return false;
            }
        }
        return true;
    }

    private function removeAccents(string $string): string
    {
        return str_replace(
            ['á', 'à', 'ã', 'â', 'é', 'ê', 'í', 'ó', 'ô', 'õ', 'ú', 'ü', 'ç', 'Á', 'À', 'Ã', 'Â', 'É', 'Ê', 'Í', 'Ó', 'Ô', 'Õ', 'Ú', 'Ü', 'Ç'],
            ['a', 'a', 'a', 'a', 'e', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'c', 'A', 'A', 'A', 'A', 'E', 'E', 'I', 'O', 'O', 'O', 'U', 'U', 'C'],
            $string
        );
    }

    private function getDeliveryStock(string $productName): string
    {
        $cleanName = strtolower($this->removeAccents($productName));

        if (str_contains($cleanName, 'pronta entrega') || str_contains($cleanName, 'ready delivery')) {
            return 'ready delivery';
        }

        if (preg_match('/(\d{2}\/\d{2})/', $cleanName, $dateMatch)) {
            return $dateMatch[1];
        }

        return 'unknown';
    }

    private function parseModuleProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): array
    {
        $originalName = $product['name'];
        $rawPrice = $product['price'];
        $cleanName = strtolower($originalName);

        $power = null;
        $brand = null;
        $model = null;

        $knownBrands = array_map('strtolower', array_column(CommonModuleBrandsEnum::cases(), 'value'));

        if (preg_match('/(\d+)\s*w/i', $cleanName, $powerMatch)) {
            $power = (int)$powerMatch[1];
            $remainingName = trim(str_ireplace($powerMatch[0], '', $cleanName));

            foreach ($knownBrands as $knownBrand) {
                if (str_contains($remainingName, $knownBrand)) {
                    $brand = $knownBrand;
                    $remainingName = trim(str_ireplace($knownBrand, '', $remainingName));
                    break;
                }
            }

            $model = trim(preg_replace('/^(r\$|previsão de entrega|previsão de chegada|e|pronta entrega).*?-?\s*/i', '', $remainingName));
            $model = trim(preg_replace('/\s+/', ' ', $model));
        }

        return [
            'name' => $originalName,
            'power' => $power,
            'brand' => strtoupper($brand ?? ''),
            'model' => strtolower($model ?? ''),
            'price' => $this->cleanPrice($rawPrice),
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }

    private function parseInverterProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): array
    {
        $originalName = $product['name'];
        $rawPrice = $product['price'];
        $cleanName = strtolower($originalName);

        if (stripos($cleanName, 'garantia total para inversores') !== false) {
            $model = null;
            if (preg_match('/(\d+\s*anos)/i', $cleanName, $durationMatch)) {
                $model = $durationMatch[1];
            }
            return [
                'type' => 'warranty',
                'name' => $cleanName,
                'model' => $model,
                'brand' => null,
                'price' => $this->cleanPrice($rawPrice),
                'power' => null,
                'voltage' => null,
                'stock' => 'pronta entrega',
                'distribution_center' => $warehouse->value,
                'category' => $category->value,
            ];
        }

        $stock = $this->getDeliveryStock($originalName);

        $nameWithoutStock = trim(preg_replace('/(previsão de chegada|pronta entrega).*? -/ui', '', $cleanName));

        $voltage = '220V';
        if (preg_match('/(\d{3,4}v)/i', $nameWithoutStock, $voltageMatch)) {
            $voltage = strtoupper($voltageMatch[1]);
        }

        $power = null;
        if (preg_match('/([\d\.\,]+)\s*k/i', $nameWithoutStock, $powerMatch)) {
            $power = (float) str_replace(',', '.', $powerMatch[1]);
        }

        $knownBrands = array_map('strtolower', array_column(CommonInverterBrandsEnum::cases(), 'value'));
        $brand = null;
        foreach ($knownBrands as $knownBrand) {
            if (str_contains($nameWithoutStock, $knownBrand)) {
                $brand = $knownBrand;
                break;
            }
        }

        $modelName = $nameWithoutStock;
        if ($brand) {
            $modelName = str_ireplace($brand, '', $modelName);
        }
        if ($power) {
            $modelName = str_ireplace((string)$power . 'k', '', $modelName);
        }
        if ($voltage) {
            $modelName = str_ireplace($voltage, '', $modelName);
        }

        $model = trim(preg_replace('/^(micro-inversor|micro inversor|inversor|inv|garantia total|em estoque|no stock|\s*-\s*.*$)/i', '', $modelName));
        $model = trim(preg_replace('/\s+/', ' ', $model));

        return [
            'type' => 'inverter',
            'name' => $cleanName,
            'model' => $model,
            'brand' => $brand ? strtoupper($brand) : null,
            'price' => $this->cleanPrice($rawPrice),
            'power' => $power,
            'voltage' => $voltage,
            'stock' => $stock,
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }

    private function parseConnectorProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): array
    {
        $originalName = $product['name'];
        $rawPrice = $product['price'];
        $cleanName = trim(preg_replace('/^(conector)\s*/i', '', $originalName));

        return [
            'name' => strtolower($cleanName),
            'price' => $this->cleanPrice($rawPrice),
            'stock' => $this->getDeliveryStock($originalName),
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }

    private function parseStructureProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): ?array
    {
        $originalName = $product['name'];
        $rawPrice = $product['price'];

        if (stripos($originalName, 'kit') === false) {
            return null;
        }

        $nameWithoutStock = trim(preg_replace('/(previsão de chegada|pronta entrega).*? -/ui', '', $originalName));
        $model = trim(preg_replace('/kit fixação/i', '', $nameWithoutStock));

        return [
            'name' => strtolower($originalName),
            'model' => strtolower(trim($model)),
            'price' => $this->cleanPrice($rawPrice),
            'stock' => 'unknown',
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }

    private function parseCableProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): array
    {
        $originalName = $product['name'];
        $rawPrice = $product['price'];
        $cleanName = strtolower($originalName);
        $model = null;
        $size = null;
        $type = null;

        if (preg_match('/(\d+mm)/', $cleanName, $modelMatch)) {
            $model = $modelMatch[1];
        }

        if (preg_match('/(\d+mt)/', $cleanName, $sizeMatch)) {
            $size = $sizeMatch[1];
        }

        if (str_contains($cleanName, 'preto')) {
            $type = 'PRETO';
        } elseif (str_contains($cleanName, 'vermelho')) {
            $type = 'VERMELHO';
        }

        $stock = $this->getDeliveryStock($originalName);

        return [
            'name' => strtolower($originalName),
            'model' => $model,
            'size' => $size,
            'type' => $type,
            'price' => $this->cleanPrice($rawPrice),
            'stock' => $stock,
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }

    private function parseDefaultProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): array
    {
        $rawPrice = $product['price'];

        return [
            'name' => strtolower($product['name']),
            'power' => null,
            'brand' => null,
            'model' => null,
            'price' => $this->cleanPrice($rawPrice),
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }

    private function cleanPrice(string $rawPrice): ?float
    {
        if ($rawPrice !== 'Unavailable' && preg_match('/R\$\s*[\d\.]+\,\d{2}/', $rawPrice, $matches)) {
            $priceString = $matches[0];
            $numericPrice = preg_replace('/[^\d,]/', '', $priceString);
            return (float) str_replace(',', '.', $numericPrice);
        }
        return null;
    }

    private function getCredentials(): array
    {
        $path = base_path('app/Packages/SoolarApiPackage/security/credentials.json');
        if (!File::exists($path)) {
            throw new \Exception("Credentials file not found at: {$path}");
        }
        $content = File::get($path);
        $data = json_decode($content, true);
        if (!$data || !isset($data['username'], $data['password'])) {
            throw new \Exception("Malformed or incomplete credentials in JSON file.");
        }
        return $data;
    }

    private function getClient(): Client
    {
        $cookiePath = storage_path('app/soolar_cookie.json');
        if (!File::isDirectory(dirname($cookiePath))) {
            File::makeDirectory(dirname($cookiePath), 0755, true, true);
        }
        $cookieJar = new FileCookieJar($cookiePath, true);

        $stack = HandlerStack::create();

        $stack->push(Middleware::retry(
            function ($retries, Request $request, Response $response = null, $exception = null) {
                // Tenta de novo até 3 vezes
                if ($retries >= 2) {
                    return false;
                }
                if ($response && $response->getStatusCode() >= 500) {
                    return true;
                }
                if ($exception instanceof \GuzzleHttp\Exception\ConnectException) {
                    return true;
                }
                return false;
            },
            function ($retries) {
                return 1000 * pow(2, $retries); // Espera 2s, depois 4s
            }
        ));

        return new Client([
            'handler' => $stack, // Usa o handler com o middleware
            'cookies' => $cookieJar,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
            ],
            'timeout' => 30.0,
        ]);
    }
}
