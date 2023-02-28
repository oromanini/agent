<?php

namespace App\Http\Controllers;

use App\Models\Homologation;
use App\Models\Installation;
use App\Models\User;
use App\Repositories\InstallationRepository;
use App\Services\InstallationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstallationController extends Controller
{
    public function __construct(
        private readonly InstallationRepository $installationRepository,
        private readonly InstallationService $installationService
    ) {
    }

    public function index(Request $request): View
    {
        $installations = $this->installationRepository->filter($request->all());
        $agents = User::all();

        return view('installation.index', compact('installations', 'agents'));
    }

    public function show(int $id): View
    {
        $installation = Installation::find($id);
        $proposal = $installation->proposal;
        $kits = ApprovalController::setKits($proposal);
        $deadline = $installation->created_at->diffInDays(now());
        $deadlineColor = deadLineColor(status: $installation->status, deadline: $deadline);
        $plusCosts = $this->installationRepository->getPlusCosts($installation);

        return view('installation.show', compact(
            'installation',
            'proposal',
            'kits',
            'deadline',
            'deadlineColor',
            'plusCosts'
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $homologation = Installation::find($id);
        $this->installationService->update(model: $homologation, request: $request);

        session()->flash('message', ['success', "Instalação atualizada!"]);

        return redirect()->back();
    }

    public function inactive(int $id): RedirectResponse
    {
        Installation::find($id)->delete();
        session()->flash('message', ['error', "Instalação deletada!"]);
        return redirect()->back();
    }
}
