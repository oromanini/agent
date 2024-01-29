<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\InstallationCost;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class WorkCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $panelQuantity = 10;
        $this->installationCost = new InstallationCost($panelQuantity);
        $this->costs = [
            'panel_price' => 120,
            'displacement' => 10,
        ];
        $this->workCost = (new WorkCostBuilder())
            ->withCosts($this->costs)
            ->withClassification(WorkCostClassificationEnum::INSTALLATION)
            ->withChangeHistory()
            ->build();
    }

    public function testToArray(): void
    {
        $this->assertIsArray($this->workCost->toArray());
    }

    public function testWorkCostBuilder_WithSameClassification_ShouldCry(): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('Integrity constraint violation');

        (new WorkCostBuilder())
            ->withCosts($this->costs)
            ->withClassification(WorkCostClassificationEnum::INSTALLATION)
            ->withChangeHistory()
            ->build();
    }
}
