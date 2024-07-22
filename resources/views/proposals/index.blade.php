@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Propostas</h3>
            </div>
            <div class="columns">
                <div class="title-bottom-line" style="margin-left: 50px"></div>
                <div class="column is-flex is-justify-content-end">
                    <a href="{{ route('proposal.create') }}" class="button is-info">
                        <ion-icon name="add-circle-outline"></ion-icon>
                        Nova proposta
                    </a>
                </div>
            </div>
            <form action="{{ route('proposal.index') }}" method="get">
                @csrf
                <div id="proposal-filters" class="columns is-flex is-flex-direction-row mt-4 mb-4">
                    <div class="column is-2">
                        <div class="field">
                            <label for="name-filter" class="label">Nome</label>
                            <div class="control">
                                <input name="name_filter" id="name-filter" class="input is-rounded" type="text" placeholder="Digite o nome">
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="document-filter" class="label">Documento (CPF/CNPJ)</label>
                            <div class="control">
                                <input name="document_filter" id="document-filter" class="input is-rounded"
                                       type="text"
                                       placeholder="Digite o Documento">
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
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
                    <div class="column is-2">
                        <div class="field">
                            <label for="initial_date_filter" class="label">Data inicial</label>
                            <div class="control">
                                <input name="initial_date_filter" id="initial_date_filter" class="input is-rounded"
                                       type="date">
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="final_date_filter" class="label">Data final</label>
                            <div class="control">
                                <input name="final_date_filter" id="final_date_filter" class="input is-rounded"
                                       type="date">
                            </div>
                        </div>
                    </div>
                    <div class="column is-2 mt-1"><br>
                        <button type="submit" class="button is-warning">Filtrar&nbsp;&nbsp;<ion-icon
                                name="search-outline"></ion-icon>
                        </button>
                        <a href="{{ route('proposal.index') }}" class="button is-danger">Limpar</a>
                    </div>
                </div>
            </form>


            <table class="table is-hoverable is-fullwidth">
                <thead>
                <tr>
                    <th><abbr title="Position">ID</abbr></th>
                    <th>Cliente</th>
                    <th>Agente</th>
                    <th>Enviado?</th>
                    <th>Potência</th>
                    <th>Valor final(R$)</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @forelse($proposals as $proposal)
                    @if(!is_null($proposal->client))
                    <tr class="lh-40">
                        <th>{{$proposal->id}}</th>
                        <td>{{ $proposal->client->name }}</td>
                        <td>{{ $proposal->agent->name }}</td>
                        <td>
                            @if(!is_null($proposal->send_date))
                                <span class="tag is-success">Enviada</span>
                            @else
                                <span class="tag is-danger">
                                Não formalizada
                            </span>
                            @endif
                        </td>
                        <td>{{ $proposal->kwp }} kWp</td>
                        <td>R$ {{ floatToMoney($proposal->valueHistory->final_price) }}</td>
                        <td>
                            <a class="button is-primary" href="{{ route('proposal.edit', [$proposal->id]) }}" >
                                <ion-icon name="create-outline" class="table-icon"></ion-icon>
                            </a>
                            <a target="_blank" class="button is-info" href="{{ route('proposal.pdf', [$proposal->id]) }}" style="padding: 0px 6px 0px 15px">
                                <ion-icon name="document-outline"></ion-icon>
                            </a>
                            <a class="button is-danger" onclick="return confirm('Deseja realmente excluir a proposta?')" href="{{ route('proposal.delete', [$proposal->id]) }}">
                                <ion-icon name="trash-outline" class="table-icon"></ion-icon>
                            </a>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr>
                        <td>Não há propostas cadastradas</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{ $proposals->appends(request()->all())->links() }}
        </div>
    </div>
@endsection
