<?php

namespace App\Models\Pricing;

use App\Repositories\WorkCostRepository;
use Illuminate\Support\Facades\Log;

abstract class BaseCost
{
    private WorkCostRepository $workCostRepository;

    public function __construct()
    {
        $this->workCostRepository = new WorkCostRepository();
    }

    protected function workCostInfo(): array
    {
        return $this->workCostRepository
            ->getWorkCostByClassification(
                $this->classification()
            )->toArray();
    }

    protected function logMissingRange(string $message): void
    {
        Log::error($message);
    }

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

    protected abstract function classification(): int;
    protected abstract function costRanges(): array;
}
