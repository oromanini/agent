<?php

namespace App\Services\Contracts;

use App\Models\Proposal;
use Illuminate\Http\Request;

interface ApprovalInterface
{
    function store(string $model, Proposal $proposal, Request $request): void;

    function update(string $model, int $proposalId, Request $request): void;

    function checkFiles(string $model, Proposal $proposal, Request $request): void;
}
