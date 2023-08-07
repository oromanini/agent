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
}
