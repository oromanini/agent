<?php

namespace App\Builders;

use App\Models\State;

class StateBuilder implements Builder
{
    private State $state;

    public function __construct()
    {
        $this->state = new State();
    }

    public function withName(string $name): static
    {
        $this->state->name = $name;
        return $this;
    }

    public function withRegion(string $region): static
    {
        $this->state->region = $region;
        return $this;
    }

    public function build(): State
    {
        $this->state->save();
        return $this->state;
    }
}
