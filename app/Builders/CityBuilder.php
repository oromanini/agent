<?php

namespace App\Builders;

use App\Models\City;

class CityBuilder implements Builder
{
    private City $city;

    public function __construct()
    {
        $this->city = new City();
        $this->city->active = true;
    }

    public function withName(string $name): static
    {
        $this->city->name = $name;
        $this->updateNameAndFederalUnit();
        return $this;
    }

    public function withFederalUnit(string $federalUnit): static
    {
        $this->city->federal_unit = $federalUnit;
        $this->updateNameAndFederalUnit();
        return $this;
    }

    public function withLatitude(string $latitude): static
    {
        $this->city->latitude = $latitude;
        return $this;
    }

    public function withLongitude(string $longitude): static
    {
        $this->city->longitude = $longitude;
        return $this;
    }

    public function withStateId(int $stateId): static
    {
        $this->city->state_id = $stateId;
        return $this;
    }

    public function withActive(bool $active): static
    {
        $this->city->active = $active;
        return $this;
    }

    public function build(): City
    {
        $this->city->save();
        return $this->city;
    }

    private function updateNameAndFederalUnit(): void
    {
        if (isset($this->city->name) && isset($this->city->federal_unit)) {
            $this->city->name_and_federal_unit = "{$this->city->name}/{$this->city->federal_unit}";
        }
    }
}
