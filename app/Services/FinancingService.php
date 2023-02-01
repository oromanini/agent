<?php

namespace App\Services;

use App\Models\Financing;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;

class FinancingService
{
    public function update(int $id, array $data): void
    {
        $proposal = Proposal::find($id);

        DB::transaction(function () use ($data, $proposal) {
            is_null($proposal->financing)
                ? $this->createFinancing($data, $proposal)
                : ($proposal->financing)->update($data);
        });

        $this->checkFiles($proposal, $data);
    }

    private function createFinancing(array $data, Proposal $proposal)
    {
        $financing = (new Financing())::create($data);
        $proposal->financing()->associate($financing);
        $proposal->update();
        dd($proposal->financing);
    }

    private function checkFiles($proposal, array $data): void
    {
        if (isset($data['proof_of_income'])) {
            $proposal->financing = $data['proof_of_income']
                ->store('public/proof_of_income/financing_' . $proposal->financing->id);

            $proposal->financing->update();
        }
    }
}
