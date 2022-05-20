<?php

use App\Enums\InverterBrands;
use App\Enums\PanelBrands;
use App\Enums\RoofStructure;

function stringMoneyToFloat(string $money): float
{
    $float = str_replace('.', '', $money);

    return (float)str_replace(',', '.', $float);
}

function floatToMoney(float $money): string
{
    return number_format($money, 2, ',', '.');
}

function setRoofs(): array
{
    return [
        [
            'id' => RoofStructure::Colonial,
            'image' => '/img/roofs/colonial.png',
            'description' => 'Colonial'
        ],
        [
            'id' => RoofStructure::Trapezoidal,
            'image' => '/img/roofs/trapezoidal.png',
            'description' => 'Trapezoidal'
        ],
        [
            'id' => RoofStructure::Laje,
            'image' => '/img/roofs/laje.png',
            'description' => 'Laje'
        ],
        [
            'id' => RoofStructure::ParafMadeira,
            'image' => '/img/roofs/paraf-madeira.png',
            'description' => 'Parafuso Madeira'
        ],
        [
            'id' => RoofStructure::ParafMetal,
            'image' => '/img/roofs/paraf-metal.png',
            'description' => 'Parafuso Metal'
        ],
        [
            'id' => RoofStructure::Solo,
            'image' => '/img/roofs/solo.png',
            'description' => 'Solo'
        ],
        [
            'id' => RoofStructure::Ondulada,
            'image' => '/img/roofs/ondulada.png',
            'description' => 'Ondulada'
        ],
    ];
}


function setPanelBrandImage($id): string
{
    $img = '';

    switch ($id) {
        case 1:
            $img = '/img/panel_brands/jinko.png';
            break;
        case 2:
            $img = '/img/panel_brands/sunket.png';
            break;
        case 3:
            $img = '/img/panel_brands/trina.png';
            break;
        default:
            throw new Exception('Painel não localizado.');
    }

    return $img;
}

function setInverterImage($id): string
{
    $img = '';

    switch ($id) {
        case 1:
            $img = '/img/inverters/growatt.png';
            break;
        case 2:
            $img = '/img/inverters/chint.png';
            break;
        case 3:
            $img = '/img/inverters/deye.png';
            break;
        default:
            throw new Exception('Inversor não localizado.');
    }

    return $img;
}

function calculateWithoutSolar($proposal): string
{

    return floatToMoney($proposal->average_consumption * $proposal->kw_price);
}


function calculateWithSolar($proposal): string
{
    if ($proposal->tension_pattern == 'MONO-220') {
        return floatToMoney(75);
    } elseif ($proposal->tension_pattern == 'BI-220') {
        return floatToMoney(95);
    }

    return floatToMoney(125);
}
