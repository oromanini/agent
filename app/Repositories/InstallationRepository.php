<?php

namespace App\Repositories;

use App\Models\Installation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InstallationRepository
{
    public function filter($data)
    : LengthAwarePaginator
    {
        return Installation::query()->with(['proposal' => function ($query) {
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

    public function getPlusCosts(Installation $installation): array
    {
        return !is_null($installation->other_expenses)
            ? json_decode($installation->other_expenses, true)
            : [];
    }
}
