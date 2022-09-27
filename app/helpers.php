<?php

use App\Enums\InverterBrands;
use App\Enums\PanelBrands;
use App\Enums\RoofStructure;
use App\Http\Controllers\KitSearchController;
use App\Models\Address;
use App\Models\City;
use App\Models\User;
use App\Services\KitSearchService;
use App\Services\PricingService;
use App\Services\ProposalService;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

function stringMoneyToFloat($money): float
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


function setPanelBrandImage($brand): string
{
    $img = '';
    $jinko = '/img/panel_brands/jinko.png';
    $sunket = '/img/panel_brands/sunket.png';
    $trina = '/img/panel_brands/trina.png';
    $dah = '/img/panel_brands/dah.png';
    $astronergy = '/img/panel_brands/astronergy.png';
    $ja = '/img/panel_brands/ja.png';
    $phono = '/img/panel_brands/phono.png';
    $longi = '/img/panel_brands/longi.png';

    if (is_string($brand)) {

        if ($brand == 'Jinko') {
            $img = $jinko;
        } elseif ($brand == 'Trina') {
            $img = $trina;
        } elseif ($brand == 'DAH Solar') {
            $img = $dah;
        } elseif ($brand == 'Sunket') {
            $img = $sunket;
        } elseif ($brand == 'Astronergy') {
            $img = $astronergy;
        } elseif ($brand == 'Ja') {
            $img = $ja;
        } elseif ($brand == 'Phono') {
            $img = $phono;
        } elseif ($brand == 'Longi') {
            $img = $longi;
        }

    } else {

        switch ($brand) {
            case 1:
                $img = $jinko;
                break;
            case 2:
                $img = $sunket;
                break;
            case 3:
                $img = $trina;
                break;
            case 4:
                $img = $dah;
                break;
            case 5:
                $img = $astronergy;
                break;
            case 6:
                $img = $ja;
                break;
            case 7:
                $img = $phono;
                break;
            case 8:
                $img = $longi;
                break;
            default:
                throw new Exception('Painel não localizado.');
        }
    }
    return $img;
}

function setInverterImage($brand): string
{
    $img = '';
    $growatt = '/img/inverters/growatt.png';
    $chint = '/img/inverters/chint.png';
    $deye = '/img/inverters/deye.png';
    $sofar = '/img/inverters/sofar.png';
    $solis = '/img/inverters/solis.png';

    if (is_string($brand)) {

        if ($brand == 'Growatt') {
            $img = $growatt;
        } elseif ($brand == 'Chint') {
            $img = $chint;
        } elseif ($brand == 'Deye') {
            $img = $deye;
        } elseif ($brand == 'Sofar') {
            $img = $sofar;
        } elseif ($brand == 'Solis') {
            $img = $sofar;
        }

    } else {

        switch ($brand) {
            case 1:
                $img = $growatt;
                break;
            case 2:
                $img = $chint;
                break;
            case 3:
                $img = $deye;
                break;
            case 4:
                $img = $sofar;
                break;
            case 5:
                $img = $solis;
                break;
            default:
                throw new Exception('Inversor não localizado.');
        }
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
        return 50;
    } elseif ($proposal->tension_pattern == 'BI-220') {
        return 75;
    }

    return 100;
}


function formatFloat($float): float
{
    return round($float, 2);
}

function paybackToString(float $float): string
{

    $whole = intval($float);
    $decimal = $float - $whole;

    return $whole . ' anos e ' . $decimal * 10 . ' mes(es)';
}

function formatTension($tension): string
{
    if ($tension == 'Mono220' || $tension == 'MONOFASICO-220V') {
        return 'MONO-220';
    } elseif ($tension == 'Bi220' || $tension == 'BIFASICO-220V') {
        return 'BI-220';
    } elseif ($tension == 'Tri220' || $tension == 'TRIFASICO-220V') {
        return 'TRI-220';
    }

    return 'TRI-380';

}

function jsonToArray($json)
{
    return json_decode($json, true);
}

function getNameAndFederalUnit(int $cityId)
{
    return City::find($cityId)->name_and_federal_unit;
}

function getAscendantName(int $ascendantId): string
{

    if ($ascendantId != 0) {
        return User::find($ascendantId)->name;
    }

    return 'Sem ascendente';
}

function kitByUuid($kit_uuid)
{

    $kitService = new KitSearchService();

    return $kitService->getKitByUuid($kit_uuid);
}

function setFinalPrice($data): float
{
    $pricingService = new PricingService();
    return $pricingService->calculateFinalPrice($data);
}

function formatTensionToEnum($tensionPattern): string
{
    if ($tensionPattern == 'MONOFASICO-220V') {
        return 'MONO-220';
    } elseif ($tensionPattern == 'BIFASICO-220V') {
        return 'BI-220';
    } elseif ($tensionPattern == 'TRIFASICO-220V') {
        return 'TRI-220';
    } else {
        return 'TRI-380';
    }

}


function getKitCodesFromProposal($proposal)
{

    $kits = explode(';', $proposal->kit_uuid);

    foreach ($kits as $key => $kit) {

        $kit = str_replace('"', '', $kit);
        $kits[$key] = str_replace('"', '', $kit);

        if (!Uuid::isValid($kit)) {
            unset($kits[$key]);
        }
    }

    return $kits;

}

function setStringFromAddress(Address $address): string
{
    return $address->street . ', ' . $address->number . ', ' . $address->neighborhood . ', ' . $address->city->name_and_federal_unit . ', ' . $address->zipcode;
}
