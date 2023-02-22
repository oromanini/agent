<?php

namespace App\Listeners;

use App\Events\AfterSaleProcessDepartmentChanged;

class UpdateChecklistAndStatus extends AfterSaleProcessBase
{

    public function handle(AfterSaleProcessDepartmentChanged $event): void
    {
        $model = $event->model;

//        $checklist = json_decode($model->checklist, true);
        $helper = new ('App\\Helpers\\' . class_basename($model) . 'Helper');

        foreach ($helper::setListenedFields() as $field) {
            isset($model->$field)
            && $this->setStatusAndMarkItem(
                item: $field,
                model: $model,
                checklist: json_decode($model->checklist, true),
                helper: $helper
            );
        }
    }
}
