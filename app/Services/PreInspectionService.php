<?php

namespace App\Services;

use App\Models\PreInspection;
use Illuminate\Support\Facades\DB;

class PreInspectionService
{
    public function store(): int
    {
        $preInspection = new PreInspection();

        DB::transaction(function () use ($preInspection) {
           $preInspection->save();
        });

        return $preInspection->id;
    }
}
