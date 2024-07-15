<?php

namespace Database\Factories;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        return [
            'file' => $this->faker->filePath(),
            'signed_file' => $this->faker->filePath(),
            'note' => $this->faker->paragraph,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
            'status_id' => \App\Models\Status::factory(),
            'owner_id' => \App\Models\User::factory(),
            'secondary_owner_id' => \App\Models\User::factory(),
        ];
    }
}
