<?php

namespace App\Builders;

use App\Models\PreInspection;
use Illuminate\Database\Eloquent\Model;

class PreInspectionBuilder implements Builder
{
    private PreInspection $preInspection;

    public function __construct()
    {
        $this->preInspection = new PreInspection();
        $this->preInspection->roof = json_encode([]);
        $this->preInspection->pattern = json_encode([]);
        $this->preInspection->circuit_breaker = json_encode([]);
        $this->preInspection->switchboard = json_encode([]);
        $this->preInspection->post = json_encode([]);
        $this->preInspection->compass = json_encode([]);
        $this->preInspection->croqui = json_encode([]);
        $this->preInspection->circuit_breaker_amperage = "";
        $this->preInspection->observations = "";
    }

    public function build(): PreInspection
    {
        $this->preInspection->save();
        return $this->preInspection;
    }
}
