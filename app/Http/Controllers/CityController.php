<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function citiesByState($id)
    {
        return City::query()
        ->select('id', 'name')
        ->where('state_id', '=', $id)
        ->get();
    }
}
