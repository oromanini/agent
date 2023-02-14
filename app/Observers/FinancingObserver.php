<?php

namespace App\Observers;

use App\Models\Financing;
use App\Services\ApprovalService;

class FinancingObserver
{
    public function updated(Financing $financing): void
    {
        ApprovalService::sendToHomologation(proposal: $financing->proposal);
    }
}
