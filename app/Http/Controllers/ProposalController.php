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
        $proposal = $this->fillProposal($request->all(), $incidence);

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
        return $pdf->stream('Alluz_' . $proposal->id . '.pdf');
    }

    private function fillProposal(array $data, $incidence): Proposal
    {
        $proposal = new Proposal();
        $proposal->is_manual = true;

        $proposal->uuid = Uuid::uuid6();
        $proposal->kit_uuid = Uuid::uuid6();

        $proposal->type = 'normal';
        $proposal->estimated_generation = $this->proposalService->calculateEstimatedGeneration($data, $incidence)['average'];
        $proposal->average_consumption = (float)$data['average_consumption'];
        $proposal->tension_pattern = $this->formatTension($data['tension_pattern']);
        $proposal->roof_structure = (int)$data['tension_pattern'];
        $proposal->number_of_panels = (int)$data['panel_quantity'];
        $proposal->kw_price = stringMoneyToFloat($data['kw_price']);
        $proposal->components = json_encode(explode(PHP_EOL, $data['components']));
        $proposal->client_id = (int)$data['client'];
        $proposal->agent_id = (int)$data['agent'];
        $proposal->kwp = (float)$data['kwp'];
        $proposal->manual_data = json_encode([
            'panel_brand' => $data['panel_brand'],
            'panel_model' => $data['panel_model'],
            'panel_power' => $data['panel_power'],
            'panel_warranty' => $data['panel_warranty'],
            'inverter_brand' => $data['inverter_brand'],
            'inverter_model' => $data['inverter_model'],
            'inverter_power' => $data['inverter_power'],
            'inverter_warranty' => $data['inverter_warranty'],
        ]);

        return $proposal;
    }

    private function formatTension($tension): string
    {

        if ($tension == 'Mono220') {
            return 'MONO-220';
        } elseif ($tension == 'Bi220') {
            return 'BI-220';
        } elseif ($tension == 'Tri220') {
            return 'TRI-220';
        } else {
            return 'TRI-380';
        }
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

