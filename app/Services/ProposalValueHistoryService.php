<?php

namespace App\Services;

use App\Models\ProposalValueHistory;
use Illuminate\Support\Facades\DB;

class ProposalValueHistoryService
{
    public function store($data): int
    {
        $valueHistory = new ProposalValueHistory();

        $valueHistory->kit_cost = stringMoneyToFloat($data['cost']);
        $valueHistory->initial_price = stringMoneyToFloat($data['final_value']);
        $valueHistory->final_price = stringMoneyToFloat($data['final_value']);
        $valueHistory->commission_percent = env('COMMISSION_PERCENT');
        $valueHistory->discount_percent = 0;
        $valueHistory->user_id = auth()->user()->id;

        DB::transaction(function () use ($valueHistory) {
            $valueHistory->save();
        });

        return $valueHistory->id;
    }

    public function update($valueHistory, $data): array
    {
        dd($data);


        return ['success', 'Alteração de valor aplicada!'];
    }

}
