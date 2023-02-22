<?php

namespace App\Services;

use App\Events\AfterSaleProcessDepartmentChanged;
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

        DB::transaction(function () use ($model) {
            $model->update();
            event(new AfterSaleProcessDepartmentChanged(model: $model));
        });
    }
}
