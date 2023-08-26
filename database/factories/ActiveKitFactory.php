<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ActiveKitFactory extends Factory
{

    public function definition(): array
    {
        return [
            'distributor' => 'ODERÇO',
            'panel_brand' => 'OSDA',
            'inverter_brand' => 'GROWATT'
        ];
    }
}
