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

    protected abstract function classification(): int;
}
