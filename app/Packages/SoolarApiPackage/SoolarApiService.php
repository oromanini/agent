<?php

namespace App\Packages\SoolarApiPackage;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;
use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class SoolarApiService
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
        try {
            $products = $this->getProduct($category, $warehouse);
            // CORREÇÃO: Acessar a chave 'products' do array retornado
            $this->soollarApiRepository->syncProducts($category, $products['products']);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao importar produtos: ' . $e->getMessage());
        }
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

        $html = $this->fetchProductPageWithAutoLogin($url);
        file_put_contents(storage_path('app/soolar_product_page.html'), $html);

        $rawProducts = $this->parseProductsFromHtml($html);
        $structuredProducts = $this->structureProductData($rawProducts, $category, $warehouse);

        return [
            'total' => count($structuredProducts),
            'products' => $structuredProducts,
        ];
    }

    private function fetchProductPageWithAutoLogin(string $url): string
    {
        if (!$this->checkIfLoggedIn()) {
            $this->login();
            if (!$this->checkIfLoggedIn()) {
                throw new \Exception("Login attempt failed. The session remains inactive. Check 'login_response.html' and your credentials.");
            }
        }
        $response = $this->client->get($url);
        return (string) $response->getBody();
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

    private function parseModuleProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): array
    {
        $originalName = $product['name'];
        $rawPrice = $product['price'];
        $finalPrice = $this->cleanPrice($rawPrice);

        $power = null;
        $brand = null;
        $model = null;

        if (preg_match('/(\d+W)/i', $originalName, $powerMatch)) {
            $power = $powerMatch[1];
            $parts = explode($power, $originalName, 2);
            $remainingName = trim($parts[1] ?? '');

            if (!empty($remainingName)) {
                $words = explode(' ', $remainingName);
                $brand = array_shift($words);
                $model = implode(' ', $words);
            }
        }

        return [
            'name' => strtolower($originalName),
            'power' => $power,
            'brand' => $brand,
            'model' => $model,
            'price' => $finalPrice,
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

        $stock = 'ready delivery';
        if (preg_match('/(\d{2}\/\d{2})/', $cleanName, $dateMatch)) {
            $stock = $dateMatch[1];
        }

        $nameWithoutStock = trim(preg_replace('/(previsão de chegada|pronta entrega).*? -/ui', '', $cleanName));

        $voltage = null;
        if (preg_match('/(\d+v)/i', $nameWithoutStock, $voltageMatch)) {
            $voltage = strtoupper($voltageMatch[1]);
        }

        $power = null;
        if (preg_match('/([\d\.]+k)/i', $nameWithoutStock, $powerMatch)) {
            $power = $powerMatch[1];
        }

        $brand = null;
        $knownBrands = ['deye', 'saj', 'sungrow', 'solis'];
        foreach ($knownBrands as $knownBrand) {
            if (stripos($nameWithoutStock, $knownBrand) !== false) {
                $brand = strtoupper($knownBrand);
                break;
            }
        }

        $model = trim(preg_replace('/^(micro-inversor|micro inversor|inversor|inv)\s*/i', '', $nameWithoutStock));

        return [
            'type' => 'inverter',
            'name' => $cleanName,
            'model' => $model,
            'brand' => $brand,
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
        $cleanName = trim(preg_replace('/^(conector)\s*/i', '', $originalName));

        return [
            'name' => strtolower($cleanName),
            'price' => $this->cleanPrice($product['price']),
            'stock' => 'pronta entrega',
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }

    private function parseStructureProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): ?array
    {
        $originalName = $product['name'];

        if (stripos($originalName, 'kit') === false) {
            return null;
        }

        $stock = 'ready delivery';
        if (preg_match('/(\d{2}\/\d{2})/', $originalName, $dateMatch)) {
            $stock = $dateMatch[1];
        }

        $nameWithoutStock = trim(preg_replace('/(previsão de chegada|pronta entrega).*? -/ui', '', $originalName));
        $model = trim(preg_replace('/kit fixação/i', '', $nameWithoutStock));

        return [
            'name' => strtolower($originalName),
            'model' => strtolower(trim($model)),
            'price' => $this->cleanPrice($product['price']),
            'stock' => $stock,
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }

    private function parseCableProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): array
    {
        $originalName = $product['name'];
        $nameWithoutPrefix = trim(preg_replace('/^(cabo solar)\s*/i', '', $originalName));

        $parts = explode('-', $nameWithoutPrefix);
        $size = trim(end($parts));

        $modelAndType = trim($parts[0]);
        $modelAndTypeParts = explode(' ', $modelAndType);
        $type = trim(end($modelAndTypeParts));

        array_pop($modelAndTypeParts);
        $model = trim(implode(' ', $modelAndTypeParts));

        $stock = 'ready delivery';
        if (preg_match('/(\d{2}\/\d{2})/', $originalName, $dateMatch)) {
            $stock = $dateMatch[1];
        }

        return [
            'name' => strtolower($originalName),
            'model' => $model,
            'size' => $size,
            'type' => $type,
            'price' => $this->cleanPrice($product['price']),
            'stock' => $stock,
            'distribution_center' => $warehouse->value,
            'category' => $category->value,
        ];
    }

    private function parseDefaultProduct(array $product, WarehouseEnum $warehouse, ProductCategoriesEnum $category): array
    {
        return [
            'name' => strtolower($product['name']),
            'power' => null,
            'brand' => null,
            'model' => null,
            'price' => $this->cleanPrice($product['price']),
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

    private function checkIfLoggedIn(): bool
    {
        $response = $this->client->get(self::BASE_URL . 'customer/profile');
        $html = (string) $response->getBody();
        return str_contains($html, 'Sair') || str_contains($html, 'Meus Pedidos');
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

        return new Client([
            'cookies' => $cookieJar,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
            ],
            'timeout' => 30.0,
        ]);
    }
}
