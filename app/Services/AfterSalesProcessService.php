<?php

namespace App\Services;

use App\Events\AfterSaleProcessDepartmentChanged;
use App\Helpers\HomologationHelper;
use App\Listeners\AfterSaleProcessBase;
use App\Models\Homologation;
use App\Models\Installation;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class AfterSalesProcessService
{
    public function update(Model $model, Request $request): void
    {
        DB::transaction(function () use ($model, $request) {
            $model->update($request->all());
        });

        $this->checkFiles(model: $model, request: $request);
    }

    protected function checkFiles(Model $model, Request $request): void
    {
        $modelToLower = strtolower(class_basename($model));

        foreach ($request->allFiles() as $name => $file) {
            (isset($model->$name))
            && $model->$name = $file->store("public/$modelToLower/$model->id");
        }

        DB::transaction(function () use ($model, $modelToLower) {
            $model->update();
            event(new AfterSaleProcessDepartmentChanged($model));
            $this->finish($model);

            $this->isReadyForNextDepartment(modelName: $modelToLower, model: $model)
            && $this->sendToNextDepartment(modelName: $modelToLower, proposal: $model->proposal);
        });
    }

    private function finish(Model $model): void
    {
        $finished = $model instanceof Homologation
            ? $model->status->is_final && $model->is_approved_on_dealership == AfterSaleProcessBase::SUB_STATUS_APPROVED
            : $model->status->is_final;

        $finished && $model->status_id = AfterSaleProcessBase::CONCLUSION_STATUS;

        $model->update();
    }


    private function isReadyForNextDepartment(string $modelName, Model $model): bool
    {
        if (ucfirst($modelName) == HomologationHelper::MODEL_NAME) {
            return !isset($model->proposal->installation) && isset($model->single_line_project);
        }

        return false;
    }

    private function sendToNextDepartment(string $modelName, Proposal $proposal): void
    {
        $nextDepartment = null;

        (ucfirst($modelName) == HomologationHelper::MODEL_NAME) && $nextDepartment = new Installation();

        if (!is_null($nextDepartment)) {
            $nextDepartment->status_id = AfterSaleProcessBase::START_STATUS;
            $nextDepartment->proposal_id = $proposal->id;

            DB::transaction(function () use ($nextDepartment) {
                $nextDepartment->save();
            });
        }
    }
}
