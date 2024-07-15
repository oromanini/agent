<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveKit extends Model
{
    use HasFactory;

    public static function boot(): void
    {
        parent::boot();

        static::saving(function ($model) {
            $model->timestamps = false;
        });
    }

    protected $guarded = [];
    protected $dates = ['last_updated_time'];

    public function __toString(): string
    {
        return "{$this->panel_brand} c/ {$this->inverter_brand}";
    }
}
