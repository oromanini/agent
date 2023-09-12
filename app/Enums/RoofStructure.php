<?php

namespace App\Enums;

enum RoofStructure: int
{
    case COLONIAL = 1;
    case METALICO = 2;
    case LAJE = 3;
    case FIBROCIMENTO_MADEIRA = 4;
    case FIBROCIMENTO_METAL = 5;
    case SOLO = 6;
    case SEM_ESTRUTURA = 8;

    public static function translateExternalRoof($roof): RoofStructure
    {
        $upperRoof = strtoupper($roof->value ?? $roof);
        return match ($upperRoof) {
            'COLONIAL', 'CERAMICO', 'CERÂMICO' => self::COLONIAL,
            'PARAFMADEIRA', 'FIBROCIMENTO MADEIRA', 'FIBROMADEIRA' => self::FIBROCIMENTO_MADEIRA,
            'PARAFMETAL', 'FIBROCIMENTO METAL', 'FIBROMETAL' => self::FIBROCIMENTO_METAL,
            'SOLO' => self::SOLO,
            'LAJE' => self::LAJE,
            'METALICO', 'TRAPEZOIDAL', 'ZINCO' => self::METALICO,
            'S/ESTRUTURA' => self::SEM_ESTRUTURA
        };
    }

    public static function setRoofsToScreen(): array
    {
        return [
            [
                'id' => RoofStructure::COLONIAL,
                'image' => '/img/roofs/colonial.png',
                'description' => 'Colonial'
            ],
            [
                'id' => RoofStructure::METALICO,
                'image' => '/img/roofs/trapezoidal.png',
                'description' => 'Metálica'
            ],
            [
                'id' => RoofStructure::LAJE,
                'image' => '/img/roofs/laje.png',
                'description' => 'Laje'
            ],
            [
                'id' => RoofStructure::FIBROCIMENTO_MADEIRA,
                'image' => '/img/roofs/paraf-madeira.png',
                'description' => 'Fibrocimento c/ Madeira'
            ],
            [
                'id' => RoofStructure::FIBROCIMENTO_METAL,
                'image' => '/img/roofs/paraf-metal.png',
                'description' => 'Fibrocimento c/ Metal'
            ],
            [
                'id' => RoofStructure::SOLO,
                'image' => '/img/roofs/solo.png',
                'description' => 'Solo'
            ],
        ];
    }
}
