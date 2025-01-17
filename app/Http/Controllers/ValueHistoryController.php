<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Services\ProposalValueHistoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ValueHistoryController extends Controller
{
    const MAX_DISCOUNT_PERCENT = 3;
    const MAX_COMMISSION_PERCENT = 12;
    const MAX_CARD_COMMISSION_PERCENT = 10;

    public function __construct(private readonly ProposalValueHistoryService $valueHistoryService)
    {
    }

    public function applyCommissionOrDiscount($id, Request $request): RedirectResponse
    {
        $data = $request->all();
        $proposal = Proposal::find($id);

        if (!is_null($proposal->send_date)) {
            session()->flash('message', ['error', 'O desconto não pode ser aplicado após a aprovação']);
            return redirect()->back();
        }

        if (isset($data['discount_percent']) && $this->isValidDiscount($data)) {
            session()->flash('message', ['error', 'O desconto não pode ser maior do que 3%']);
            return redirect()->back();
        }

        if (isset($data["commission_percent"]) && $this->isValidCommission($data)) {
            session()->flash('message', ['error', 'A comissão ultrapassou o limite de 12%']);
            return redirect()->back();
        }

        if (isset($data["card_commission_percent"]) && $this->isValidCardCommission($data)) {
            session()->flash('message', ['error', 'A comissão ultrapassou o limite de 10%']);
            return redirect()->back();
        }

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

    private function isValidCommission(array $data): bool
    {
        return $data['commission_percent'] > self::MAX_COMMISSION_PERCENT
            || $data['commission_percent'] < 0;
    }

    private function isValidCardCommission(array $data): bool
    {
        return $data['card_commission_percent'] > self::MAX_CARD_COMMISSION_PERCENT
            || $data['card_commission_percent'] < 0;
    }
}
