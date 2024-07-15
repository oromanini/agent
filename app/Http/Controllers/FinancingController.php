<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class FinancingController extends Controller
{
    public function show(): View
    {
        return view('financing.simulator');
    }

    public function getMfs(): View
    {
        return view('financing.mfs');
    }
}
