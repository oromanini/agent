<?php

namespace App\Builders;

use App\Models\Pricing\WorkCost;
use Illuminate\Support\Facades\Auth;

class WorkCostBuilder
{
    private WorkCost $workCost;

    public function __construct()
    {
        $this->workCost = new WorkCost();
    }

    public function withCosts(array $costs): WorkCostBuilder
    {
        $this->workCost->costs = json_encode($costs);

        return $this;
    }

    public function withClassification(int $classificationEnum): WorkCostBuilder
    {
        $this->workCost->classification = $classificationEnum;

        return $this;
    }

    public function withChangeHistory(): WorkCostBuilder
    {
        $history = [
            'user_id' => 'first',
            'date' => now()->toDateTimeString(),
        ];

        $this->workCost->changeHistory = json_encode($history);

        return $this;
    }

    public function build(): WorkCost
    {
        return $this->workCost;
    }
}
