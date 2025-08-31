<?php

namespace App\Packages\SoolarApiPackage\Parsers;

use DOMDocument;
use DOMXPath;

class HtmlParser
{
    public function parseProductsFromHtml(string $html): array
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

}
