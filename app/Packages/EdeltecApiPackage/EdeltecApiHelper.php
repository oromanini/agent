<?php

namespace App\Packages\EdeltecApiPackage;

use App\Packages\EdeltecApiPackage\Enums\InverterBrand;
use App\Packages\EdeltecApiPackage\Enums\PanelBrand;
use DOMDocument;

class EdeltecApiHelper
{
    public static function getPanelModel(string $panelData): string
    {
        preg_match('/^(.*?)(?=\s*<br>)/', $panelData, $panelModel);
        return $panelModel[1];
    }

    public static function getPanelEfficiency(string $panelData): string
    {
        preg_match('/Eficiência: (\d+\.\d+) %/', $panelData, $panelEfficiency);
        return $panelEfficiency[1]  ?? 'N/I';
    }

    public static function getInverterModel(string $inverterData): string
    {
        preg_match('/MODELO\s(.*?)<br>/', $inverterData, $inverterModel);

        if (!$inverterModel) {
            $firstSentence = strtok($inverterData, '<br>');

            return $firstSentence;
        }

        return $inverterModel[1];
    }

    public static function getPanelWarranty(PanelBrand $panelBrand): int
    {
        return match ($panelBrand) {
            PanelBrand::OSDA, PanelBrand::HONOR => 15,
            default => 12,
        };
    }

    public static function getPanelLinearWarranty(PanelBrand $panelBrand): int
    {
        return match ($panelBrand) {
            PanelBrand::SINE, PanelBrand::RESUN => 25,
            PanelBrand::OSDA, PanelBrand::HONOR => 30,
        };
    }

    public static function getInverterWarranty(InverterBrand $inverterBrand): int
    {
        return match ($inverterBrand) {
            InverterBrand::SAJ,
            InverterBrand::GROWATT,
            InverterBrand::DEYE,
            InverterBrand::SUNGROW => 10,
        };
    }

    public static function getPanelLogo(string $brand): string
    {
        return match ($brand) {
            PanelBrand::SINE->value => '/EdeltecApiPackage/img/panels/sine.png',
            PanelBrand::HONOR->value => '/EdeltecApiPackage/img/panels/honor.png',
            PanelBrand::OSDA->value => '/EdeltecApiPackage/img/panels/osda.png',
            PanelBrand::RESUN->value => '/EdeltecApiPackage/img/panels/resun.png',
        };
    }

    public static function getInverterLogo(string $brand): string
    {
        return match ($brand) {
            InverterBrand::SAJ->value => '/EdeltecApiPackage/img/inverters/saj.png',
            InverterBrand::DEYE->value => '/EdeltecApiPackage/img/inverters/deye.png',
            InverterBrand::SUNGROW->value => '/EdeltecApiPackage/img/inverters/sungrow.png',
            InverterBrand::GROWATT->value => '/EdeltecApiPackage/img/inverters/growatt.png',
        };
    }

    public static function getComponents(string $components): array
    {
        $result = [];

        $dom = new DOMDocument();
        @$dom->loadHTML($components);

        $trElements = $dom->getElementsByTagName('tr');

        foreach ($trElements as $trElement) {
            $tdElements = $trElement->getElementsByTagName('td');

            if ($tdElements->length >= 3) {
                $sku = (int) trim($tdElements->item(0)->nodeValue);
                $quantidade = (int) trim($tdElements->item(1)->nodeValue);
                $descricao = trim($tdElements->item(2)->nodeValue);

                $result[] = [
                    'sku' => $sku,
                    'quantidade' => $quantidade,
                    'descrição' => $descricao
                ];
            }
        }

        foreach ($result as $item) {
            $quantidade = $item['quantidade'];
            $descricao = $item['descrição'];

            $optimizedArray[] = "{$quantidade} {$descricao}";
        }

        return $optimizedArray;
    }

    public static function setPanelSpecs(array $item): string
    {
        $panel = PanelBrand::matchCases($item['marca']);

        return json_encode([
            'model' => self::getPanelModel($item['caracteristicasModulo']),
            'logo' => self::getPanelLogo($item['marca']),
            'efficiency' => self::getPanelEfficiency($item['caracteristicasModulo']),
            'warranty' => self::getPanelWarranty($panel),
            'linear_warranty' => self::getPanelLinearWarranty($panel),
        ]);
    }

    public static function setInverterSpecs(array $item): string
    {
        $inverter = InverterBrand::tryFrom($item['fabricante']);

        return json_encode([
            'marca' => $item['fabricante'],
            'model' => self::getInverterModel($item['caracteristicasInversor']),
            'logo' => self::getInverterLogo($item['fabricante']),
            'warranty' => self::getInverterWarranty($inverter),
        ]);
    }
}
