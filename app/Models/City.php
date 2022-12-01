<?php

namespace App\Models;

use App\Services\SolarIncidenceService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function incidence(): string
    {
        return (new SolarIncidenceService())->getSolarIncidence($this)->average;
    }
}
