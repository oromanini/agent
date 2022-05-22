<?php

namespace App\Http\Controllers;

use App\Enums\InverterBrands;
use App\Enums\PanelBrands;
use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\Client;
use App\Models\Proposal;
use App\Models\State;
use App\Models\User;
use App\Repositories\ProposalRepository;
use App\Services\PaybackService;
use App\Services\PreInspectionService;
use App\Services\ProposalService;
use App\Services\ProposalValueHistoryService;
use App\Services\SolarIncidenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;

class ProposalController extends Controller
{
    private $proposalService;
    private $proposalRepository;
    private $proposalValueHistoryService;
    private $preInspectionService;
    private $solarIncidenceService;
    private $paybackService;

    public function __construct(ProposalService             $proposalService,
                                ProposalRepository          $proposalRepository,
                                ProposalValueHistoryService $proposalValueHistoryService,
                                PreInspectionService        $preInspectionService,
                                PaybackService              $paybackService,
                                SolarIncidenceService       $solarIncidenceService)
    {
        $this->proposalService = $proposalService;
        $this->proposalRepository = $proposalRepository;
        $this->proposalValueHistoryService = $proposalValueHistoryService;
        $this->preInspectionService = $preInspectionService;
        $this->solarIncidenceService = $solarIncidenceService;
        $this->paybackService = $paybackService;
    }

    public function index(Request $request)
    {
        $proposals = $this->proposalRepository->filter($request->all());
        $agents = User::all();

        return view('proposals.index', compact('proposals', 'agents'));
    }

    public function create()
    {
        $clients = auth()->user()->is_admin
            ? Client::all()
            : Client::where('agent_id', auth()->user()->id)->get();

        $tensions = TensionPattern::asSelectArray();
        $roofs = setRoofs();

        return view('proposals.form', compact('clients', 'tensions', 'roofs'));
    }

    public function manual()
    {
        $clients = Client::all();
        $agents = User::all();
        $tensions = TensionPattern::asSelectArray();
        $roofs = setRoofs();
        $panels = PanelBrands::asSelectArray();
        $inverters = InverterBrands::asSelectArray();

        return view('proposals.manual', compact($this->setManualParams()));
    }

    public function edit($id)
    {
        $proposal = Proposal::find($id);

        return view('proposals.show', compact('proposal'));
    }

    public function approve($id)
    {
        $proposal = Proposal::find($id);

        dd($proposal);
    }

    public function manualStore(Request $request): RedirectResponse
    {
        $message = null;
        $city = Client::find($request->all()['client'])->addresses->first()->city;
        $incidence = $this->solarIncidenceService->getSolarIncidence($city);
        $proposal = $this->proposalService->fillObject($request->all(), $incidence);

        try {

            $proposal->pre_inspection_id = $this->proposalValueHistoryService->store($request->all());
            $proposal->value_history_id = $this->preInspectionService->store();
            $message = $this->proposalService->store($proposal);

        } catch (\Exception $e) {
//            throw new \Exception($e);
            session()->flash('message', ['error' => $e]);
            return redirect()->route('proposal.index');

        }

        session()->flash('message', $message);
        return redirect()->route('proposal.index');

    }

    /**
     * @throws \Exception
     */
    public function generatePdf($proposal_id): Response
    {
        $proposal = Proposal::find($proposal_id);
        $city = $proposal->client->addresses->first()->city;
        $components = json_decode($proposal->components, true);
        $manualData = $proposal->is_manual ? json_decode($proposal->manual_data, true) : null;
        $inverterImage = setInverterImage((int)$manualData['inverter_brand']);
        $panelBrandImage = setPanelBrandImage((int)$manualData['panel_brand']);
        $withoutSolar = calculateWithoutSolar($proposal);
        $withSolar = floatToMoney(calculateWithSolar($proposal));
        $incidence = $this->solarIncidenceService->getSolarIncidence($city)->average;
        $payback = $this->paybackService->setPaybackData($proposal);
        $generationData = $this->paybackService->setGeterationData($proposal);

        $pdf = PDF::loadView('proposals.pdf', compact($this->setPdfParams()));
        return $pdf->stream('#' . $proposal->id . ' '. $proposal->client->name. '.pdf');
    }

    private function setManualParams(): array
    {
        return [
            'clients',
            'agents',
            'tensions',
            'roofs',
            'panels',
            'inverters',
        ];
    }

    private function setPdfParams(): array
    {
        return [
            'proposal',
            'components',
            'manualData',
            'inverterImage',
            'panelBrandImage',
            'withoutSolar',
            'withSolar',
            'incidence',
            'payback',
            'generationData',
        ];
    }

}

