<?php

namespace App\Services;

use App\Models\City;
use App\Models\SolarIncidence;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SolarIncidenceService
{
    public function getSolarIncidence(City $city): SolarIncidence|null
    {
        return SolarIncidence::query()
            ->where('latitude', '>=', $city->latitude)
            ->where('longitude', '>=', $city->longitude)
            ->orderByRaw("latitude ASC, longitude ASC")
            ->first();
    }
}
