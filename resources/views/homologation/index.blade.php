@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Homologações</h3>
            </div>

            <form action="{{ route('homologation.index') }}" method="get">
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
                    <th>Status</th>
                    <th>Prazo decorrido</th>
                    <th>Agente</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @forelse($homologations as $homologation)
                        <tr class="lh-40">
                            <th>{{$homologation->id}}</th>
                            <td>{{ $homologation->proposal->client->name }}</td>
                            <td><span style="font-size: 12pt" class="tag
                            @if($homologation->status->is_final) is-success
                            @elseif($homologation->status->name == 'Pendente')
                            @else is-info
                            @endif box w100">{{ $homologation->status->name }}</span></td>
                            <td><span style="font-size: 12pt" class="tag box w100 {{ deadLineColor($homologation->status, $homologation->created_at->diffInDays(now())) }}">
                                    @if($homologation->status->is_final) {{ 'FINALIZADO' }}
                                    @else {{ $homologation->created_at->format('d/m/Y') . ' - ' . $homologation->created_at->diffInDays(now()) . ' Dias' }}
                                    @endif
                                </span>
                            </td>
                            <td>{{ $homologation->proposal->agent->name }}</td>
                            <td>R$ {{ floatToMoney($homologation->proposal->valueHistory->final_price) }}</td>
                            <td>
                                <a class="button is-primary" href="{{ route('homologation.show', [$homologation->id]) }}">
                                    <ion-icon name="create-outline" class="table-icon"></ion-icon>
                                </a>
                                <a class="button is-danger" href="{{ route('homologation.inactive', [$homologation->id]) }}">
                                    <ion-icon name="trash-outline" class="table-icon"></ion-icon>
                                </a>
                            </td>
                        </tr>
                @empty
                    <tr>
                        <td>Não há Homologações disponíveis</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{ $homologations->appends(request()->all())->links() }}
        </div>
    </div>

@endsection
