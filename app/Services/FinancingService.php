<?php

namespace App\Services;

use App\Models\Inspection;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;

class FinancingService
{
    public function update(int $id, array $data)
    {
        $proposal = Proposal::find($id);

        if (is_null($proposal->inspection)) {

            $inspection = new Inspection();
            $inspection->status = $inspection->status == $data['status'] ?? $data['status'];
            $inspection->note = $inspection->note == $data['note'] ?? $data['note'];

            DB::transaction(function () use ($inspection, $proposal) {
               $inspection->save();
               $proposal->inspection_id = $inspection->id;
               $proposal->update();
            });

        } else {
            $inspection = $proposal->inspection;

            $inspection->status = $proposal->inspection->status == $data['status'] ?? $data['status'];
            $inspection->note = $proposal->inspection->note == $data['note'] ?? $data['note'];

            DB::transaction(function () use($inspection){
                $inspection->update();
            });
        }
    }
}
