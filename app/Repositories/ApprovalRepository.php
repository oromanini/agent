<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Proposal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ApprovalRepository implements Filter
{
    public function filter($data): LengthAwarePaginator
    {
        return Proposal::query()
            ->where(function ($query) use($data) {
                filterName($data, $query);
                filterAgent($data, $query);
                filterDocument($data, $query);
                filterInitialDate($data, $query);
                filterFinalDate($data, $query);
            })
            ->whereHas('inspection', function ($query) {
                if (Auth::user()->permission == 'technical') {
                    $query->where('owner_id', Auth::user()->id);
                }
            })
            ->whereNotNull('send_date')
            ->orderBy('proposals.send_date', 'desc')
            ->paginate(20);
    }
}
