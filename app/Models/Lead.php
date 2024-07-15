<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function pricing(): array
    {
        return json_decode($this->pricing_data, true);
    }

    public function manual(): array
    {
        return json_decode($this->manual_data, true);
    }

    public function kit(): array|null
    {
        return json_decode($this->kit_data, true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function components(): array
    {
        $kit = json_decode($this->kit_data, true);
        return json_decode($kit['components'], true);
    }

    public function city(): City
    {
        return City::find($this->city_id);
    }

}
