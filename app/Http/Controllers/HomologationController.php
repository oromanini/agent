<?php

namespace App\Http\Controllers;

use App\Models\Homologation;
use App\Models\User;
use App\Repositories\HomologationRepository;
use App\Services\HomologationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomologationController extends Controller
{
    public function __construct(
        private readonly HomologationRepository $homologationRepository,
        private readonly HomologationService $homologationService
    ) {
    }

    public function index(Request $request): View
    {
        $homologations = $this->homologationRepository->filter($request->all());
        $agents = User::all();

        return view('homologation.index', compact('homologations', 'agents'));
    }

    public function show(int $id): View
    {
        $homologation = Homologation::find($id);
        $proposal = $homologation->proposal;
        $kits = ApprovalController::setKits($proposal);
        $deadline = $homologation->created_at->diffInDays(now());
        $deadlineColor = deadLineColor(status: $homologation->status, deadline: $deadline);

        return view('homologation.show', compact(
            'homologation',
            'proposal',
            'kits',
            'deadline',
            'deadlineColor'
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $homologation = Homologation::find($id);
        $this->homologationService->update(model: $homologation, request: $request);

        session()->flash('message', ['success', "Homologação atualizada!"]);

        return redirect()->back();
    }
}
