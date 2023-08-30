<?php

namespace App\Enums;

enum RoofStructure: int
{
    case Colonial = 1;
    case Trapezoidal = 2;
    case Laje = 3;
    case ParafMetal = 4;
    case ParafMadeira = 5;
    case Solo = 6;
    case Ondulada = 7;
    case SemEstrutura = 8;

    public static function matchRoof($roof): RoofStructure
    {
        $upperRoof = strtoupper($roof->value ?? $roof);
        return match ($upperRoof) {
            'COLONIAL', 'CERAMICO', 'CERÂMICO' => self::Colonial,
            'PARAFMADEIRA', 'FIBROCIMENTO MADEIRA', 'FIBROMADEIRA' => self::ParafMadeira,
            'PARAFMETAL', 'FIBROCIMENTO METAL', 'FIBROMETAL' => self::ParafMetal,
            'ONDULADO' => self::Ondulada,
            'SOLO' => self::Solo,
            'LAJE' => self::Laje,
            'METALICO', 'TRAPEZOIDAL', 'ZINCO' => self::Trapezoidal,
            'S/ESTRUTURA' => self::SemEstrutura
        };
    }
}
