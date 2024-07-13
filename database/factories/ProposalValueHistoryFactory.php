<?php

namespace Database\Factories;

use App\Models\ProposalValueHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProposalValueHistoryFactory extends Factory
{
    protected $model = ProposalValueHistory::class;

    public function definition(): array
    {
        return [
            'kit_cost' => $this->faker->randomFloat(2, 100, 1000),
            'initial_price' => $this->faker->randomFloat(2, 200, 2000),
            'cash_initial_price' => $this->faker->optional()->randomFloat(2, 150, 1950),
            'card_initial_price' => $this->faker->optional()->randomFloat(2, 150, 1950),
            'final_price' => $this->faker->randomFloat(2, 200, 2000),
            'card_final_price' => $this->faker->optional()->randomFloat(2, 150, 1950),
            'cash_final_price' => $this->faker->optional()->randomFloat(2, 150, 1950),
            'commission_percent' => $this->faker->randomFloat(2, 5, 20),
            'commission' => $this->faker->optional()->randomElement([json_encode(['type' => 'fixed', 'amount' => 100]), json_encode(['type' => 'percentage', 'amount' => 10])]),
            'discount_percent' => $this->faker->randomFloat(2, 0, 5),
            'user_id' => \App\Models\User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
            'is_promotional' => $this->faker->boolean,
        ];
    }
}
