@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Clientes</h3>
            </div>
            <div class="columns">
                <div class="title-bottom-line" style="margin-left: 50px"></div>
                <div class="column is-flex is-justify-content-end">
                    <a href="{{ route('client.create') }}" class="button is-info">
                        <ion-icon name="add-circle-outline"></ion-icon>
                        Novo cliente
                    </a>
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    <h4>Filtrar</h4>
                </div>
            </div>
            <form action="{{ route('client.index') }}" method="get">
                @csrf
                <div id="client-filters" class="columns is-flex is-flex-direction-row mt-4 mb-4">
                    <div class="column is-3">
                        <div class="field">
                            <label for="name-filter" class="label">Nome</label>
                            <div class="control">
                                <input name="name_filter" id="name-filter" class="input is-rounded" type="text"
                                       placeholder="Digite o nome">
                            </div>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label for="document-filter" class="label">Documento (CPF/CNPJ)</label>
                            <div class="control">
                                <input name="document_filter" id="document-filter" class="input is-rounded"
                                       type="text"
                                       placeholder="Digite o Documento">
                            </div>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label for="agent-filter" class="label">Agente</label>
                            <div class="select is-multiline is-rounded is-fullwidth">
                                <select id="agent-filter" name="agent_filter">
                                    <option></option>
                                    @foreach($agents as $agent)
                                        <option value="{{$agent->id}}">{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="column is-3 mt-1"><br>
                        <button type="submit" class="button is-warning">Filtrar&nbsp;&nbsp;<ion-icon
                                name="search-outline"></ion-icon>
                        </button>
                        <a href="{{ route('client.index') }}" class="button is-danger">Limpar</a>
                    </div>
                </div>
            </form>


            <table class="table is-hoverable is-fullwidth">
                <thead>
                <tr>
                    <th><abbr title="Position">ID</abbr></th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Documento</th>
                    <th>Cidade/Estado</th>
                    <th>Telefone</th>
                    <th>Agente</th>
                    <th>Ações</th>

                </tr>
                </thead>
                <tbody>
                @forelse($clients as $client)
                    @if(count($client->addresses) != 0)
                        <tr class="lh-40">
                            <th>{{$client->id}}</th>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->type == 'person' ? 'Pessoa física' : 'Pessoa jurídica' }}</td>
                            <td>{{$client->document}}</td>
                            <td>{{$client->addresses->first()->city->name_and_federal_unit}}</td>
                            <td>{{$client->phone_number}}</td>
                            <td>{{$client->agent->name}}</td>
                            <td>
                                <a class="button is-primary" href="{{ route('client.edit', [$client->id]) }}">
                                    <ion-icon name="create-outline" class="table-icon"></ion-icon>
                                </a>
                                <a class="button is-danger">
                                    <ion-icon name="trash-outline" class="table-icon"></ion-icon>
                                </a>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td>Não há clientes cadastrados</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            @if(isset($clients) && count($clients) > 0)
                {{ $clients->appends(request()->all())->links() }}
            @endif
        </div>
    </div>

@endsection
