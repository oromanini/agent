<?php

namespace Database\Factories;

use App\Models\Inspection;
use Illuminate\Database\Eloquent\Factories\Factory;

class InspectionFactory extends Factory
{
    protected $model = Inspection::class;

    public function definition(): array
    {
        return [
            'note' => $this->faker->paragraph,
            'files' => json_encode([$this->faker->filePath(), $this->faker->filePath()]), // Exemplos de arquivos JSON
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'status_id' => \App\Models\Status::factory(),
//            'three_dimensional' => json_encode(['x' => $this->faker->randomFloat(2, 0, 100), 'y' => $this->faker->randomFloat(2, 0, 100), 'z' => $this->faker->randomFloat(2, 0, 100)]),
            'owner_id' => \App\Models\User::factory(),
            'secondary_owner_id' => \App\Models\User::factory(),
        ];
    }
}
