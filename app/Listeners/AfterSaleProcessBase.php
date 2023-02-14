<?php

namespace App\Listeners;

use App\Services\AfterSalesProcessService;
use Illuminate\Database\Eloquent\Model;

class AfterSaleProcessBase extends AfterSalesProcessService
{
    protected const STATUS_AWAITING = 13;

    protected function setStatusAndMarkItem(string $item, Model $model, array $checklist, object $helper): void
    {
        $checklist[$helper::translateItem($item)] = true;

        $model->checklist = json_encode($checklist);
        $model->status_id = $helper::matchStatus(key: $item, helper: $helper);

        $model->update();
    }
}
