<?php

namespace Database\Factories;

use App\Models\PreInspection;
use Illuminate\Database\Eloquent\Factories\Factory;

class PreInspectionFactory extends Factory
{
    protected $model = PreInspection::class;

    public function definition(): array
    {
        return [
            'roof' => $this->faker->optional()->randomElement([json_encode(['type' => 'tile', 'condition' => 'good']), json_encode(['type' => 'metal', 'condition' => 'poor'])]),
            'pattern' => $this->faker->optional()->randomElement([json_encode(['pattern' => 'standard']), json_encode(['pattern' => 'custom'])]),
            'circuit_breaker' => $this->faker->optional()->randomElement([json_encode(['type' => 'standard', 'amperage' => '20A']), json_encode(['type' => 'custom', 'amperage' => '30A'])]),
            'switchboard' => $this->faker->optional()->randomElement([json_encode(['location' => 'garage']), json_encode(['location' => 'basement'])]),
            'post' => $this->faker->optional()->randomElement([json_encode(['material' => 'wood']), json_encode(['material' => 'metal'])]),
            'compass' => $this->faker->optional()->randomElement([json_encode(['direction' => 'N']), json_encode(['direction' => 'S'])]),
            'croqui' => $this->faker->optional()->randomElement([json_encode(['layout' => 'simple']), json_encode(['layout' => 'complex'])]),
            'observations' => $this->faker->optional()->text,
            'created_at' => now(),
            'updated_at' => now(),
            'circuit_breaker_amperage' => $this->faker->optional()->randomElement(['10A', '20A', '30A']),
//            'property_fax' => $this->faker->optional()->phoneNumber,
//            'open_pattern' => $this->faker->optional()->text,
//            'meter' => $this->faker->optional()->text,
//            'roof_structure' => $this->faker->optional()->randomElement([json_encode(['structure' => 'truss']), json_encode(['structure' => 'rafter'])]),
//            'inverter_local' => $this->faker->optional()->text,
        ];
    }
}
