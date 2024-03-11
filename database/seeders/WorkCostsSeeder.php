<?php

namespace Database\Seeders;

use App\Builders\WorkCostBuilder;
use App\Enums\WorkCostClassificationEnum;
use Illuminate\Database\Seeder;

class WorkCostsSeeder extends Seeder
{
    public function run(): void
    {
        (new WorkCostBuilder())
            ->withCosts([
                'panel_price' => 120,
                'displacement' => 10,
            ])
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::INSTALLATION)->build();

        (new WorkCostBuilder())
            ->withCosts(['estimated_material_percentage' => 0.045])
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::DIRECT_CURRENT_MATERIAL)->build();

        (new WorkCostBuilder())
            ->withCosts([
                'sell_estimated_percentage' => 0.06,
                'service_estimated_percentage' => 0.135,
            ])
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::TAX)->build();

        (new WorkCostBuilder())
            ->withCosts([
                'homologation_cost_range' =>
                    [
                        5 => 250,
                        10 => 350,
                        15 => 450,
                        20 => 650,
                        30 => 800,
                    ]
            ])
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::HOMOLOGATION)->build();

        (new WorkCostBuilder())
            ->withCosts([
                'monitoring_cost_range' =>
                    [
                        5 => 160,
                        10 => 230,
                        15 => 450,
                        20 => 620,
                    ]
            ])
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::WORK_MONITORING)->build();

        (new WorkCostBuilder())
            ->withCosts([
                'commission_percentage' => 0.1,
                'credit_card_commission_percentage' => 0.08,
            ])
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::EXTERNAL_CONSULTANT_COMMISSION)->build();

        (new WorkCostBuilder())
            ->withCosts([
                'commission_percentage' => 0.01
            ])
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::INTERNAL_COMMERCIAL_COMMISSION)->build();

        (new WorkCostBuilder())
            ->withCosts([
                'commission_percentage' => 0.01
            ])
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::INTERNAL_FINANCING_COMMISSION)->build();

        (new WorkCostBuilder())
            ->withCosts([
                'estimated_percentage' => 0.017
            ])
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::SAFETY_MARGIN)->build();

        (new WorkCostBuilder())
            ->withCosts(
                ['estimated_percentage' => 0.02]
            )
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::ROYALTY)->build();

        (new WorkCostBuilder())
            ->withCosts([
                'estimated_fee' => 0.114
            ])
            ->withChangeHistory()
            ->withClassification(WorkCostClassificationEnum::CARD_FEE)->build();
    }
}
