<?php

namespace Database\Factories\Packages\SoolarApiPackage\Models;

use App\Packages\SoolarApiPackage\Models\InverterBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

class InverterBrandFactory extends Factory
{
    protected $model = InverterBrand::class;

    public function definition(): array
    {
        return [
            'brand' => $this->faker->company,
            'warranty' => $this->faker->numberBetween(5, 15),
            'overload' => $this->faker->randomFloat(2, 0.5, 2.0),
            'active' => true,
            'logo' => $this->faker->imageUrl(),
            'picture' => $this->faker->imageUrl(),
        ];
    }
}
