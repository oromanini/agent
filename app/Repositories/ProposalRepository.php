<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Proposal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProposalRepository implements Filter
{
    public function filter($data)
    : LengthAwarePaginator
    {
        return Proposal::query()
            ->where(function ($query) use($data) {
                filterName($data, $query);
                filterAgent($data, $query);
                filterDocument($data, $query);
                filterInitialDate($data, $query);
                filterFinalDate($data, $query);
                filterPermission($data, $query);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
    }
}
