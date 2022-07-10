<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;

class ContractService
{
    public function update(int $id, array $data)
    {
        $proposal = Proposal::find($id);

        if (is_null($proposal->contract)) {

            $contract = new Contract();
            $contract->status = isset($data['status']) && $contract->status == $data['status'] ? $contract->status : $data['status'];
            $contract->file = isset($data['file']) ? $data['file']->store('public/contracts/'. $proposal->id) : $contract->file;
            $contract->signed_file = isset($data['signed_file']) ? $data['signed_file']->store('public/signed_contracts/'. $proposal->id) : $contract->signed_file;

            DB::transaction(function () use ($contract, $proposal) {
                $contract->save();
                $proposal->contract_id = $contract->id;
                $proposal->update();
            });

        } else {
            $contract = $proposal->contract;

            $contract->status = $contract->status == $data['status'] ? $contract->status : $data['status'];
            $contract->file = isset($data['file']) ? $data['file']->store('public/contracts/'. $proposal->id) : $contract->file;
            $contract->signed_file = isset($data['signed_file']) ? $data['signed_file']->store('public/signed_contracts/'. $proposal->id) : $contract->signed_file;

            DB::transaction(function () use ($contract) {
                $contract->update();
            });
        }
    }
}
