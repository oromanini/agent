<?php

use App\Enums\InverterBrands;
use App\Enums\PanelBrands;
use App\Enums\RoofStructure;
use App\Http\Controllers\KitSearchController;
use App\Models\Address;
use App\Models\City;
use App\Models\Proposal;
use App\Models\User;
use App\Services\KitSearchService;
use App\Services\PricingService;
use App\Services\ProposalService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

function stringMoneyToFloat($money): float
{
    $float = str_replace('.', '', $money);

    return (float)str_replace(',', '.', $float);
}

function stringInverterPowerToFloat($inverterPower): float
{
    return (float)str_replace(
        ',',
        '.',
        $inverterPower
    );
}

function floatToMoney(float $money): string
{
    return number_format($money, 2, ',', '.');
}


function setPanelBrandImage($brand): string
{
    return match ($brand) {
        'Jinko', PanelBrands::Jinko->value => '/img/panel_brands/jinko.png',
        'Trina', PanelBrands::Trina->value => '/img/panel_brands/trina.png',
        'DAH Solar', PanelBrands::DAH->value => '/img/panel_brands/dah.png',
        'Ja', PanelBrands::Ja->value => '/img/panel_brands/ja.png',
        'Phono', PanelBrands::Phono->value => '/img/panel_brands/phono.png',
        'Longi', PanelBrands::Longi->value => '/img/panel_brands/longi.png',
        'Sunova', 'SUNOVA', PanelBrands::Sunova->value => '/img/panel_brands/sunova.png',
        'Osda', PanelBrands::Osda->value => '/img/panel_brands/osda.png',
        'Ae_solar', PanelBrands::Ae_Solar->value => '/img/panel_brands/ae_solar.png',
        'Znshine', PanelBrands::ZNShine->value => '/img/panel_brands/znshine.png',
        'Astroenergy', PanelBrands::Astronergy->value => '/img/panel_brands/astronergy.png',
        'Pulling', 'PULLING', PanelBrands::Pulling->value => '/img/panel_brands/pulling.png',
        'Hanersun', PanelBrands::Hanersun->value => '/img/panel_brands/hanersun.png',
        'Resun', 'RESUN', PanelBrands::Resun->value => '/img/panel_brands/resun.png',
        'Sine', 'SINE', PanelBrands::Sine->value => '/img/panel_brands/sine.png',
        'Era', PanelBrands::Era->value, 'ERA' => '/img/panel_brands/era.png',
        'Honor', 'HONOR', PanelBrands::Honor->value => '/img/panel_brands/honor.png',
        'Runergy', PanelBrands::Runergy->value => '/img/panel_brands/runergy.png',
        default => throw new Exception('Painel não localizado.'),
    };
}

function setInverterImage($brand): string
{
    $img = '';
    $growatt = '/img/inverters/growatt.png';
    $chint = '/img/inverters/chint.png';
    $deye = '/img/inverters/deye.png';
    $deyeString = '/img/inverters/deye-string.png';
    $sofar = '/img/inverters/sofar.png';
    $solis = '/img/inverters/solis.png';
    $solplanet = '/img/inverters/solplanet.png';
    $bel = '/img/inverters/bel.png';
    $sungrow = '/img/inverters/sungrow.png';
    $canadian = '/img/inverters/canadian.png';
    $saj = '/img/inverters/saj.png';
    $techpower = '/img/inverters/techpower.png';
    $sajMicro = '/img/inverters/saj_micro.png';

    return match ($brand) {
        InverterBrands::Growatt->value, 'Growatt', 'GROWATT' => $growatt,
        InverterBrands::Chint->value, 'Chint', 'CHING' => $chint,
        InverterBrands::DeyeMicro->value, 'Deye', 'DEYE' => $deye,
        InverterBrands::DeyeString->value, 'DeyeString', 'DEYESTRING' => $deyeString,
        InverterBrands::Sofar->value, 'Sofar', 'SOFAR' => $sofar,
        InverterBrands::Solis->value, 'Solis', 'SOLIS' => $solis,
        InverterBrands::Bel->value, 'Bel', 'BEL' => $bel,
        InverterBrands::Sungrow->value, 'Sungrow', 'SUNGROW' => $sungrow,
        InverterBrands::Canadian->value, 'Canadian', 'CANADIAN' => $canadian,
        InverterBrands::Solplanet->value, 'Solplanet', 'SOLPLANET' => $solplanet,
        InverterBrands::Saj->value, 'Saj', 'SAJ' => $saj,
        InverterBrands::TechPowerMicro->value, 'TechPowerMicro' => $techpower,
        InverterBrands::SajMicroinverter->value, 'SajMicroinverter' => $sajMicro,
        default => throw new Exception('Inversor não localizado.'),
    };
}


function formatFloat($float): float
{
    return round($float, 2);
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

function setStringFromAddress(Address $address): string
{
    return $address->street
        . ', ' . $address->number
        . ', ' . $address->neighborhood
        . ', ' . $address->city->name_and_federal_unit
        . ', ' . $address->zipcode;
}

function isApproved($status): string
{
    return match ($status) {
        'Aguardando', 'Em análise', 'Aguardando fotos agente', 'Aguardando assinatura' => '',
        'Finalizado', 'Aprovado', 'Aprovado com adequação', 'À Vista', 'Cartão', '60/40' => 'is-success',
        'reprovado' => 'is-danger',
        default => $status
    };
}

function deadLineColor($status, $deadline): string
{
    if ($deadline <= 2 || $status->is_final) {
        return 'is-success';
    } elseif ($deadline == 3) {
        return 'is-warning';
    } else {
        return 'is-danger';
    }
}

function getSubstatusColor(string $status): string
{
    if ($status === 'Em análise') {
        return 'is-yellow';
    }
    if ($status === 'Aprovado') {
        return 'is-green';
    }
    if ($status === 'Reprovado') {
        return 'is-danger';
    }
    return '';
}

function commaFloatToDotFloat(string $commaFloat): float
{
    return (float)str_replace(
        search: ',',
        replace: '.',
        subject: $commaFloat
    );
}

function roundOrFloorDecimalNumber(float $number): int
{
    $int = floor($number);
    $decimal = $number - $int;

    if ($decimal <= 0.5) {
        return $int;
    }

    return ceil($number);
}

function calculateWithoutSolar($proposal): string
{

    return floatToMoney($proposal->average_consumption * $proposal->kw_price);
}


function calculateWithSolar($proposal): string
{
    if ($proposal->tension_pattern == 'MONO-220') {
        return 70;
    } elseif ($proposal->tension_pattern == 'BI-220') {
        return 100;
    }

    return 130;
}
