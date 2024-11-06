<?php

namespace App\Http\Controllers;

use App\Enums\DistributorsEnum;
use App\Enums\InverterBrands;
use App\Enums\PanelBrands;
use App\Enums\PaymentTypeEnum;
use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Helpers\ImageHelper;
use App\Http\Requests\ProposalRequest;
use App\Models\City;
use App\Models\Client;
use App\Models\Kit;
use App\Models\Lead;
use App\Models\PromotionalKit;
use App\Models\Proposal;
use App\Models\User;
use App\Models\ValueHistoryInfo;
use App\Repositories\ProposalRepository;
use App\Services\KitSpecService;
use App\Services\LeadService;
use App\Services\PaybackService;
use App\Services\PricingService;
use App\Services\ProposalService;
use App\Services\ProposalValueHistoryService;
use App\Services\SolarIncidenceService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ProposalController extends Controller
{
    public function __construct(
        private readonly ProposalService             $proposalService,
        private readonly ProposalRepository          $proposalRepository,
        private readonly ProposalValueHistoryService $proposalValueHistoryService,
        private readonly PaybackService              $paybackService,
        private readonly PricingService              $pricingService,
        private readonly KitSpecService              $kitSpecService,
    )
    {}

    public function index(Request $request): View
    {
        $proposals = $this->proposalRepository->filter($request->all());
        $agents = User::all();

        return view('proposals.index', compact('proposals', 'agents'));
    }

    public function create(): View
    {
        $clients = $this->setClients();

        $tensions = TensionPattern::cases();
        $roofs = RoofStructure::setRoofsToScreen();
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
        $agents = User::query()->whereNull('deleted_at')->get();
        $tensions = TensionPattern::cases();
        $roofs = RoofStructure::setRoofsToScreen();
        $panels = PanelBrands::cases();
        $inverters = InverterBrands::cases();

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
        $valueHistoryInfo = (new ValueHistoryInfo($proposal))->pricingInfo();

        $kits = $proposal->components
            ? jsonToArray($proposal->components)
            : (new KitSpecService())->getKitFromProposal($proposal)->components;

        $kit = Kit::query()
            ->where('distributor_code', $proposal->kit_uuid)
            ->first();

        list($panelSpecs, $inverterSpecs) = $this->getComponentsSpec($proposal, $kit);

        $isPromotional = PromotionalKit::query()
            ->where('kwp', $kit->kwp ?? $proposal->kwp)
            ->where('panel_brand', $panelSpecs['brand'])
            ->where('panel_power', $panelSpecs['power'])
            ->where('inverter_brand', $inverterSpecs['brand'])
            ->exists();

        $fields = $this->setEditFields();

        return view('proposals.show',
            compact(
                'proposal',
                'valueHistoryData',
                'valueHistoryInfo',
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
            $message = $this->proposalService->store(data: $data, isManual: true);

        } catch (\Exception $e) {
            throw new \Exception($e);
        }

        session()->flash('message', $message);

        return redirect()->route('proposal.index');
    }

    public function generatePdf(int $proposalId, ?bool $isSample = false): Response
    {
        $proposal = Proposal::find($proposalId);
        $pdfParams = $this->setPdfParams($proposal);
        $city = $proposal->client->addresses->first()->city;
        $components = jsonToArray($proposal->components);
        $finalValue = $proposal->valueHistory->final_price;

        if (!$proposal->is_manual) {
            $kit = $this->kitSpecService->getKitFromProposal($proposal);
            $inverterBrand = jsonToArray($kit->inverter_specs)['brand'];
            $panelBrand = jsonToArray($kit->panel_specs)['logo'];
        }

        $manualData = $proposal->is_manual
            ? jsonToArray($proposal->manual_data)
            : null;
        $panelImage = $proposal->is_manual
            ? (new ImageHelper())->setImageByBrand(type: 'panel', brand: $manualData['panel_brand'])
            : (new ImageHelper())->setImageByBrand(type: 'panel', brand: jsonToArray($kit->panel_specs)['brand']);

        $inverterImage = $proposal->is_manual
        ? (new ImageHelper())->setImageByBrand(type: 'inverter', brand: $manualData['inverter_brand'])
        : (new ImageHelper())->setImageByBrand(type: 'inverter', brand: $inverterBrand)
        ;

        $incidence = (new SolarIncidenceService())->getSolarIncidence(city: $city)->average;
        $payback = $this->paybackService->setPaybackData(proposal: $proposal);
        $generationData = $this->paybackService->setGenerationData(proposal: $proposal);

        $overload = 'Até ' . $this->kitSpecService->getKitOverload($kit ?? null, $manualData) . ' módulos';

        $invertersCount = $proposal->is_manual
            ? ($manualData['inverter_quantity'] ?? 1)
            : $this->kitSpecService->setInvertersCount(is_array($components) ? $components : jsonToArray($components));

        $inverterModels = $this->kitSpecService->setInverterModels(is_array($components) ? $components : jsonToArray($components));

        $firstKit = $kit ?? null;

        $pdf = $isSample
            ? PDF::loadView('proposals.small_pdf', compact($pdfParams))
            : PDF::loadView('proposals.pdf', compact($pdfParams));

        return $pdf
            ->stream('#' . $proposal->id . ' ' . $proposal->client->name . '.pdf');
    }

    public function generateLeadPdf(int $id): Response
    {
        $lead = Lead::find($id);
        $city = City::find($lead->city_id);
        $kitData = $lead->kit();
        $components = $lead->components();

        $inverterSpecs = jsonToArray($kitData['inverter_specs']);
        $panelSpecs = jsonToArray($kitData['panel_specs']);
        $inverterImage = setInverterImage($inverterSpecs['brand']);
        $panelBrandImage = setPanelBrandImage($panelSpecs['brand']);
        $incidence = (new SolarIncidenceService())->getSolarIncidence(city: $city)->average;
        $panelQuantity = (new LeadService($this))->getLeadPanelQuantity($lead);

        $payback = $this->paybackService->setLeadPaybackData(lead: $lead);
        $overload =
            "Até " .
            ceil(($inverterSpecs['power'] * 1.5) / ($panelSpecs['power'] / 1000)) .
            " módulos";

        $pdfParams = [
            'lead',
            'city',
            'payback',
            'panelQuantity',
            'panelBrandImage',
            'inverterImage',
            'inverterSpecs',
            'panelSpecs',
            'overload'
        ];

        $pdf = PDF::loadView('leads.base', compact($pdfParams));
        return $pdf->stream("#L{$lead->id} {$lead->name}.pdf");
    }

    public function setFinalValue(Request $request): JsonResponse
    {
        $cost = $request->get('cost');
        $kwp = $request->get('kwp');
        $panelCount = $request->get('panelCount');
        $panelBrand = $request->get('panelBrand');
        $panelPower = $request->get('panelPower');
        $inverterBrand = $request->get('inverterBrand');
        $roofStructure = $request->get('roofStructure');

        $finalPriceAndDetail = $this->pricingService
            ->calculateFinalPrice(
                cost: $cost,
                kwp: $kwp,
                panelCount: $panelCount,
                panelBrand: $panelBrand,
                panelPower: $panelPower,
                inverterBrand: $inverterBrand,
                roofStructure: $roofStructure,
                finalValue: $cost * ProposalValueHistoryService::BASE_GROSS_PROFIT,
                paymentType: PaymentTypeEnum::FINANCING,
                isLead: $request->get('isLead') == true
            );

        return response()->json($finalPriceAndDetail);
    }

    public function setAverageProduction(Request $request): float
    {
        return $this->kitSpecService
            ->setAverageProduction($request->all());
    }

    public function setTensionByValue(Request $request): string
    {
        return response()->json(
            $this->kitSpecService->setTensionByValue($request->all())
        )->getContent();
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
            'panelImage',
            'incidence',
            'payback',
            'generationData',
            'firstKit',
            'invertersCount',
            'overload',
            'inverterModels',
            'finalValue'
        ];
    }

    private function setEditFields(): array
    {
        return [
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
    }

    private function setClients(): array|Collection
    {
        return Client::query()
            ->when(!auth()->user()->is_admin, function ($query) {
                return $query->where('agent_id', auth()->user()->id);
            })
            ->orderBy('id', 'desc')
            ->get();
    }

    private function setInverterImageByDistributor(string $inverterBrand, string $distributor)
    {
        if ($distributor === DistributorsEnum::EDELTEC->value) {
            return \App\Packages\EdeltecApiPackage\Enums\InverterImage::getByCase($inverterBrand);
        }
        if ($distributor === DistributorsEnum::ODEX->value) {
            return strtolower($inverterBrand) == 'saj'
                ? '/img/inverters/saj.png'
                : '/img/inverters/sajmicroinverter.png';
        }
    }

    public function getComponentsSpec(Proposal $proposal, ?Kit $kit = null): array
    {
        if ($proposal->is_manual) {
            $manualData = jsonToArray($proposal->manual_data);
            $panelSpecs = [
                "brand" => $manualData['panel_brand'],
                "model" => $manualData['panel_model'],
                "power" => $manualData['panel_power'],
                "warranty" => $manualData['panel_warranty'],
            ];
            $inverterSpecs = [
                "brand" => $manualData['inverter_brand'],
                "model" => $manualData['inverter_model'],
                "power" => $manualData['inverter_power'],
                "warranty" => $manualData['inverter_warranty'],
                "quantity" => $manualData['inverter_quantity']
            ];
        } else {
            $panelSpecs = jsonToArray($kit->panel_specs);
            $inverterSpecs = jsonToArray($kit->inverter_specs);
        }
        return array($panelSpecs, $inverterSpecs);
    }

}

