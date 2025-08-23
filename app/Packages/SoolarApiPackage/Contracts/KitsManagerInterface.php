<?php

namespace App\Packages\SoolarApiPackage\Contracts;

use Illuminate\Http\JsonResponse;

interface KitsManagerInterface
{
    function handle(): JsonResponse;
}
