<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\City;
use App\Models\Client;
use App\Models\ConsumerUnit;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AddressFactory extends Factory
{

    protected $model = Address::class;

    public function definition(): array
    {
        $state = State::create([
            'id' => 1000,
            'name' => 'Paraná',
            'region' => 'Sudoeste',
        ]);

        $city = City::create([
            'name' => 'Maringá',
            'active' => true,
            'name_and_federal_unit' => 'Maringa-PR',
            'latitude' => -12.45,
            'longitude' => -20.45,
            'federal_unit' => 'PR',
            'state_id' => $state->id,
        ]);

        $agent = User::create([
            'name' => $this->faker->name,
            'cpf' => '111.111.111-12',
            'cnpj' => '00.000.000/0000-00',
            'phone_number' => '(44) 9 9915-5919',
            'email' => $this->faker->email,
            'password' => $this->faker->password,
            'city' => $city->id,
            'ascendant' => 1,
        ]);

        $client = Client::create([
            'uuid' => \Ramsey\Uuid\Uuid::uuid1(),
            'name' => '',
            'type' => 'person',
            'document' => '000.000.000-00',
            'email' => $this->faker->email,
            'phone_number' => $this->faker->phoneNumber,
            'agent_id' => $agent->id,
        ]);

        $consumerUnit = ConsumerUnit::create([
            'number' => $this->faker->randomNumber(5),
            'type' => 'residential',
            'eletricity_bill' => $this->faker->realTextBetween(20)
        ]);

        return [
            'street' => $this->faker->streetName(),
            'number' => $this->faker->numberBetween(1,1000),
            'complement' => '1404',
            'zipcode' => '87010-040',
            'neighborhood' => $this->faker->streetAddress,
            'city_id' => $city->id,
            'client_id' => $client->id,
            'consumer_unit_id' => $consumerUnit->id,
            'is_installation_address' => true,
        ];
    }
}
