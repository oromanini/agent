<?php

namespace App\Services\Fotus;

use Illuminate\Support\Facades\Log;

enum PanelBrandEnum: string
{
    case ZNSHINE = 'ZNSHINE';
    case SUNOVA = 'SUNOVA';
    case PULLING = 'PULLING';

    public static function matchCases(string $panel): self
    {
        return match (strtoupper($panel)) {
            'ZNSHINE' => self::ZNSHINE,
            'SUNOVA' => self::SUNOVA,
            'PULLING' => self::PULLING,
            default => Log::info("Padrão {$panel} não encontrado!")
        };
    }
}
