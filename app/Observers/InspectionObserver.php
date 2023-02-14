<?php

namespace App\Observers;

use App\Models\Inspection;
use App\Services\ApprovalService;

class InspectionObserver
{
    public function updated(Inspection $inspection): void
    {
        ApprovalService::sendToHomologation(proposal: $inspection->proposal);
    }
}
