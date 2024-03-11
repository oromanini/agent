<?php

namespace App\Models\Pricing;

use App\Enums\WorkCostClassificationEnum;
use Exception;
use Illuminate\Database\Eloquent\Model;

class WorkCost extends Model
{
    protected $guarded = [];
    protected $casts = [
        'costs' => 'json',
    ];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function costs()
    {
        return json_decode($this->costs, true);
    }

    /** @throws Exception */
    public function toArray(): array
    {
        $classification = WorkCostClassificationEnum::classificateByEnum(
            WorkCostClassificationEnum::INSTALLATION
        );

        return [
            'classification' => $classification,
            'costs' => $this->costs(),
            'last_update' => $this->updated_at,
        ];
    }
}
