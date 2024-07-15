<?php

namespace Database\Factories;

use App\Models\Financing;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancingFactory extends Factory
{
    protected $model = Financing::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['person', 'company']),
            'full_name' => $this->faker->name,
            'owner_document' => $this->faker->numerify('###########'),
            'birthdate' => $this->faker->date('Y-m-d'),
            'property_situation' => $this->faker->word,
            'income' => $this->faker->randomFloat(2, 1000, 100000),
            'patrimony' => $this->faker->randomFloat(2, 10000, 1000000),
            'profession' => $this->faker->jobTitle,
            'bank' => $this->faker->name,
            'installments' => $this->faker->randomNumber(2),
            'payment_grace' => $this->faker->randomNumber(2),
            'note' => $this->faker->paragraph,
            'proof_of_income' => $this->faker->word,
            'document_file' => $this->faker->filePath(),
            'created_at' => now(),
            'updated_at' => now(),
            'address' => $this->faker->address,
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->phoneNumber,
            'deleted_at' => null,
            'status_id' => \App\Models\Status::factory(),
            'owner_id' => \App\Models\User::factory(),
            'secondary_owner_id' => \App\Models\User::factory(),
        ];
    }
}
