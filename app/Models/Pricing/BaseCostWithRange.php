<?php

namespace App\Models\Pricing;

use App\Repositories\WorkCostRepository;
use Illuminate\Support\Facades\Log;

abstract class BaseCostWithRange extends BaseCost
{
    protected function calculateRange(
        float $minimumCost,
        float $kwp
    ): float {
        $cost = $minimumCost;
        $range = $this->costRanges();

        foreach ($range as $rangeKwp => $rangeCost) {
            if ($kwp < $rangeKwp) {
                $cost = $rangeCost;
                break;
            }
        }

        return $cost;
    }

    protected abstract function costRanges(): array;
}
