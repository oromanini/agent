<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements Filter
{
    public function filter($data): LengthAwarePaginator
    {
        return User::query()
            ->withTrashed()
            ->where(function ($query) use($data) {
                filterName($data, $query);
                filterCnpj($data, $query);
                filterPhoneNumber($data, $query);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
    }
}
