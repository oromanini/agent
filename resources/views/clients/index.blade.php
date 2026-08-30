@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">

        <div class="a-page-head">
            <h1>Clientes</h1>
            <a href="{{ route('client.create') }}" class="a-btn-primary">
                <ion-icon name="add-outline"></ion-icon>
                Novo cliente
            </a>
        </div>

        <form action="{{ route('client.index') }}" method="get">
            @csrf
            <div id="client-filters" class="a-filterbar">
                <div class="field" style="flex:1; min-width:220px;">
                    <label for="name-filter" class="label">Nome</label>
                    <div class="control">
                        <input name="name_filter" id="name-filter" class="input" type="text"
                               value="{{ request('name_filter') }}" placeholder="Buscar por nome">
                    </div>
                </div>
                <div class="field" style="flex:1; min-width:200px;">
                    <label for="document-filter" class="label">Documento (CPF/CNPJ)</label>
                    <div class="control">
                        <input name="document_filter" id="document-filter" class="input" type="text"
                               value="{{ request('document_filter') }}" placeholder="Digite o documento">
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
                <div class="client-filter-actions" style="display:flex; gap:.5rem;">
                    <button type="submit" class="a-btn-primary client-filter-button">
                        <ion-icon name="search-outline"></ion-icon> Filtrar
                    </button>
                    <a href="{{ route('client.index') }}" class="a-btn-ghost client-filter-button">Limpar</a>
                </div>
            </div>
        </form>

        <div class="a-table-wrap">
            <table class="table is-hoverable is-fullwidth">
                <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Documento</th>
                    <th>Cidade/UF</th>
                    <th>Telefone</th>
                    <th>Agente</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($clients as $client)
                    @if(count($client->addresses) != 0)
                        <tr>
                            <td>
                                <div class="a-name-cell">
                                    <x-avatar :name="$client->name" />
                                    <span style="font-weight:600;">{{ $client->name }}</span>
                                </div>
                            </td>
                            <td>{{ $client->type == 'person' ? 'Pessoa física' : 'Pessoa jurídica' }}</td>
                            <td>{{ $client->document }}</td>
                            <td>{{ $client->addresses->first()->city->name_and_federal_unit }}</td>
                            <td>{{ $client->phone_number }}</td>
                            <td>{{ $client->agent ? $client->agent->name : 'Agente inativo' }}</td>
                            <td>
                                <div class="a-row-actions">
                                    <a class="a-iconbtn" title="Editar" href="{{ route('client.edit', [$client->id]) }}">
                                        <ion-icon name="create-outline"></ion-icon>
                                    </a>
                                    <a class="a-iconbtn" title="Excluir"
                                       onclick="return confirm('Deseja realmente excluir o cliente?')"
                                       href="{{ route('client.delete', [$client->id]) }}">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7">Não há clientes cadastrados</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($clients) && count($clients) > 0)
            <div class="mt-4">{{ $clients->appends(request()->all())->links() }}</div>
        @endif
    </div>
@endsection
