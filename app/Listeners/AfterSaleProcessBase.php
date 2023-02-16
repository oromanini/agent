<?php

namespace App\Listeners;

use App\Services\AfterSalesProcessService;
use Illuminate\Database\Eloquent\Model;

class AfterSaleProcessBase extends AfterSalesProcessService
{
    protected const STATUS_AWAITING = 13;

    protected function setStatusAndMarkItem(string $item, Model $model, array $checklist, object $helper): void
    {
        $translatedItem = $helper::translateItem($item);

        (!$item == 'is_approved_on_dealership')
            ? $checklist[$translatedItem] = true
            : $checklist = $this->setDealershipStatus($item, $checklist, $model, $translatedItem);

        $model->checklist = json_encode($checklist);
        $model->status_id = $helper::matchStatus(key: $item);

        $model->update();
    }

    private function setDealershipStatus(string $item, array $checklist, Model $model, string $translatedItem): array
    {
        if ($model->$item == 'Em Análise') {
            unset($checklist[$translatedItem]);
            $checklist['Em Análise na concessionária'] = true;

        } elseif ($model->$item == 'Reprovado') {
            unset($checklist[$translatedItem]);
            if (isset($checklist['Em Análise na concessionária'])) { unset($checklist['Em Análise na concessionária']);}
            $checklist['Reprovado na concessionária'] = false;

        } else {
            if (isset($checklist['Reprovado na concessionária'])) { unset($checklist['Reprovado na concessionária']);}
            if (isset($checklist['Em Análise na concessionária'])) { unset($checklist['Em Análise na concessionária']);}
            $checklist['Aprovado na concessionária'] = true;
        }

        return $checklist;
    }
}
