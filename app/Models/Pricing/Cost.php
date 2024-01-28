<?php

namespace App\Models\Pricing;

interface Cost
{
    public function cost(): float;
    public function workCostInfo(): array;
}
