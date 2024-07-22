<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Proposal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProposalRepository implements Filter
{
    public function filter($data): LengthAwarePaginator
    {
        return Proposal::query()->where('deleted_at', '=', null)
            ->where(function($query) use($data) {
                filterName($data, $query);
                filterAgent($data, $query);
                filterDocument($data, $query);
                filterInitialDate($data, $query);
                filterFinalDate($data, $query);
            })
            ->where(function($query) use($data) {
                if (auth()->user()->permission == 'agent') {
                    $query->where('agent_id', \auth()->user()->id);
                }
            })
            ->orderBy('proposals.id', 'desc')
            ->paginate(10);
    }
}
