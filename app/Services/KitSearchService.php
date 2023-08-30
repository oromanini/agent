<?php

namespace App\Services;

use App\Enums\DistributorsEnum;
use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\ActiveKit;
use App\Models\Kit;
use App\Packages\EdeltecApiPackage\EdeltecApiService;
use App\Packages\EdeltecApiPackage\Enums\StructureType;
use App\Packages\KitResource;
use GuzzleHttp\Client;


class KitSearchService
{

    public function kitSearch(float $kwp, int $roof, string $tension): array
    {
        $kits = [];
        $roof = RoofStructure::matchRoof($roof);
        $tension = TensionPattern::setTensionPattern($tension);


        $kits = Kit::query()->where('is_active', true)
            ->where('roof_structure', $roof->value)
            ->where('kwp', '>=', $kwp)
            ->where('tension_pattern', $tension)
            ->where('')
            ->first();
    }

}
