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
        return Homologation::query()->with(['proposal' => function ($query) {
            $query->withTrashed();
        }])
            ->where(function ($query) use($data) {
                filterName($data, $query);
                filterAgent($data, $query);
                filterDocument($data, $query);
                filterInitialDate($data, $query);
                filterFinalDate($data, $query);
            })
            ->where(function ($query) use($data) {
                if (!auth()->user()->isAdmin) {
                    $query->where('owner_id',auth()->id());
                    $query->orWhere('secondary_owner_id',auth()->id());
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
}
