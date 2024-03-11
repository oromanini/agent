<?php

namespace App\Packages\EdeltecApiPackage;

use App\Models\Kit;
use Illuminate\Support\Collection;

class EdeltecApiRepository implements EdeltecApiRepositoryInterface
{
    public function getAllActiveKits(): Collection
    {
        return Kit::query()
            ->where('is_active', true)
            ->get();
    }
}
