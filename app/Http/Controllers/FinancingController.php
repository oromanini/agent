<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinancingController extends Controller
{
    public function show()
    {
        return view('financing.simulator');
    }

    public function getMfs()
    {
        return view('financing.mfs');
    }
}
