<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\City;
use App\Models\Client;
use App\Models\ConsumerUnit;
use App\Models\Proposal;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ClientFactory extends Factory
{

    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->name,
            'type' => $this->faker->randomElement(['person', 'company']),
            'document' => $this->faker->unique()->numerify('###########'),
            'alias' => $this->faker->optional()->company,
            'email' => $this->faker->email,
            'phone_number' => $this->faker->phoneNumber,
            'agent_id' => \App\Models\User::factory(),
            'owner_document' => $this->faker->optional()->numerify('###########'),
            'deleted_at' => $this->faker->optional()->dateTime,
            'created_at' => now(),
            'updated_at' => now(),
            'birthdate' => $this->faker->optional()->date,
            'account_owner_document' => $this->faker->optional()->numerify('###########'),
        ];
    }
}
