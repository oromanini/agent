<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'region'];

    public function scopeOfRegion($query, $type) {
        return $query->where('type', $type);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
