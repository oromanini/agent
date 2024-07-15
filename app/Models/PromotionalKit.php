<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalKit extends Model
{
    use HasFactory;
    use Timestamp;

    protected $fillable = [
        'panel_brand',
        'panel_power',
        'inverter_brand',
        'kwp',
        'final_value',
        'active',
    ];

    public static function isPromotional(Kit $kit): bool
    {
        $panel_specs = jsonToArray($kit->panel_specs);
        $inverter_specs = jsonToArray($kit->inverter_specs);

        $promotion = PromotionalKit::where('kwp', $kit->kwp)
            ->where('panel_brand', $panel_specs['brand'])
            ->where('panel_power', $panel_specs['power'])
            ->where('inverter_brand', $inverter_specs['brand'])
            ->where('is_active', true)
            ->first();

        return !is_null($promotion);
    }
}
