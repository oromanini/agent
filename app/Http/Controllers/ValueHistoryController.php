<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Services\ProposalValueHistoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ValueHistoryController extends Controller
{
    const MAX_DISCOUNT_PERCENT = 3;

    public function __construct(private readonly ProposalValueHistoryService $valueHistoryService)
    {}

    public function applyCommissionOrDiscount($id, Request $request): RedirectResponse
    {
        $data = $request->all();

         (isset($data['discount_percent']) && $this->isValidDiscount($data))
         && session()->flash('message', ['error', 'O desconto não pode ser maior do que 4%']);

        $proposal = Proposal::find($id);
        $valueHistory = $proposal->valueHistory;

        $message = $this->valueHistoryService->update($valueHistory, $request->all());

        session()->flash('message', $message);

        return redirect()->route('proposal.edit', [$proposal->id]);
    }

    private function isValidDiscount(array $data): bool
    {
        return $data['discount_percent'] > self::MAX_DISCOUNT_PERCENT
            || $data['discount_percent'] < 0;
    }

}
