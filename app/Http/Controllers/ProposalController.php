<?php

namespace App\Http\Controllers;

use App\Enums\InverterBrands;
use App\Enums\PanelBrands;
use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Http\Requests\ProposalRequest;
use App\Models\Address;
use App\Models\Client;
use App\Models\Proposal;
use App\Models\State;
use App\Models\User;
use App\Repositories\ProposalRepository;
use App\Services\PaybackService;
use App\Services\PreInspectionService;
use App\Services\pricingService;
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
    private $pricingService;

    public function __construct(ProposalService             $proposalService,
                                ProposalRepository          $proposalRepository,
                                ProposalValueHistoryService $proposalValueHistoryService,
                                PreInspectionService        $preInspectionService,
                                PaybackService              $paybackService,
                                SolarIncidenceService       $solarIncidenceService,
                                pricingService              $pricingService
    )
    {
        $this->proposalService = $proposalService;
        $this->proposalRepository = $proposalRepository;
        $this->proposalValueHistoryService = $proposalValueHistoryService;
        $this->preInspectionService = $preInspectionService;
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

    public function edit($id)
    {
        $proposal = Proposal::find($id);
        $valueHistoryData = $this->setValueHistoryData($proposal);
        $kits = $proposal->is_manual ? json_decode($proposal->components, true) : getKitCodesFromProposal($proposal);

        return view('proposals.show', compact('proposal', 'valueHistoryData', 'kits'));
    }

    public function approve($id)
    {
        $proposal = Proposal::find($id);

        dd($proposal);
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


        return ceil((float)$data['kwp'] * 30 * $incidence);
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
            'firstKit'
        ];
    }

    private function setValueHistoryData($proposal): array
    {

        $valueHistory = $proposal->valueHistory;

        $discountPercent = $valueHistory->discount_percent / 100;
        $discountValue = $valueHistory->initial_price * $discountPercent;
        $calculateBase = $valueHistory->initial_price - $discountValue;

        $initialCommission = $calculateBase * ((float)env('COMMISSION_PERCENT'));
        $commissionPercent = $valueHistory->commission_percent;
        $commissionValue = $calculateBase * ($commissionPercent / 100);
        $commissionDiscountValue = $initialCommission - $commissionValue;

        $grossProfit = ($valueHistory->final_price / $proposal->valueHistory->kit_cost) - 1;

        $totalCost = $this->setTotalCost($proposal);


        return [
            'discountValue' => $discountValue,
            'calculateBase' => $calculateBase,
            'initialCommission' => $initialCommission,
            'finalCommission' => $commissionValue,
            'commissionDiscountValue' => $commissionDiscountValue,
            'cost' => floatToMoney($proposal->valueHistory->kit_cost),
            'gross_profit' => $grossProfit,
            'totalCost' => $totalCost

        ];
    }

    private function setTensions(): array
    {
        return [
            'MONOFASICO-220V' => 'Monofásico 220V',
            'BIFASICO-220V' => 'Bifásico 220V',
            'TRIFASICO-220V' => 'Trifásico 220V',
            'TRIFASICO-380V' => 'Trifásico 220V',
        ];
    }

    private function setTotalCost($proposal): array
    {
        $installation = $proposal->number_of_panels * env('INSTALLATION_PANEL_PRICE');
        $homologation = $this->pricingService->calculateHomologation($proposal->kwp, $proposal->valueHistory->final_price);
        $ca = $this->pricingService->calculateCa($proposal->valueHistory->final_price, $proposal->kwp);
        $tax = $proposal->valueHistory->final_price * env('TAX_PERCENT');
        $commission = ($proposal->valueHistory->commission_percent / 100) * $proposal->valueHistory->final_price;
        $servicesCost = $installation + $homologation + $ca + $tax + $commission;
        $netProfitValue = $proposal->valueHistory->final_price - ($proposal->valueHistory->kit_cost + $servicesCost);
        $netProfitPercent = $netProfitValue / $proposal->valueHistory->final_price;

        return [
            'installation' => $installation,
            'homologation' => $homologation,
            'ca' => $ca,
            'tax' => $tax,
            'commission' => $commission,
            'net_profit_value' => $netProfitValue,
            'net_profit_percent' => $netProfitPercent,
            'services_cost' => $servicesCost
        ];
    }

}

