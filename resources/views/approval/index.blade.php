@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Projetos para aprovação</h3>
            </div>

            <form action="{{ route('approval.index') }}" method="get">
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
                        <a href="{{ route('approval.index') }}" class="button is-danger">Limpar</a>
                    </div>
                </div>
            </form>


            <table class="table is-hoverable is-fullwidth">
                <thead>
                <tr>
                    <th><abbr title="Position">ID</abbr></th>
                    <th>Cliente</th>
                    <th>Vistoria</th>
                    <th>Financiamento</th>
                    <th>Contrato</th>
                    <th>Agente</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @forelse(auth()->user()->id == 20 ? $approvals->where('created_at', '<=', '2022-10-01') : $approvals as $approval)
                    @if($approval->client)
                    <tr class="lh-40">
                        <th>{{$approval->id}}</th>
                        <td>{{ $approval->client->name }}</td>
                        @php
                        $inspectionStatus = $approval->inspection ? $approval->inspection->status->name : 'Aguardando';
                        $financingStatus = $approval->financing ? $approval->financing->status->name : 'Aguardando';
                        $contractStatus = $approval->contract ? $approval->contract->status->name : 'Aguardando';
                        @endphp
                        <td><span class="tag box w100 {{ isApproved($inspectionStatus) }}">{{ $inspectionStatus }}</span></td>
                        <td><span class="tag box w100 {{ isApproved($financingStatus) }}">{{ $financingStatus }}</span></td>
                        <td><span class="tag  box w100 {{ isApproved($contractStatus) }}">{{ $contractStatus }}</span></td>
                        <td>{{ $approval->agent->name }}</td>
                        <td>R$ {{ floatToMoney($approval->valueHistory->final_price) }}</td>
                        <td>
                            <a class="button is-primary" href="{{ route('approval.show', [$approval->id]) }}">
                                <ion-icon name="create-outline" class="table-icon"></ion-icon>
                            </a>
                            <a class="button is-danger" href="{{ route('approval.inactive', [$approval->id]) }}">
                                <ion-icon name="trash-outline" class="table-icon"></ion-icon>
                            </a>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr>
                        <td>Não há aprovações disponíveis</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{ $approvals->appends(request()->all())->links() }}
        </div>
    </div>

@endsection
