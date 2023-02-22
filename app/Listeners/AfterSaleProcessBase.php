<?php

namespace App\Listeners;

use App\Helpers\HomologationHelper;
use App\Models\Proposal;
use App\Services\AfterSalesProcessService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AfterSaleProcessBase extends AfterSalesProcessService
{
    private const CONCLUSION_STATUS = 14;
    private const SUB_STATUS_APPROVED = 'Aprovado';

    protected function setStatusAndMarkItem(string $item, Model $model, array $checklist, object $helper): void
    {
        $translatedItem = $helper::translateItem($item);
        $checklist[$translatedItem] = true;
        unset($checklist[""]);
        unset($checklist["Unifilar anexado"]);

        $model->checklist = json_encode($checklist);
        $model->status_id = $helper::matchStatus(key: $item);

        $finished = $model->status->is_final && $model->is_approved_on_dealership == self::SUB_STATUS_APPROVED;

        $finished && $model->status_id = self::CONCLUSION_STATUS;

        $this->isReadyForNextDepartment(modelName: $helper::MODEL_NAME, model: $model)
            && $this->sendToNextDepartment(modelName: $helper::MODEL_NAME, proposal: $model->proposal);

        $model->update();
    }

    private function isReadyForNextDepartment(string $modelName, Model $model): bool
    {
        if ($modelName == HomologationHelper::MODEL_NAME) {
            return !isset($model->proposal->installation) && isset($model->single_line_project);
        }

        return false;
    }

    private function sendToNextDepartment(string $modelName, Proposal $proposal): void
    {
        $nextDepartment = null;

        if ($modelName == HomologationHelper::MODEL_NAME) {
            $nextDepartment = new Installation();
            $nextDepartment->proposal_id = $proposal->id;
        }

        DB::transaction(function () use ($nextDepartment) {
            $nextDepartment->save();
        });
    }
}
