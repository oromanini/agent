<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function kitSpecs(): array
    {
        return [
            'panel' => json_decode($this->panel_specs, true),
            'inverter' => json_decode($this->inverter_specs, true),
        ];
    }
}
