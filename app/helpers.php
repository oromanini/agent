<?php

use App\Models\Address;
use App\Models\City;
use App\Models\User;

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

function setPanelBrandImage(string $brand): string
{
    $path = '/storage/module_brand_pictures/';

    return $path . $brand . '.png';
}

function setInverterBrandImage(string $brand): string
{
    $path = '/storage/inverter_brand_logos/';

    return $path . $brand . '.png';
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

function setRoofPlusLost(string $roof_orientation): string
{
    return match ($roof_orientation) {
        '["sul"]' => "S 50%",
        '["leste/oeste"]' => "L/O 30%",
        default => "N 20%"
    };
}
