<?php

namespace App\Repositories;

use App\Models\Homologation;
use App\Models\Proposal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HomologationRepository
{
    public function filter($data)
    : LengthAwarePaginator
    {
        return Homologation::withTrashed()->query()
            ->where(function ($query) use($data) {
                filterName($data, $query);
                filterAgent($data, $query);
                filterDocument($data, $query);
                filterInitialDate($data, $query);
                filterFinalDate($data, $query);
                filterPermission($data, $query);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
}
