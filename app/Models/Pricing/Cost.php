<?php

namespace App\Models\Pricing;

interface Cost
{
    public function cost(?float $getPercent = null): float;
}
