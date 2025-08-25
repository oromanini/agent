<?php

namespace Database\Factories\Packages\SoolarApiPackage\Models;

use App\Packages\SoolarApiPackage\Models\ModuleBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleBrandFactory extends Factory
{
    protected $model = ModuleBrand::class;

    public function definition(): array
    {
        return [
            'brand' => $this->faker->company,
            'warranty' => $this->faker->numberBetween(10, 15),
            'linear_warranty' => $this->faker->numberBetween(20, 30),
            'active' => true,
            'logo' => $this->faker->imageUrl(),
            'picture' => $this->faker->imageUrl(),
        ];
    }
}
