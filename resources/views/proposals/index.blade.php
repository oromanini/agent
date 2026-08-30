@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">

        <div class="a-page-head">
            <h1>Minhas propostas</h1>
            <a href="{{ route('proposal.create') }}" class="a-btn-primary">
                <ion-icon name="add-outline"></ion-icon>
                Nova proposta
            </a>
        </div>

        <form action="{{ route('proposal.index') }}" method="get">
            @csrf
            <div id="proposal-filters" class="a-filterbar">
                <div class="field" style="flex:1; min-width:200px;">
                    <label for="name-filter" class="label">Nome</label>
                    <div class="control">
                        <input name="name_filter" id="name-filter" class="input" type="text"
                               value="{{ request('name_filter') }}" placeholder="Buscar por nome">
                    </div>
                </div>
                <div class="field" style="width:180px;">
                    <label for="document-filter" class="label">Documento (CPF/CNPJ)</label>
                    <div class="control">
                        <input name="document_filter" id="document-filter" class="input" type="text"
                               value="{{ request('document_filter') }}" placeholder="Documento">
                    </div>
                </div>
                <div class="field" style="width:200px;">
                    <label for="agent-filter" class="label">Agente</label>
                    <div class="select is-fullwidth">
                        <select id="agent-filter" name="agent_filter">
                            <option value="">Todos os agentes</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" @selected(request('agent_filter') == $agent->id)>{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="field" style="width:160px;">
                    <label for="initial_date_filter" class="label">Data inicial</label>
                    <div class="control">
                        <input name="initial_date_filter" id="initial_date_filter" class="input" type="date"
                               value="{{ request('initial_date_filter') }}">
                    </div>
                </div>
                <div class="field" style="width:160px;">
                    <label for="final_date_filter" class="label">Data final</label>
                    <div class="control">
                        <input name="final_date_filter" id="final_date_filter" class="input" type="date"
                               value="{{ request('final_date_filter') }}">
                    </div>
                </div>
                <div style="display:flex; gap:.5rem;">
                    <button type="submit" class="a-btn-primary">
                        <ion-icon name="search-outline"></ion-icon> Filtrar
                    </button>
                    <a href="{{ route('proposal.index') }}" class="a-btn-ghost">Limpar</a>
                </div>
            </div>
        </form>

        <div class="a-table-wrap">
            <table class="table is-hoverable is-fullwidth">
                <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Agente</th>
                    <th>Status</th>
                    <th>Potência</th>
                    <th>Valor final</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($proposals as $proposal)
                    @if(!is_null($proposal->client))
                        @php $manual = json_decode($proposal->manual_data, true); @endphp
                        <tr>
                            <td>
                                <div class="a-name-cell">
                                    <x-avatar :name="$proposal->client->name" />
                                    <span style="font-weight:600;">{{ $proposal->client->name }}</span>
                                </div>
                            </td>
                            <td>{{ $proposal->agent ? $proposal->agent->name : 'Inativo' }}</td>
                            <td>
                                @if(!is_null($proposal->send_date))
                                    <span class="a-pill a-pill--amber">Enviada</span>
                                @else
                                    <span class="a-pill a-pill--neutral">Não formalizada</span>
                                @endif
                                @if(isset($manual['is_edited']) && $manual['is_edited'] == true)
                                    <span class="a-pill a-pill--blue" title="Editado por {{ $manual['updated_by'] ?? '—' }}">E</span>
                                @elseif($proposal->is_manual == true)
                                    <span class="a-pill a-pill--amber" title="Feito por {{ $manual['created_by'] ?? 'Sistema' }}">M</span>
                                @endif
                            </td>
                            <td>{{ $proposal->kwp }} kWp</td>
                            <td style="font-weight:700;">R$ {{ floatToMoney($proposal->valueHistory->final_price) }}</td>
                            <td>
                                <div class="a-row-actions">
                                    <a class="a-iconbtn" title="Ver" href="{{ route('proposal.edit', [$proposal->id]) }}">
                                        <ion-icon name="eye-outline"></ion-icon>
                                    </a>
                                    @if(auth()->user()->isAdmin)
                                        <a class="a-iconbtn" title="Editar" href="{{ route('proposal.editExistentProposal', [$proposal->id]) }}">
                                            <ion-icon name="create-outline"></ion-icon>
                                        </a>
                                    @endif
                                    <a class="a-iconbtn" title="PDF" target="_blank" href="{{ route('proposal.pdf', [$proposal->id]) }}">
                                        <ion-icon name="document-outline"></ion-icon>
                                    </a>
                                    <a class="a-iconbtn" title="Excluir"
                                       onclick="return confirm('Deseja realmente excluir a proposta?')"
                                       href="{{ route('proposal.delete', [$proposal->id]) }}">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6">Não há propostas cadastradas</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $proposals->appends(request()->all())->links() }}</div>
    </div>
@endsection
