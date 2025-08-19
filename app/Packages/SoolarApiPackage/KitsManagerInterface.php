<?php

namespace App\Packages\SoolarApiPackage;

use App\Enums\RoofStructure;
use Illuminate\Http\JsonResponse;

interface KitsManagerInterface
{
    function handle(): JsonResponse;
    function calculateKwp(int $panelQuantity, int $panelPower): float;
    function chooseInverter(float $kwp): array;
    function calculateCable(int $panelQuantity): array;
    function calculateStructure(int $panelQuantity, RoofStructure $roofStructure): array;
}
