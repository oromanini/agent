<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductsUpdateController extends Controller
{
    public function index(): View
    {
        return view('update_products.index');
    }
}
