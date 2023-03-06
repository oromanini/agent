<?php

namespace App\Http\Controllers;

use App\Enums\MonitoringAppsEnum;
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
        $appList = MonitoringAppsEnum::cases();
        $costSums = $this->installationService->setCostSums($installation);

        return view('installation.show', compact(
            'installation',
            'proposal',
            'kits',
            'deadline',
            'deadlineColor',
            'plusCosts',
            'appList',
            'costSums'
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $installation = Installation::find($id);
        $this->installationService->update(model: $installation, request: $request);

        session()->flash('message', ['success', "Instalação atualizada!"]);

        return redirect(route('installation.show', [$installation->id]) . '#installation');
    }

    public function addPlusCosts(Request $request, int $id): RedirectResponse
    {
        $installation = Installation::find($id);
        $this->installationService->addPlusCost(installation: $installation, data: $request->all());

        session()->flash('message', ['success', "Novo custo adicionado!"]);
        return redirect(route('installation.show', [$installation->id]) . '#costs');
    }

    public function updatePictures(Request $request, int $id): RedirectResponse
    {
        $installation = Installation::find($id);
        $this->installationService->updatePictures(installation: $installation, request: $request);

        session()->flash('message', ['success', "Fotos atualizadas!"]);
        return redirect(route('installation.show', [$installation->id]) . '#images');
    }

    public function inactive(int $id): RedirectResponse
    {
        Installation::find($id)->delete();
        session()->flash('message', ['error', "Instalação deletada!"]);
        return redirect()->back();
    }
}
