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
    return (float) str_replace(
        ',',
        '.',
        $inverterPower
    );
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
    $bel = '/img/panel_brands/bel.png';
    $sunova = '/img/panel_brands/sunova.png';
    $osda = '/img/panel_brands/osda.png';
    $ae_solar = '/img/panel_brands/ae_solar.png';

    if (is_string($brand)) {

        if ($brand == 'Jinko') {
            $img = $jinko;
        } elseif ($brand == 'Trina') {
            $img = $trina;
        } elseif ($brand == 'DAH Solar') {
            $img = $dah;
        } elseif ($brand == 'Sunket') {
            $img = $sunket;
        } elseif ($brand == 'Astronergy' || $brand == 'Astronergy Chint') {
            $img = $astronergy;
        } elseif ($brand == 'Ja') {
            $img = $ja;
        } elseif ($brand == 'Phono') {
            $img = $phono;
        } elseif ($brand == 'Longi') {
            $img = $longi;
        } elseif ($brand == 'Bel') {
            $img = $bel;
        } elseif ($brand == 'Sunova') {
            $img = $sunova;
        } elseif ($brand == 'Osda') {
            $img = $osda;
        }  elseif ($brand == 'Ae_Solar') {
            $img = $ae_solar;
        }

    } else {

        $img = match ($brand) {
            1 => $jinko,
            2 => $sunket,
            3 => $trina,
            4 => $dah,
            5 => $astronergy,
            6 => $ja,
            7 => $phono,
            8 => $longi,
            9 => $bel,
            10 => $sunova,
            11 => $osda,
            12 => $ae_solar,
            default => throw new Exception('Painel não localizado.'),
        };
    }
    return $img;
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
    $bel = '/img/inverters/bel.png';
    $sungrow = '/img/inverters/sungrow.png';
    $canadian = '/img/inverters/canadian.png';
    $saj = '/img/inverters/saj.png';

    if (is_string($brand)) {
        $img = match ($brand) {
            'Growatt' => $growatt,
            'Chint' => $chint,
            'Deye' => $deye,
            'DeyeString' => $deyeString,
            'Sofar' => $sofar,
            'Solis' => $solis,
            'Bel' => $bel,
            'Sungrow' => $sungrow,
            'Canadian' => $canadian,
            'Saj' => $saj,
        };
    } else {

        $img = match ($brand) {
            1 => $growatt,
            2 => $chint,
            3 => $deye,
            4 => $sofar,
            5 => $solis,
            6 => $bel,
            7 => $sungrow,
            8 => $deyeString,
            9 => $canadian,
            10 => $saj,
            default => throw new Exception('Inversor não localizado.'),
        };
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

function setFinalPrice($data)
{
    $pricingService = new PricingService();
    $data['kwp'] = $data['sumKits']['kwp'];
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

function setContractStatuses(): array
{
    return [
        'Aguardando',
        'Aguardando assinatura',
        'Finalizado',

    ];
}

function setInspectionStatuses(): array
{
    return [
        'Aguardando',
        'Aguardando fotos agente',
        'Aprovado',
        'Aprovado com adequação',
        'Reprovado',
    ];
}

function setFinancingStatuses(): array
{
    return [
        'Aguardando',
        'Em análise',
        'Reprovado',
        'Aprovado',
        'À Vista',
        'Cartão',
        '60/40'
    ];
}

function deadLineColor($status, $deadline): string
{
    if ($deadline <= 2 || $status->is_final) {
        return 'is-success';
    } elseif($deadline == 3) {
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
    } if ($status === 'Reprovado') {
        return 'is-danger';
    }
}
