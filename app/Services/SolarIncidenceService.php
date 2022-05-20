<?php

namespace App\Services;

use App\Models\City;
use App\Models\SolarIncidence;

class SolarIncidenceService
{
    public function getSolarIncidence(City $city)
    {
        return SolarIncidence::query()
            ->where('latitude', '>=', $city->latitude)
            ->where('longitude', '>=', $city->longitude)
            ->orderByRaw("latitude ASC, longitude ASC")
            ->first();
    }
}
