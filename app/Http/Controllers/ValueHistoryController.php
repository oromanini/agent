<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Services\ProposalValueHistoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ValueHistoryController extends Controller
{
    protected $valueHistoryService;

    public function __construct(ProposalValueHistoryService $valueHistoryService)
    {
        $this->valueHistoryService = $valueHistoryService;
    }

    public function applyCommissionOrDiscount($id, Request $request): RedirectResponse
    {
        $proposal = Proposal::find($id);
        $valueHistory = $proposal->valueHistory;

        $message = $this->valueHistoryService->update($valueHistory, $request->all());

        session()->flash('message', $message);

        return redirect()->route('proposal.edit', [$proposal->id]);
    }

}
