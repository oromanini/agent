<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\User;
use App\Repositories\ApprovalRepository;
use App\Services\ApprovalService;
use App\Services\ContractService;
use App\Services\FinancingService;
use App\Services\InspectionService;
use App\Services\ProposalValueHistoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function __construct(
        private readonly ApprovalRepository          $approvalRepository,
        private readonly ProposalValueHistoryService $valueHistoryService,
        private readonly InspectionService           $inspectionService,
        private readonly FinancingService            $financingService,
        private readonly ContractService             $contractService,
    )
    {
    }

    public function index(Request $request): View
    {
        $data = $request->all();
        $approvals = $this->approvalRepository->filter($data);
        $agents = User::all();

        return view('approval.index', compact('approvals', 'agents'));
    }

    public function show($id): View
    {
        $proposal = Proposal::find($id);
        $client = $proposal->client;
        $valueHistoryData = $this->valueHistoryService->setValueHistoryData($proposal);
        $kits = $this->setKits($proposal);
        $isPromotional = $this->isPromotional($proposal);
        $contractStatuses = setContractStatuses();
        $inspectionStatuses = setInspectionStatuses();
        $financingStatuses = setFinancingStatuses();

        $inspection = $proposal->inspection ?: null;
        $financing = $proposal->financing ?: null;
        $contract = $proposal->contract ?: null;

        return view('approval.show', compact($this->setApprovalParams()));
    }

    public function updateContract($id, Request $request): RedirectResponse
    {
        $this->contractService->update(
            model: ApprovalService::CONTRACT,
            proposalId: $id,
            request: $request
        );

        $this->flashUpdateMessage(model: ApprovalService::CONTRACT);
        return redirect()->back();
    }

    public function updateInspection($id, Request $request): RedirectResponse
    {
        $this->inspectionService->update(
            model: ApprovalService::INSPECTION,
            proposalId: $id,
            request: $request
        );

        $this->flashUpdateMessage(model: ApprovalService::INSPECTION);
        return redirect()->back();
    }

    public function updateFinancing($id, Request $request): RedirectResponse
    {
        $this->financingService->update(
            model: ApprovalService::FINANCING,
            proposalId: $id,
            request: $request
        );

        $this->flashUpdateMessage(model: ApprovalService::FINANCING);
        return redirect()->back();
    }

    private function setKits($proposal)
    {
        return $proposal->is_manual
            ? json_decode($proposal->components, true)
            : getKitCodesFromProposal($proposal);
    }

    private function isPromotional($proposal): bool
    {
        return false;
//            ($proposal->number_of_panels == 4
//                || $proposal->number_of_panels == 8
//                || $proposal->number_of_panels == 12
//                || $proposal->number_of_panels == 14)
//            &&
//            (
//                $proposal->kwp == 2.2
//                || $proposal->kwp == 4.4
//                || $proposal->kwp == 6.6
//                || $proposal->kwp == 7.7);
    }

    private function setApprovalParams(): array
    {
        return [
            'proposal',
            'kits',
            'valueHistoryData',
            'isPromotional',
            'contractStatuses',
            'inspectionStatuses',
            'financingStatuses',
            'inspection',
            'financing',
            'contract',
            'client'
        ];
    }

    private function flashUpdateMessage(string $model): void
    {
        session()->flash('message', ['success', "{$model} atualizado(a)!"]);
    }
}
