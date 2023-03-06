<?php

namespace App\Services;

use App\Events\AfterSaleProcessDepartmentChanged;
use App\Helpers\HomologationHelper;
use App\Listeners\AfterSaleProcessBase;
use App\Models\Homologation;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class AfterSalesProcessService
{
    const MODEL_PATH = 'App\\Models\\';
    const SERVICE_PATH = 'App\\Services\\';

    public function update(Model $model, Request $request): void
    {
        $data = $request->all();

        DB::transaction(function () use ($model, $data) {
            $data = $this->convertMonetaryValues($data);
            $model->update($data);
        });

        $this->checkFiles(model: $model, request: $request);
    }

    protected function checkFiles(Model $model, Request $request): void
    {
        $modelToLower = strtolower(class_basename($model));

        foreach ($request->allFiles() as $name => $file) {
            (isset($model->$name))
            && $model->$name = $file->store("public/$modelToLower/$name/$model->id");
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
        $previousDepartment = $proposal->$modelName;
        $nextDepartment = $this->setNextDepartment($modelName);

        if (is_null($previousDepartment->proposal->$nextDepartment)) {

            $nextDepartmentInstance = new (self::MODEL_PATH . ucfirst($nextDepartment));
            $nextDepartmentService = new (self::SERVICE_PATH . ucfirst($nextDepartment) . 'Service');

            $nextDepartmentInstance->status_id = AfterSaleProcessBase::START_STATUS;
            $nextDepartmentInstance->proposal_id = $proposal->id;
            $nextDepartmentInstance->checklist = $nextDepartmentService::getChecklist();

            DB::transaction(function () use ($nextDepartmentInstance) {
                $nextDepartmentInstance->save();
            });
        }
    }

    private function setNextDepartment(string $modelName): string
    {
        return match ($modelName) {
            'homologation' => 'installation',
            'installation' => 'finalInspection',
            default => null
        };
    }

    private function convertMonetaryValues(array $data): array
    {
        $data['ca_cost'] = stringMoneyToFloat($data['ca_cost']);
        $data['installation_cost'] = stringMoneyToFloat($data['installation_cost']);

        return $data;
    }
}
