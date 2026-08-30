@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">

        <div class="a-page-head">
            <h1>Homologações</h1>
        </div>

        <form action="{{ route('homologation.index') }}" method="get">
            @csrf
            <div id="client-filters" class="a-filterbar">
                <div class="field" style="flex:1; min-width:220px;">
                    <label for="name-filter" class="label">Nome</label>
                    <div class="control">
                        <input name="name_filter" id="name-filter" class="input" type="text"
                               value="{{ request('name_filter') }}" placeholder="Buscar por nome">
                    </div>
                </div>
                <div class="field" style="width:200px;">
                    <label for="document-filter" class="label">Documento (CPF/CNPJ)</label>
                    <div class="control">
                        <input name="document_filter" id="document-filter" class="input" type="text"
                               value="{{ request('document_filter') }}" placeholder="Documento">
                    </div>
                </div>
                <div class="field" style="width:220px;">
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
                <div style="display:flex; gap:.5rem;">
                    <button type="submit" class="a-btn-primary"><ion-icon name="search-outline"></ion-icon> Filtrar</button>
                    <a href="{{ route('homologation.index') }}" class="a-btn-ghost">Limpar</a>
                </div>
            </div>
        </form>

        <div class="a-table-wrap">
            <table class="table is-hoverable is-fullwidth">
                <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Status</th>
                    <th>Prazo decorrido</th>
                    <th>Agente</th>
                    <th>Total</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($homologations as $homologation)
                    <tr>
                        <td>
                            <div class="a-name-cell">
                                <x-avatar :name="$homologation->proposal->client->name" />
                                <span style="font-weight:600;">{{ $homologation->proposal->client->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="tag @if($homologation->status->is_final) is-success @elseif($homologation->status->name == 'Pendente') @else is-info @endif">{{ $homologation->status->name }}</span>
                        </td>
                        <td>
                            <span class="tag {{ deadLineColor($homologation->status, $homologation->created_at->diffInDays(now())) }}">
                                @if($homologation->status->is_final) FINALIZADO
                                @else {{ $homologation->created_at->format('d/m/Y') . ' · ' . $homologation->created_at->diffInDays(now()) . ' dias' }}
                                @endif
                            </span>
                        </td>
                        <td>{{ $homologation->proposal->agent->name }}</td>
                        <td style="font-weight:700;">R$ {{ floatToMoney($homologation->proposal->valueHistory->final_price) }}</td>
                        <td>
                            <div class="a-row-actions">
                                <a class="a-iconbtn" title="Abrir" href="{{ route('homologation.show', [$homologation->id]) }}">
                                    <ion-icon name="create-outline"></ion-icon>
                                </a>
                                <a class="a-iconbtn" title="Excluir" href="{{ route('homologation.inactive', [$homologation->id]) }}">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Não há homologações disponíveis</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $homologations->appends(request()->all())->links() }}</div>
    </div>
@endsection
