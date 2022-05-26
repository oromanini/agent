<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Address;
use App\Models\Client;
use App\Models\State;
use App\Models\User;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use App\Services\SolarIncidenceService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected $clientService;
    protected $clientRepository;
    protected $solarIncidenceService;

    public function __construct(ClientService $clientService, ClientRepository $clientRepository, SolarIncidenceService $incidenceService)
    {
        $this->clientService = $clientService;
        $this->clientRepository = $clientRepository;
        $this->solarIncidenceService = $incidenceService;
    }

    public function index(Request $request)
    {
        $clients = $this->clientRepository->filter($request->all());
        $agents = User::all();

        return view('clients.index', compact('clients', 'agents'));
    }

    public function create()
    {
        $states = State::all();

        return view('clients.form', compact('states'));
    }

    /**
     * @throws Exception
     */
    public function store(ClientRequest $request): RedirectResponse
    {
        $request->validated();

        $message = $this->clientService->store($request->all());

        session()->flash('message', $message);

        return redirect()->route('client.index');
    }

    public function edit($id)
    {
        $client = Client::find($id);
        $states = State::all();
        $address = $client->addresses->first();
        $consumerUnit = $address->consumerUnit;

        return view('clients.form', compact($this->setEditParams()));
    }

    public function addressesFromClientId($id)
    {
        return Client::find($id)->addresses;
    }

    public function ucsFromClientId($id)
    {
        return Client::find($id)->consumerUnit;
    }

    public function incidenceFromAddress($id)
    {
        $city = Address::find($id)->city;
        $average =  str_replace(',', '.', $this->solarIncidenceService->getSolarIncidence($city)->average);

        return (float)$average;
    }

    private function setEditParams(): array
    {
        return [
            'client',
            'states',
            'address',
            'consumerUnit'
        ];
    }
}

