<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Eloquent\Collection;

class CityController extends Controller
{
    public function citiesByState($id): Collection|array
    {
        return City::query()
            ->select('id', 'name')
            ->where('state_id', '=', $id)
            ->get();
    }

    public function citiesByNameAndUf(string $name, string $uf): object
    {
        return City::query()
            ->select('id', 'name', 'state_id')
            ->where('name', 'like', '%' . $name . '%')
            ->where('federal_unit', $uf)
            ->first();
    }
}
