<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'brands';
    protected $guarded = [];

    public function scopePanels(Builder $query): Collection
    {
        return $query->where('type', 'panel')->get();
    }

    public function scopeInverters(Builder $query): Collection
    {
        return $query->where('type', 'inverter')->get();
    }
}
