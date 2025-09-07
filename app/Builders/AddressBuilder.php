<?php

namespace App\Builders;

use App\Models\Address;
use App\Models\City;
use App\Models\Client;
use App\Models\ConsumerUnit;
use Illuminate\Database\Eloquent\Model;

class AddressBuilder implements Builder
{
    private Address $address;

    public function __construct()
    {
        $this->address = new Address();
    }

    public function withStreet(string $street): static
    {
        $this->address->street = $street;
        return $this;
    }

    public function withNumber(string $number): static
    {
        $this->address->number = $number;
        return $this;
    }

    public function withComplement(string $complement): static
    {
        $this->address->complement = $complement;
        return $this;
    }

    public function withZipcode(string $zipcode): static
    {
        $this->address->zipcode = $zipcode;
        return $this;
    }

    public function withNeighborhood(string $neighborhood): static
    {
        $this->address->neighborhood = $neighborhood;
        return $this;
    }

    public function withCity(City|Model $city): static
    {
        $this->address->city_id = $city->id;
        return $this;
    }

    public function withClient(Client|Model $client): static
    {
        $this->address->client_id = $client->id;
        return $this;
    }

    public function withConsumerUnit(ConsumerUnit|Model $consumerUnit): static
    {
        $this->address->consumer_unit_id = $consumerUnit->id;
        return $this;
    }

    public function build(): Address
    {
        $this->address->save();
        return $this->address;
    }
}
