<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\CrmAgentLead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CrmAgentController extends Controller
{
    private array $statusLabels = [
        'novo' => 'Novo',
        'em_atendimento' => 'Em atendimento',
        'aguardando_resposta' => 'Aguardando resposta',
        'confeccao_de_contrato' => 'Confecção de contrato',
        'contrato_assinado' => 'Contrato assinado',
        'desistencia' => 'Desistência',
    ];

    public function index(Request $request)
    {
        abort_unless(auth()->user()->isAdmin, 403);

        $query = CrmAgentLead::query()->with(['interactions.user', 'user']);

        $query->when($request->filled('name'), function ($builder) use ($request) {
            $builder->where('name', 'like', '%' . $request->get('name') . '%');
        });

        $query->when($request->filled('phone_number'), function ($builder) use ($request) {
            $builder->where('phone_number', 'like', '%' . $request->get('phone_number') . '%');
        });

        $query->when($request->filled('registered_from'), function ($builder) use ($request) {
            $builder->whereDate('created_at', '>=', $request->get('registered_from'));
        });

        $query->when($request->filled('registered_to'), function ($builder) use ($request) {
            $builder->whereDate('created_at', '<=', $request->get('registered_to'));
        });

        $leads = $query->orderByDesc('created_at')->get();

        return view('crm_agents.index', [
            'leads' => $leads,
            'statusLabels' => $this->statusLabels,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin, 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:30',
            'status' => 'nullable|in:' . implode(',', CrmAgentLead::STATUSES),
        ]);

        CrmAgentLead::create([
            ...$data,
            'status' => $data['status'] ?? 'novo',
            'created_by' => auth()->id(),
        ]);

        session()->flash('message', ['success', 'Candidato cadastrado no CRM.']);

        return redirect()->route('crm-agentes.index');
    }

    public function updateStatus(Request $request, CrmAgentLead $lead): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin, 403);

        $data = $request->validate([
            'status' => 'required|in:' . implode(',', CrmAgentLead::STATUSES),
        ]);

        $lead->update(['status' => $data['status']]);

        return redirect()->route('crm-agentes.index', $request->only([
            'name',
            'phone_number',
            'registered_from',
            'registered_to',
        ]));
    }

    public function addInteraction(Request $request, CrmAgentLead $lead): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin, 403);

        $data = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $lead->interactions()->create([
            'content' => $data['content'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('crm-agentes.index', $request->only([
            'name',
            'phone_number',
            'registered_from',
            'registered_to',
        ]));
    }

    public function registerAgent(Request $request, CrmAgentLead $lead): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin, 403);

        if ($lead->user_id) {
            session()->flash('message', ['success', 'Candidato já cadastrado no sistema.']);
            return redirect()->route('crm-agentes.index');
        }

        $password = $this->generateSecurePassword();
        $cityId = City::query()->value('id') ?? 1;

        $user = new User();
        $user->name = $lead->name;
        $user->email = $lead->email ?? ('agente' . $lead->id . '@alluz.local');
        $user->password = Hash::make($password);
        $user->phone_number = $lead->phone_number ?? '00000000000';
        $user->city = $cityId;
        $user->permission = 'agent';
        $user->save();

        $lead->update([
            'user_id' => $user->id,
            'generated_password' => $password,
        ]);

        $lead->interactions()->create([
            'content' => 'Candidato cadastrado no sistema como agente.',
            'user_id' => auth()->id(),
        ]);

        session()->flash('message', ['success', 'Agente cadastrado com sucesso.']);

        return redirect()->route('crm-agentes.index');
    }

    private function generateSecurePassword(): string
    {
        return Str::upper(Str::random(4)) . '!' . Str::random(8) . rand(10, 99);
    }
}
