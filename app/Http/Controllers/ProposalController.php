<?php

namespace App\Http\Controllers;

use App\Enums\RoofStructure;
use App\Enums\TensionPattern;
use App\Models\Client;
use App\Models\State;
use App\Models\User;
use App\Repositories\ProposalRepository;
use App\Services\ProposalService;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    private $proposalService;
    private $proposalRepository;

    public function __construct(ProposalService $proposalService, ProposalRepository $proposalRepository)
    {
        $this->proposalService = $proposalService;
        $this->proposalRepository = $proposalRepository;
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
        $roofs = $this->setRoofs();

        return view('proposals.form', compact('clients', 'tensions', 'roofs'));
    }

    public function manual()
    {
        $clients = Client::all();
        $agents = User::all();
        $tensions = TensionPattern::asSelectArray();
        $roofs = $this->setRoofs();
        $panels = $this->setPanels();
        $inverters = $this->setInverters();

        return view('proposals.manual', compact($this->setManualParams()));
    }

    public function manualStore(Request $request)
    {

    }

    private function setRoofs(): array
    {
        return [
            [
                'id' => RoofStructure::Colonial,
                'image' => '/img/roofs/colonial.png',
                'description' => 'Colonial'
            ],
            [
                'id' => RoofStructure::Trapezoidal,
                'image' => '/img/roofs/trapezoidal.png',
                'description' => 'Trapezoidal'
            ],
            [
                'id' => RoofStructure::Laje,
                'image' => '/img/roofs/laje.png',
                'description' => 'Laje'
            ],
            [
                'id' => RoofStructure::ParafMadeira,
                'image' => '/img/roofs/paraf-madeira.png',
                'description' => 'Parafuso Madeira'
            ],
            [
                'id' => RoofStructure::ParafMetal,
                'image' => '/img/roofs/paraf-metal.png',
                'description' => 'Parafuso Metal'
            ],
            [
                'id' => RoofStructure::Solo,
                'image' => '/img/roofs/solo.png',
                'description' => 'Solo'
            ],
            [
                'id' => RoofStructure::Ondulada,
                'image' => '/img/roofs/ondulada.png',
                'description' => 'Ondulada'
            ],
        ];
    }

    private function setPanels(): array
    {
        return [
            1 => ['Jinko', 460],
            2 => ['Sunket', 550],
            3 => ['Trina', 400],
        ];
    }

    private function setInverters(): array
    {
        return [
            1 => 'Growatt',
            2 => 'Chint',
            3 => 'Microinversor Deye',
        ];
    }

    private function setManualParams()
    {
        return [
            'clients',
            'agents',
            'tensions',
            'roofs',
            'panels',
            'inverters'
        ];

    }
}
