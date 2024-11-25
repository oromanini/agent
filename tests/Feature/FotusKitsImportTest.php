<?php

namespace Feature;

use App\Enums\RoofStructure;
use App\Models\Kit;
use App\Services\Fotus\FotusKitsImportService;
use Tests\TestCase;

class FotusKitsImportTest extends TestCase
{
    private FotusKitsImportService $fotusKitsImportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fotusKitsImportService = new FotusKitsImportService('tests/Feature/files/fotus/kits.csv');
    }

    public function testFotusKitsImportService(): void
    {
        $this->fotusKitsImportService->importStringMonoInverterKits();
        $totalKitsCount = (count(RoofStructure::cases()) - 1) * 3;
        $this->assertCount($totalKitsCount, Kit::all());
    }
}
