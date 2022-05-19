<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ClientRepository implements Filter
{
    public function filter($data): LengthAwarePaginator
    {
        return Client::query()
            ->where(function ($query) use($data) {
                filterName($data, $query);
                filterAgent($data, $query);
                filterDocument($data, $query);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
    }
}
