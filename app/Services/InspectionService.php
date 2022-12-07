<?php

namespace App\Services;

use App\Models\Inspection;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;

class InspectionService
{
    public function update(int $id, array $data)
    {
        $proposal = Proposal::find($id);

        if (is_null($proposal->inspection)) {

            $inspection = new Inspection();

            $inspection->status = isset($data['status']) ? $data['status'] : $inspection->status;
            $inspection->note = isset($data['note']) ? $data['note'] : $inspection->note;

            DB::transaction(function () use ($inspection, $proposal) {
               $inspection->save();
               $proposal->inspection_id = $inspection->id;
               $proposal->update();
            });

        } else {
            $inspection = $proposal->inspection;

            $inspection->status = $inspection->status == $data['status'] ? $inspection->status : $data['status'] ;
            $inspection->note = $inspection->note == $data['note'] ? $inspection->note : $data['note'];

            DB::transaction(function () use($inspection){
                $inspection->update();
            });
        }
    }
}
