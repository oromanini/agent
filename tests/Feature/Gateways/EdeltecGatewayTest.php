<?php

namespace Tests\Feature\Gateways;

use App\Enums\RoofStructure;
use App\Models\ActiveKit;
use App\Models\Address;
use App\Models\PromotionalKit;
use App\Packages\EdeltecApiPackage\EdeltecApiService;
use App\Packages\EdeltecApiPackage\EdeltecGeneratorsRepository;
use App\Packages\EdeltecApiPackage\Enums\Category;
use App\Packages\EdeltecApiPackage\Enums\InverterBrand;
use App\Packages\EdeltecApiPackage\Enums\PanelBrand;
use App\Packages\EdeltecApiPackage\Enums\StructureType;
use App\Services\PricingService;
use Generator;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EdeltecGatewayTest extends TestCase
{
    private EdeltecApiService $edeltecApiService;
}
