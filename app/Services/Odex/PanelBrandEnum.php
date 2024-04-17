<?php

namespace App\Services\Odex;

use Illuminate\Support\Facades\Log;

enum PanelBrandEnum: string
{
    case ERA = 'ERA';

    public static function matchCases(string $panel): self
    {
        return match (strtoupper($panel)) {
            'ERA' => self::ERA,
            default => Log::alert("Padrão {$panel} não encontrado!")
        };
    }
}
