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
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ProposalController extends Controller
{
    private ProposalService $proposalService;
    private ProposalRepository $proposalRepository;
    private ProposalValueHistoryService $proposalValueHistoryService;
    private SolarIncidenceService $solarIncidenceService;
    private PaybackService $paybackService;
    private PricingService $pricingService;

    public function __construct(
        ProposalService             $proposalService,
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

    public function index(Request $request): View
    {
        $proposals = $this->proposalRepository->filter($request->all());
        $agents = User::all();

        return view('proposals.index', compact('proposals', 'agents'));
    }

    public function create(): View
    {
        $clients = null;

        if (auth()->user()->is_admin) {
            $clients = Client::query()
                ->orderBy('id', 'desc')
                ->get();

        } else {
            $clients = Client::query()
                ->where('agent_id', auth()->user()->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        $tensions = $this->setTensions();
        $roofs = setRoofs();
        $agents = User::query()->orderBy('name')->get();

        return view('proposals.form', compact('clients', 'tensions', 'roofs', 'agents'));
    }

    public function store(ProposalRequest $request): RedirectResponse
    {
        $request->validated();
        $proposal = $this->proposalService->store($request->all());

        return redirect()->route('proposal.edit', [$proposal->id]);
    }

    public function manual(): View
    {
        $clients = Client::all();
        $agents = User::all();
        $tensions = TensionPattern::asSelectArray();
        $roofs = setRoofs();
        $panels = PanelBrands::asSelectArray();
        $inverters = InverterBrands::asSelectArray();

        return view('proposals.manual', compact($this->setManualParams()));
    }

    public function delete($id): RedirectResponse
    {
        $proposal = Proposal::find($id);
        $proposal->delete();

        return redirect()->back();
    }

    public function edit($id): View
    {
        $proposal = Proposal::find($id);
        $valueHistoryData = $this->proposalValueHistoryService->setValueHistoryData($proposal);
        $isPromotional = false;

        $kits = $proposal->is_manual
            ? json_decode($proposal->components, true)
            : getKitCodesFromProposal($proposal);

        $fields = [
            ['id' => 'croqui', 'name' => 'inspection[croqui]', 'label' => 'Croqui'],
            ['id' => 'roof', 'name' => 'inspection[roof][]', 'label' => 'Telhado'],
            ['id' => 'roof_structure', 'name' => 'inspection[roof_structure]', 'label' => 'Estrutura do Telhado'],
            ['id' => 'pattern', 'name' => 'inspection[pattern]', 'label' => 'Padrão (tampa FECHADA) '],
            ['id' => 'open_pattern', 'name' => 'inspection[open_pattern]', 'label' => 'Padrão (tampa ABERTA)'],
            ['id' => 'pattern_circuit_break', 'name' => 'inspection[circuit_breaker]', 'label' => 'Disjuntor do padrão'],
            ['id' => 'meter', 'name' => 'inspection[meter]', 'label' => 'Medidor'],
            ['id' => 'switchboard', 'name' => 'inspection[switchboard]', 'label' => 'Quadro de distrib.'],
            ['id' => 'inverter_local', 'name' => 'inspection[inverter_local]', 'label' => 'Local do inversor'],
            ['id' => 'post', 'name' => 'inspection[post]', 'label' => 'Poste'],
            ['id' => 'compass', 'name' => 'inspection[compass]', 'label' => '/Print Bússola'],
            ['id' => 'property_fax', 'name' => 'inspection[property_fax]', 'label' => 'Faxada do imóvel'],
        ];

        return view('proposals.show', compact(
                'proposal',
                'valueHistoryData',
                'kits',
                'isPromotional',
                'fields')
        );
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

    public function generatePdf(int $proposal_id, ?bool $isSample = false): Response
    {
        $proposal = Proposal::find($proposal_id);
        $pdfParams = $this->setPdfParams(proposal: $proposal);
        $city = $proposal->client->addresses->first()->city;
        $components = json_decode($proposal->components, true);

        $firstKit = $proposal->is_manual
            ? null
            : kitByUuid(getKitCodesFromProposal($proposal)[0]);

        $manualData = $proposal->is_manual
            ? json_decode($proposal->manual_data, true)
            : null;

        $inverterImage = $proposal->is_manual
            ? setInverterImage((int)$manualData['inverter_brand'])
            : setInverterImage($firstKit['technical_description']['inverter_brand']);

        $panelBrandImage = $proposal->is_manual
            ? setPanelBrandImage((int)$manualData['panel_brand'])
            : setPanelBrandImage($firstKit['technical_description']['panel_specs']['panel_brand']);

        $withoutSolar = calculateWithoutSolar(proposal: $proposal);
        $withSolar = floatToMoney(calculateWithSolar(proposal: $proposal));

        $incidence = $this->solarIncidenceService->getSolarIncidence(city: $city)->average;
        $payback = $this->paybackService->setPaybackData(proposal: $proposal);
        $generationData = $this->paybackService->setGenerationData(proposal: $proposal);

        $floatOverload = $proposal->is_manual
            ? (stringInverterPowerToFloat($manualData['inverter_power']) * 1.35) / ((int)$manualData['panel_power'] / 1000)
            : 0;

        $overload = $proposal->is_manual
            ? 'Até ' . floor($floatOverload) . ' módulos'
            : 'Até ' . $this->getKitOverload(codes: getKitCodesFromProposal($proposal)) . ' módulos';

        $invertersCount = $proposal->is_manual
            ? ($manualData['inverter_quantity'] ?? 1)
            : $this->setInvertersCount($components);

        $pdf = $isSample
            ? PDF::loadView('proposals.small_pdf', compact($pdfParams))
            : PDF::loadView('proposals.pdf', compact($pdfParams));

        return $pdf
            ->stream('#' . $proposal->id . ' ' . $proposal->client->name . '.pdf');
    }

    public function setFinalValue(Request $request): float
    {
        $data = $request->all();
        return $this->pricingService
            ->calculateFinalPrice($data);
    }

    public function setAverageProduction(Request $request): float
    {
        $data = $request->all();
        $city = Address::find((int)$data['addressId'])->city;

        $incidence = (float)str_replace(
            search: ',',
            replace: '.',
            subject: $this->solarIncidenceService->getSolarIncidence($city)->average
        );

        return ceil(
            ((float) $data['kwp']) / ((1 + (float)env('GENERATION_LOST')))
            * 30 * $incidence
        );
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
            'invertersCount',
            'overload'
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

    private function getKitOverload(array $codes): int
    {
        return array_map(function ($code) {
            $kit = kitByUuid($code);

            return floor(
                $kit['technical_description']['inverter_overload']
                / ($kit['technical_description']['panel_specs']['panel_power'] / 1000)
            );

        }, $codes)[0];
    }

}

