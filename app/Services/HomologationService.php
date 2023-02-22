<?php

namespace App\Services;

use App\Enums\DepartmentsEnum;
use App\Models\Homologation;
use App\Models\Proposal;
use App\Models\Status;

class HomologationService extends AfterSalesProcessService
{
    public const CHECKLIST_ITEM_DUPLICATE_EMITTED = 'Boleto TRT Emitido';
    public const CHECKLIST_ITEM_DUPLICATE_PAYED = 'Boleto TRT Pago';
    public const CHECKLIST_ITEM_ACCESS_FORM_EMITTED = 'Formulário de acesso emitido';
    public const CHECKLIST_ITEM_ACCESS_FORM_SIGNED = 'Formulário de acesso assinado';

    public function store(Proposal $proposal):void
    {
        $generalStatus = Status::where('department', DepartmentsEnum::GENERAL)->first()->id;

        Homologation::create([
            'proposal_id' => $proposal->id,
            'status_id' => $generalStatus,
            'checklist' => self::getChecklist()
        ]);
    }

    public static function getChecklist(): string
    {
        return json_encode([
            self::CHECKLIST_ITEM_DUPLICATE_EMITTED => false,
            self::CHECKLIST_ITEM_DUPLICATE_PAYED => false,
            self::CHECKLIST_ITEM_ACCESS_FORM_EMITTED => false,
            self::CHECKLIST_ITEM_ACCESS_FORM_SIGNED => false,
        ]);
    }
}
