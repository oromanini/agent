<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\User;
use App\Repositories\ApprovalRepository;
use App\Services\ContractService;
use App\Services\FinancingService;
use App\Services\InspectionService;
use App\Services\ProposalValueHistoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    private ApprovalRepository $approvalRepository;
    private ProposalValueHistoryService $valueHistoryService;
    private InspectionService $inspectionService;
    private FinancingService $financingService;
    private ContractService $contractService;

    public function __construct(
        ApprovalRepository $approvalRepository,
        ProposalValueHistoryService $valueHistoryService,
        InspectionService $inspectionService,
        FinancingService $financingService,
        ContractService $contractService
    )
    {
        $this->approvalRepository = $approvalRepository;
        $this->valueHistoryService = $valueHistoryService;
        $this->inspectionService = $inspectionService;
        $this->financingService = $financingService;
        $this->contractService = $contractService;
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $approvals = $this->approvalRepository->filter($data);
        $agents = User::all();

        return view('approval.index', compact('approvals', 'agents'));
    }

    public function show($id)
    {
        $proposal = Proposal::find($id);
        $valueHistoryData = $this->valueHistoryService->setValueHistoryData($proposal);
        $kits = $this->setKits($proposal);
        $isPromotional = $this->isPromotional($proposal);
        $contractStatuses = $this->setContractStatuses();
        $inspectionStatuses = $this->setInspectionStatuses();
        $financingStatuses = $this->setFinancingStatuses();

        $inspection = $proposal->inspection ?: null;
        $financing = $proposal->financing ?: null;
        $contract = $proposal->contract ?: null;

        return view('approval.show', compact($this->setApprovalParams()));
    }

    public function updateContract($id, Request $request): RedirectResponse
    {
        $data = $request->all();
        $this->contractService->update($id, $data);

        session()->flash('message', ['success', 'Contrato atualizado!']);

        return redirect()->back();
    }

    public function updateInspection($id, Request $request): RedirectResponse
    {
        $data = $request->all();
        $this->inspectionService->update($id, $data);

        session()->flash('message', ['success', 'Vistoria atualizada!']);

        return redirect()->back();
    }

    public function updateFinancing($id, Request $request): RedirectResponse
    {
        $data = $request->all();
        $this->financingService->update($id, $data);

        session()->flash('message', ['success', 'Financiamento atualizado!']);

        return redirect()->back();
    }

    private function setContractStatuses(): array
    {
        return [
            'Aguardando',
            'Aguardando assinatura',
            'Finalizado',
        ];
    }

    private function setInspectionStatuses(): array
    {
        return [
            'Aguardando',
            'Aguardando fotos agente',
            'Aprovado',
            'Aprovado com adequação',
            'Reprovado',
        ];
    }

    private function setFinancingStatuses(): array
    {
        return [
            'Aguardando',
            'Em análise',
            'Reprovado',
            'Aprovado',
            'À Vista',
            'Cartão',
            '60/40'
        ];
    }

    private function setKits($proposal)
    {
        return $proposal->is_manual ? json_decode($proposal->components, true) : getKitCodesFromProposal($proposal);
    }

    private function isPromotional($proposal): bool
    {
        return ($proposal->number_of_panels == 4
                || $proposal->number_of_panels == 8
                || $proposal->number_of_panels == 12
                || $proposal->number_of_panels == 14)
            &&
            (
                $proposal->kwp == 2.2
                || $proposal->kwp == 4.4
                || $proposal->kwp == 6.6
                || $proposal->kwp == 7.7);
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
        ];
    }
}
