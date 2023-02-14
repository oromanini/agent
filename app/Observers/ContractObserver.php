<?php

namespace App\Observers;

use App\Models\Contract;
use App\Services\ApprovalService;

class ContractObserver
{
    public function updated(Contract $contract): void
    {
        ApprovalService::sendToHomologation(proposal: $contract->proposal);
    }
}
