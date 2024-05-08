<?php

namespace App\Http\Controllers;

use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Http\Requests\LeadRequest;
use App\Models\City;
use App\Models\Lead;
use App\Models\State;
use App\Models\User;
use App\Services\KitSpecService;
use App\Services\LeadService;
use App\Services\ProposalService;
use App\Services\ProposalValueHistoryService;
use App\Services\SolarIncidenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function __construct(private readonly LeadService $leadService)
    {}

    public function index(): View
    {
        $leads = Lead::query()->orderBy('id', 'desc')->paginate(10);
        $users = User::query()
            ->where('permission', 'admin')
            ->get();

        return view('leads.proposal.index', compact('leads', 'users'));
    }

    public function create(): View
    {
        $states = State::query()->select('id', 'name')->get();
        $tensions = TensionPattern::cases();
        $roofs = RoofStructure::setRoofsToScreen();

        return view('leads.proposal.form', compact('states', 'tensions', 'roofs'));
    }

    public function show(int $id): View
    {
        $lead = Lead::find($id);
        $incidence = (new SolarIncidenceService())->getSolarIncidence(City::find($lead->city_id));
        $average = (new ProposalService(
            new SolarIncidenceService(),
            new ProposalValueHistoryService())
        )->calculateEstimatedGeneration($lead->kit()['kwp'], $incidence)['average'];


        return view('leads.proposal.show', compact('lead', 'average'));
    }

    public function store(LeadRequest $request): RedirectResponse
    {
        $request->validated();
        $message = $this->leadService->store($request->all());
        session()->flash('message', $message);

        return redirect()->route('leads.index');
    }

    public function delete(int $id): RedirectResponse
    {
        (new LeadService())->delete($id);
        session()->flash('message', 'LEAD excluído com sucesso!');

        return redirect()->back();
    }

    public function incidenceFromCity(int $id): float
    {
        $city = City::find($id);
        $incidence = (new SolarIncidenceService())->getSolarIncidence($city);

        return (float) str_replace(',', '.', $incidence->average);
    }

    public function setAverageProductionByCity(Request $request): float
    {
        return (new KitSpecService())
            ->setAverageProduction($request->all());
    }

    public function updateLeadStatus(Request $request): RedirectResponse
    {
        $message = $this->leadService->updateStatus($request->only(['lead_id', 'status']));
        session()->flash('message', $message);

        return redirect()->route('leads.show', [$request->get('lead_id')]);
    }

    public function generatePdf(int $leadId): void
    {
        $lead = Lead::find($leadId);
        $this->leadService->generatePdf($lead);
    }
}
