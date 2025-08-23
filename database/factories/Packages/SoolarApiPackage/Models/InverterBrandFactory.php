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
            'overload' => $this->faker->randomFloat(2, 1, 2),
            'active' => true,
        ];
    }
}
