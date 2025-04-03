<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentRequest;
use App\Models\State;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $agents = $this->userRepository->filter($request->all());

        return view('users.index', compact('agents'));
    }

    public function create()
    {
        $states = State::all();
        $agents = User::all();

        return view('users.form', compact('states', 'agents'));
    }

    public function edit($id)
    {
        $states = State::all();
        $agent = User::withTrashed()->find($id);
        $agents = User::all();

        return view('users.form', compact('states', 'agent', 'agents'));
    }

    /**
     * @throws Throwable
     */
    public function store(AgentRequest $request): RedirectResponse
    {
        $request->validated();
        $data = $request->all();

        DB::transaction(function () use ($data) {
            $user = $this->fillUser(null, $data);
            $user->save();

            if (isset($data['contract'])) {
                $user->contract = $data['contract']->store('public/contracts/' . 'agent_' . $user->id);
                $user->update();
            }
        });

        session()->flash('message', ['success', 'Agente cadastrado com sucesso!']);

        return redirect()->route('user.index');
    }

    public function update($id, AgentRequest $request): RedirectResponse
    {
        $data = $request->all();
        $user = User::withTrashed()->find($id);

        DB::transaction(function () use ($data, $user) {
            $user = $this->fillUser($user, $data);
            $user->update();

            if (isset($data['contract'])) {
                $user->contract = $data['contract']->store('public/contracts/' . 'agent_' . $user->id);
                $user->update();
            }
        });

        (isset($data['active']) && $data['active'] == 'on' && $user->trashed())
        && $user->restore();

        (!isset($data['active']) && !$user->trashed())
        && $user->delete();

        session()->flash('message', ['success', 'Agente atualizado com sucesso!']);

        return redirect()->route('user.index');
    }

    public function inactive($id): RedirectResponse
    {
        DB::transaction(function () use ($id) {
            User::find($id)->delete();
        });

        session()->flash('message', ['success', 'Agente deletado com sucesso!']);

        return redirect()->route('user.index');
    }

    private function fillUser($user, array $data): User
    {
        $user = !is_null($user) ? $user : new User();

        $ascendant = $data['ascendant'] ? (int)$data['ascendant'] : 0;

        $user->name = $data['name'] ?? $user->name;
        $user->email = $data['email'] ?? $user->email;
        $user->password = $data['password'] ? Hash::make($data['password']) : $user->password;
        $user->phone_number = $data['phone_number'] ?? $user->phone_number;
        $user->city = $data['city'] ?? $user->city;
        $user->ascendant = $data['ascendant'] ?? $user->ascendant;
        $user->cpf = $data['cpf'] ?? $user->cpf;
        $user->cnpj = $data['cnpj'] ?? $user->cnpj;

        return $user;
    }
}
