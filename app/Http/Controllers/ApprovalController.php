<?php

namespace App\Http\Controllers;

use App\Enums\DepartmentsEnum;
use App\Models\Contract;
use App\Models\Kit;
use App\Models\PromotionalKit;
use App\Models\Proposal;
use App\Models\Status;
use App\Models\User;
use App\Repositories\ApprovalRepository;
use App\Services\ApprovalService;
use App\Services\ContractService;
use App\Services\FinancingService;
use App\Services\InspectionService;
use App\Services\KitSpecService;
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
    {}

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
        $kits = $this::setKits($proposal);
        $isPromotional = $kits instanceof Kit && PromotionalKit::isPromotional($kits);

        $contractStatuses = Status::query()->where('department', DepartmentsEnum::CONTRACT)
            ->orWhere('department', DepartmentsEnum::GENERAL)->get();

        $inspectionStatuses = Status::query()->where('department', DepartmentsEnum::INSPECTION)
            ->orWhere('department', DepartmentsEnum::GENERAL)->get();

        $financingStatuses = Status::query()->where('department', DepartmentsEnum::FINANCING)
            ->orWhere('department', DepartmentsEnum::GENERAL)->get();

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

    public static function setKits(Proposal $proposal): array|Kit
    {
        return $proposal->components
            ? json_decode($proposal->components, true)
            : (new KitSpecService())->getKitFromProposal($proposal);
    }

    public function inactive(int $id): RedirectResponse
    {
        $approval = Proposal::find($id);
        $approval->delete();
        session()->flash('message', ['error', "Aprovação Inativada!"]);

        return redirect()->back();
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
