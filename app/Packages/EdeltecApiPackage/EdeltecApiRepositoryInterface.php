<?php

namespace App\Packages\EdeltecApiPackage;

use Illuminate\Support\Collection;

interface EdeltecApiRepositoryInterface
{
    public function getAllActiveKits(): Collection;
}
