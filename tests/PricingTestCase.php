<?php

namespace Tests;

use App\Builders\WorkCostBuilder;

class PricingTestCase extends TestCase
{
    protected function createWorkCosts(): void {
        $workCostsData = $this->setWorkCosts();
        foreach ($workCostsData as $data) {
            $costsArray = json_decode($data['costs'], true);

            (new WorkCostBuilder())
                ->withClassification($data['classification'])
                ->withCosts($costsArray)
                ->withChangeHistory()
                ->build();
        }
    }

    private function setWorkCosts(): array {
        return [
            [
                'id' => 1,
                'classification' => 7,
                'costs' => '{"panel_price":130,"displacement":10,"lead_panel_price":100}',
                'change_history' => '[{"user_id":"first","date":"2024-02-05 01:19:20"},{"user_id":1,"date":"2025-08-30 22:01:01","action":"updated","previous_costs":{"panel_price":150,"displacement":10,"lead_panel_price":110}}]',
            ],
            [
                'id' => 2,
                'classification' => 6,
                'costs' => '{"estimated_material_percentage":0.045}',
                'change_history' => '{"user_id":"first","date":"2024-02-05 01:19:20"}',
            ],
            [
                'id' => 3,
                'classification' => 11,
                'costs' => '{"sell_estimated_percentage":0.03,"service_estimated_percentage":0.135}',
                'change_history' => '[{"user_id":"first","date":"2024-02-05 01:19:20"},{"user_id":1,"date":"2025-08-30 22:00:40","action":"updated","previous_costs":{"sell_estimated_percentage":0.06,"service_estimated_percentage":0.135}}]',
            ],
            [
                'id' => 4,
                'classification' => 8,
                'costs' => '{"homologation_cost_range":{"5":450,"10":500,"15":900,"20":1200,"30":1800}}',
                'change_history' => '[{"user_id":"first","date":"2024-02-05 01:19:20"},{"user_id":2,"date":"2025-09-01 20:21:46","action":"updated","previous_costs":{"homologation_cost_range":{"5":500,"10":900,"15":1200,"20":1800,"30":2500}}}]',
            ],
            [
                'id' => 5,
                'classification' => 5,
                'costs' => '{"monitoring_cost_range":{"5":1,"10":1,"15":1,"20":1}}',
                'change_history' => '{"user_id":"first","date":"2024-02-05 01:19:20"}',
            ],
            [
                'id' => 6,
                'classification' => 1,
                'costs' => '{"commission_percentage":0.12,"credit_card_commission_percentage":0.12}',
                'change_history' => '{"user_id":"first","date":"2024-02-05 01:19:20"}',
            ],
            [
                'id' => 7,
                'classification' => 2,
                'costs' => '{"commission_percentage":0.01}',
                'change_history' => '{"user_id":"first","date":"2024-02-05 01:19:20"}',
            ],
            [
                'id' => 8,
                'classification' => 3,
                'costs' => '{"commission_percentage":0.01}',
                'change_history' => '{"user_id":"first","date":"2024-02-05 01:19:20"}',
            ],
            [
                'id' => 9,
                'classification' => 9,
                'costs' => '{"estimated_percentage":0.017}',
                'change_history' => '{"user_id":"first","date":"2024-02-05 01:19:20"}',
            ],
            [
                'id' => 10,
                'classification' => 10,
                'costs' => '{"estimated_percentage":0.02}',
                'change_history' => '{"user_id":"first","date":"2024-02-05 01:19:20"}',
            ],
            [
                'id' => 11,
                'classification' => 12,
                'costs' => '{"estimated_fee":0.095}',
                'change_history' => '{"user_id":"first","date":"2024-02-05 01:19:20"}',
            ],
            [
                'id' => 12,
                'classification' => 13,
                'costs' => '{"profit":0.115}',
                'change_history' => '{"user_id":"first","date":"2024-02-05 01:19:20"}',
            ],
        ];
    }
}
