<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected $clientService;
    protected $clientRepository;

    public function __construct(ClientService $clientService, ClientRepository $clientRepository)
    {
        $this->clientService = $clientService;
        $this->clientRepository = $clientRepository;
    }

    public function index(Request $request)
    {
        $clients = $this->clientRepository->filter($request->all());
        $agents = User::all();

        return view('clients.index', compact('clients', 'agents'));
    }
}
