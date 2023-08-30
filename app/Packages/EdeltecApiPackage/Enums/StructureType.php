<?php

namespace App\Packages\EdeltecApiPackage\Enums;

enum StructureType: string
{
    case COLONIAL = 'COLONIAL';
    case FIBROMADEIRA = 'FIBROMADEIRA';
    case FIBROMETAL = 'FIBROMETAL';
    case LAJE = 'LAJE';
    case METALICO = 'METALICO';
    case SOLO = 'SOLO';
    case SEM_ESTRUTURA = 'S/ESTRUTURA';

    public static function matchRoof($roof): StructureType
    {
        $upperRoof = strtoupper($roof);

        return match ($upperRoof) {
            'COLONIAL', 'CERAMICO', 'CERÂMICO' => self::COLONIAL,
            'PARAFMADEIRA', 'FIBROCIMENTO MADEIRA', 'FIBROMADEIRA', 'ONDULADO' => self::FIBROMADEIRA,
            'PARAFMETAL', 'FIBROCIMENTO METAL', 'FIBROMETAL' => self::FIBROMETAL,
            'SOLO' => self::SOLO,
            'LAJE' => self::LAJE,
            'METALICO', 'TRAPEZOIDAL', 'ZINCO' => self::METALICO,
            'S/ESTRUTURA' => self::SEM_ESTRUTURA,
        };
    }

}
