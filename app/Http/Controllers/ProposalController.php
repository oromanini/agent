<?php

namespace App\Http\Controllers;

use App\Enums\InverterBrands;
use App\Enums\PanelBrands;
use App\Enums\TensionPattern;
use App\Http\Requests\ProposalRequest;
use App\Models\Address;
use App\Models\Client;
use App\Models\Proposal;
use App\Models\User;
use App\Repositories\ProposalRepository;
use App\Services\PaybackService;
use App\Services\PricingService;
use App\Services\ProposalService;
use App\Services\ProposalValueHistoryService;
use App\Services\SolarIncidenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ProposalController extends Controller
{
    private $proposalService;
    private $proposalRepository;
    private $proposalValueHistoryService;
    private $solarIncidenceService;
    private $paybackService;
    private $pricingService;

    public function __construct(ProposalService             $proposalService,
                                ProposalRepository          $proposalRepository,
                                ProposalValueHistoryService $proposalValueHistoryService,
                                PaybackService              $paybackService,
                                SolarIncidenceService       $solarIncidenceService,
                                PricingService              $pricingService
    )
    {
        $this->proposalService = $proposalService;
        $this->proposalRepository = $proposalRepository;
        $this->proposalValueHistoryService = $proposalValueHistoryService;
        $this->solarIncidenceService = $solarIncidenceService;
        $this->paybackService = $paybackService;
        $this->pricingService = $pricingService;
    }

    public function index(Request $request)
    {
        $proposals = $this->proposalRepository->filter($request->all());
        $agents = User::all();

        return view('proposals.index', compact('proposals', 'agents'));
    }

    public function create()
    {
        $clients = null;

        if (auth()->user()->is_admin) {
            $clients = Client::query()->orderBy('id', 'desc')->get();
        } else {
            $clients = Client::query()->where('agent_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        }

        $tensions = $this->setTensions();
        $roofs = setRoofs();

        return view('proposals.form', compact('clients', 'tensions', 'roofs'));
    }

    public function store(ProposalRequest $request): RedirectResponse
    {
        $request->validated();
        $proposal = $this->proposalService->store($request->all());

        return redirect()->route('proposal.edit', [$proposal->id]);
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

    public function delete($id)
    {
        $proposal = Proposal::find($id);
        $proposal->delete();

        return redirect()->back();
    }

    public function edit($id)
    {
        $proposal = Proposal::find($id);
        $valueHistoryData = $this->proposalValueHistoryService->setValueHistoryData($proposal);
        $kits = $proposal->is_manual ? json_decode($proposal->components, true) : getKitCodesFromProposal($proposal);
        $isPromotional = false;


        return view('proposals.show', compact('proposal', 'valueHistoryData', 'kits', 'isPromotional'));
    }

    public function approve($id): RedirectResponse
    {
        $proposal = Proposal::find($id);

        if (is_null($proposal->send_date)) {
            $proposal->send_date = now();
            $proposal->save();
            session()->flash('message', ['success', 'Enviado para aprovação!']);
        } else {
            session()->flash('message', ['error', 'Proposta já formalizada! Em caso de dúvidas, fale com a equipe da Alluz']);
        }

        return redirect()->back();
    }

    public function manualStore(Request $request): RedirectResponse
    {
        $message = null;
        $data = $request->all();

        try {
            $message = $this->proposalService->store($data, true);

        } catch (\Exception $e) {
            throw new \Exception($e);
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
        $pdfParams = $this->setPdfParams($proposal);
        $city = $proposal->client->addresses->first()->city;
        $components = json_decode($proposal->components, true);
        $firstKit = $proposal->is_manual ? null : kitByUuid(getKitCodesFromProposal($proposal)[0]);

        $manualData = $proposal->is_manual ? json_decode($proposal->manual_data, true) : null;
        $inverterImage = $proposal->is_manual ? setInverterImage((int)$manualData['inverter_brand']) : setInverterImage($firstKit['technical_description']['inverter_brand']);
        $panelBrandImage = $proposal->is_manual ? setPanelBrandImage((int)$manualData['panel_brand']) : setPanelBrandImage($firstKit['technical_description']['panel_specs']['panel_brand']);

        $withoutSolar = calculateWithoutSolar($proposal);
        $withSolar = floatToMoney(calculateWithSolar($proposal));
        $incidence = $this->solarIncidenceService->getSolarIncidence($city)->average;
        $payback = $this->paybackService->setPaybackData($proposal);
        $generationData = $this->paybackService->setGeterationData($proposal);

        $invertersCount = $proposal->is_manual ? $manualData['inverter_quantity'] : $this->setInvertersCount($components);

        $pdf = PDF::loadView('proposals.pdf', compact($pdfParams));
        return $pdf->stream('#' . $proposal->id . ' ' . $proposal->client->name . '.pdf');
    }

    public function setFinalValue(Request $request): float
    {
        $data = $request->all();
        return $this->pricingService->calculateFinalPrice($data);
    }

    public function setAverageProduction(Request $request)
    {
        $data = $request->all();
        $city = Address::find((int)$data['addressId'])->city;
        $incidence = (float)str_replace(',', '.', $this->solarIncidenceService->getSolarIncidence($city)->average);

        return ceil((float)$data['kwp'] * 30 * ($incidence - (float)env('GENERATION_LOST')));
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

    private function setPdfParams($proposal): array
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
            'firstKit',
            'invertersCount'
        ];
    }

    private function setTensions(): array
    {
        return [
            'MONOFASICO-220V' => 'Monofásico 220V',
            'BIFASICO-220V' => 'Bifásico 220V',
            'TRIFASICO-220V' => 'Trifásico 220V',
            'TRIFASICO-380V' => 'Trifásico 380V',
        ];
    }

    private function setInvertersCount(array $components)
    {
        $inverter_description = null;

        array_map(function ($item) use (&$inverter_description) {
            (str_contains($item, 'Inversor')) && $inverter_description = $item;

        }, $components);

        return $inverter_description[0];
    }


}

