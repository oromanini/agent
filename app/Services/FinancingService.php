<?php

namespace App\Services;

use App\Models\Financing;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;

class FinancingService
{
    public function update(int $id, array $data)
    {
        $proposal = Proposal::find($id);
        dd($data);

        if (is_null($proposal->inspection)) {

            $financing = new Financing();

        } else {
            $financing = $proposal->financing;

        }
    }
}
