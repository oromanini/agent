<?php

namespace Tests\Unit\Pricingv2;

use App\Builders\WorkCostBuilder;
use App\Enums\WorkCostClassificationEnum;
use App\Models\Pricing\WorkCost;
use PHPUnit\Framework\TestCase;

class WorkCostTest extends TestCase
{
    private WorkCost $workCost;
    private array $costs;

    protected function setUp(): void
    {
        parent::setUp();
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

    public function testWorkCostCreation(): void
    {
        $this->assertInstanceOf(WorkCost::class, $this->workCost);
    }

    public function testWorkCostToArray(): void
    {
        $this->assertIsArray(
            $this->workCost->toArray()
        );
    }
}
