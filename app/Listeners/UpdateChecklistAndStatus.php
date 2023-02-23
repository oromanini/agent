<?php

namespace App\Listeners;

use App\Events\AfterSaleProcessDepartmentChanged;
use Illuminate\Database\Eloquent\Model;

class UpdateChecklistAndStatus extends AfterSaleProcessBase
{

    public function handle(AfterSaleProcessDepartmentChanged $event): void
    {


        $helper = new ('App\\Helpers\\' . class_basename($event->model) . 'Helper');

        foreach ($helper::setListenedFields() as $field) {

            $updatedModel = self::setModel($event->model);

            isset($updatedModel->$field)
            && $this->setStatusAndMarkItem(
                item: $field,
                model: $updatedModel,
                checklist: json_decode($updatedModel->checklist, true),
                helper: $helper
            );
        }
    }

    private static function setModel(Model $model)
    {
        return ('App\\Models\\' . class_basename($model))::find($model->id);
    }
}
