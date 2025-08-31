<?php

namespace App\Packages\SoolarApiPackage;

use App\Packages\SoolarApiPackage\Auth\AuthManager;
use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\Parsers\CableParser;
use App\Packages\SoolarApiPackage\Parsers\ConnectorParser;
use App\Packages\SoolarApiPackage\Parsers\HtmlParser;
use App\Packages\SoolarApiPackage\Parsers\InverterParser;
use App\Packages\SoolarApiPackage\Parsers\ModuleParser;
use App\Packages\SoolarApiPackage\Parsers\StructureParser;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;

class SoollarApiManager
{
    private const BASE_URL = 'https://www.soollar.com.br/';
    private const LOGIN_URI = 'login_check';
    private const LOGIN_PAGE_URI = 'customer/login';

    public function __construct(
        private readonly SoollarApiRepository $soollarApiRepository,
        private readonly HtmlParser $htmlParser,
        private readonly AuthManager $authManager,
        private readonly ModuleParser $moduleParser,
        private readonly InverterParser $inverterParser,
        private readonly CableParser $cableParser,
        private readonly ConnectorParser $connectorParser,
        private readonly StructureParser $structureParser,
    ) {}

    public function handle(ProductCategoriesEnum $category, WarehouseEnum $warehouse): void
    {
        $products = $this->getProduct($category, $warehouse);
        $this->soollarApiRepository->syncProducts($category, $warehouse, $products['products']);
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

        $html = $this->authManager->fetchPageAndHandleLogin($url);
        file_put_contents(storage_path('app/soolar_product_page.html'), $html);

        $rawProducts = $this->htmlParser->parseProductsFromHtml($html);
        $structuredProducts = $this->structureProductData($rawProducts, $category, $warehouse);

        return [
            'total' => count($structuredProducts),
            'products' => $structuredProducts,
        ];
    }


    private function structureProductData(
        array $rawProducts,
        ProductCategoriesEnum $category,
        WarehouseEnum $warehouse
    ): array {

        $structuredProducts = [];
        foreach ($rawProducts as $product) {

            if (!$this->isReadyDelivery($product['name'])) {
                continue;
            }

            $structuredProduct = null;

            switch ($category) {
                case ProductCategoriesEnum::MODULO:
                    $structuredProduct = $this->moduleParser->parseModuleProduct($product, $warehouse, $category);
                    break;
                case ProductCategoriesEnum::INVERSOR:
                    $structuredProduct = $this->inverterParser->parseInverterProduct($product, $warehouse, $category);
                    break;
                case ProductCategoriesEnum::CONECTOR:
                    $structuredProduct = $this->connectorParser->parseConnectorProduct($product, $warehouse, $category);
                    break;
                case ProductCategoriesEnum::ESTRUTURA:
                    $structuredProduct = $this->structureParser->parseStructureProduct($product, $warehouse, $category);
                    break;
                case ProductCategoriesEnum::CABO:
                    $structuredProduct = $this->cableParser->parseCableProduct($product, $warehouse, $category);
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

    public static function getDeliveryStock(string $productName): string
    {
        $cleanName = strtolower(self::removeAccents($productName));

        if (str_contains($cleanName, 'pronta entrega') || str_contains($cleanName, 'ready delivery')) {
            return 'ready delivery';
        }

        if (preg_match('/(\d{2}\/\d{2})/', $cleanName, $dateMatch)) {
            return $dateMatch[1];
        }

        return 'unknown';
    }

    private static function removeAccents(string $string): string
    {
        return str_replace(
            ['á', 'à', 'ã', 'â', 'é', 'ê', 'í', 'ó', 'ô', 'õ', 'ú', 'ü', 'ç', 'Á', 'À', 'Ã', 'Â', 'É', 'Ê', 'Í', 'Ó', 'Ô', 'Õ', 'Ú', 'Ü', 'Ç'],
            ['a', 'a', 'a', 'a', 'e', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'c', 'A', 'A', 'A', 'A', 'E', 'E', 'I', 'O', 'O', 'O', 'U', 'U', 'C'],
            $string
        );
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

    public static function cleanPrice(string $rawPrice): ?float
    {
        if ($rawPrice !== 'Unavailable' && preg_match('/R\$\s*[\d\.]+\,\d{2}/', $rawPrice, $matches)) {
            $priceString = $matches[0];
            $numericPrice = preg_replace('/[^\d,]/', '', $priceString);
            return (float) str_replace(',', '.', $numericPrice);
        }
        return null;
    }
}
