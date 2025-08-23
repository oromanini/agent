<?php

namespace Database\Factories;

use App\Models\WorkCost;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkCostFactory extends Factory
{
    protected $model = WorkCost::class;

    public function definition(): array
    {
        return [
            'classification' => $this->faker->numberBetween(1, 5),
            'costs' => [
                'base_cost' => $this->faker->randomFloat(2, 100, 500),
                'tax_percentage' => $this->faker->randomFloat(4, 0.1, 0.25),
            ],
            'change_history' => [],
        ];
    }
}
