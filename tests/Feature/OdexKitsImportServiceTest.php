<?php

namespace Tests\Feature;

use App\Models\Kit;
use App\Services\OdexKitsImportService;
use Tests\TestCase;

class OdexKitsImportServiceTest extends TestCase
{
    private OdexKitsImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OdexKitsImportService();
    }

    public function testOdexKitsImport(): void
    {
        $this->service->importMicroInverterKits(limit: 4);
        $first = Kit::query()->first()->attributesToArray();

        $expected = [
            "description" => "Kit gerador 2.22 kWP microinversor SAJ/ Era 555W",
            "kwp" => 2.22   ,
            "cost" => 3358.0,
            "roof_structure" => 1,
            "tension_pattern" => 1,
        ];

        $this->assertEquals($expected["description"], $first["description"]);
        $this->assertEquals($expected["kwp"], $first["kwp"]);
        $this->assertEquals($expected["cost"], $first["cost"]);
        $this->assertEquals($expected["roof_structure"], $first["roof_structure"]);
        $this->assertEquals($expected["tension_pattern"], $first["tension_pattern"]);
    }
}
