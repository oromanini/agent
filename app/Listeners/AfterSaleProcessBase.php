<?php

namespace App\Listeners;

use App\Helpers\HomologationHelper;
use App\Models\Installation;
use App\Models\Proposal;
use App\Services\AfterSalesProcessService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AfterSaleProcessBase extends AfterSalesProcessService
{
    protected const START_STATUS = 13;
    protected const CONCLUSION_STATUS = 14;
    protected const SUB_STATUS_APPROVED = 'Aprovado';

    protected function setStatusAndMarkItem(string $item, Model $model, array $checklist, object $helper): void
    {
        $translatedItem = $helper::translateItem($item);
        $checklist[$translatedItem] = true;
        unset($checklist[""]);
        unset($checklist["Unifilar anexado"]);
        unset($checklist["Em Análise na concessionária"]);

        $model->checklist = json_encode($checklist);
        $model->status_id = $helper::matchStatus(key: $item);

        $model->update();
    }
}
