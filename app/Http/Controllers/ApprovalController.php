<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\User;
use App\Repositories\ApprovalRepository;
use App\Services\ProposalValueHistoryService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    private $approvalRepository;
    private $valueHistoryService;

    public function __construct(ApprovalRepository $approvalRepository, ProposalValueHistoryService $valueHistoryService)
    {
        $this->approvalRepository = $approvalRepository;
        $this->valueHistoryService = $valueHistoryService;
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
        $kits = $proposal->is_manual ? json_decode($proposal->components, true) : getKitCodesFromProposal($proposal);
        $isPromotional = ($proposal->number_of_panels == 4 || $proposal->number_of_panels == 8 || $proposal->number_of_panels == 12 || $proposal->number_of_panels == 14) && ($proposal->kwp == 2.2 || $proposal->kwp == 4.4 || $proposal->kwp == 6.6 || $proposal->kwp == 7.7);


        return view('approval.show', compact('proposal', 'kits', 'valueHistoryData', 'isPromotional'));
    }
}
