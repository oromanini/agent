<?php

namespace App\Repositories;

use App\Models\Pricing\WorkCost;
use Illuminate\Database\Eloquent\Model;

class WorkCostRepository
{
    public function getWorkCostByClassification(int $classificationEnum): WorkCost|Model
    {
        return WorkCost::query()
            ->where('classification', $classificationEnum)
            ->first();
    }
}
