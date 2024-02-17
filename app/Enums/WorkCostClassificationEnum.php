<?php

namespace App\Enums;

enum WorkCostClassificationEnum
{
    const EXTERNAL_CONSULTANT_COMMISSION = 1;
    const INTERNAL_COMMERCIAL_COMMISSION = 2;
    const INTERNAL_FINANCING_COMMISSION = 3;
    const DELIVERY = 4;
    const WORK_MONITORING = 5;
    const DIRECT_CURRENT_MATERIAL = 6;
    const INSTALLATION = 7;
    const HOMOLOGATION = 8;
    const SAFETY_MARGIN = 9;
    const ROYALTY = 10;
    const TAX = 11;
    const CARD_FEE = 12;

    public static function classificateByEnum(int $enum): string
    {
        return match ($enum) {
            1 => 'Comissão do consultor',
            2 => 'Comissão do comercial interno',
            3 => 'Comissão do financiamento',
            4 => 'Frete',
            5 => 'Acompanhamento da obra',
            6 => 'Material C.A',
            7 => 'Instalação',
            8 => 'Homologação',
            9 => 'Margem de segurança',
            10 => 'Royalties',
            11 => 'Impostos',
            12 => 'Taxa da máquina de cartão',
            default => throw new \Exception('Unexistent workcost'),
        };
    }
}
