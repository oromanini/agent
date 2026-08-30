@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">

        <div class="a-page-head">
            <h1>Projetos para aprovação</h1>
        </div>

        <form action="{{ route('approval.index') }}" method="get">
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
                    <a href="{{ route('approval.index') }}" class="a-btn-ghost">Limpar</a>
                </div>
            </div>
        </form>

        @php $authUser = \Illuminate\Support\Facades\Auth::user(); @endphp

        <div class="a-table-wrap">
            <table class="table is-hoverable is-fullwidth">
                <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Vistoria</th>
                    <th>Financiamento</th>
                    <th>Contrato</th>
                    <th>Agente</th>
                    <th>Total</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($approvals as $approval)
                    @php
                        $hasFinancing = !is_null($approval->financing);
                        $hasInspection = !is_null($approval->inspection);
                        $financingHasOwner = $hasFinancing && !is_null($approval->financing->owner);
                        $inspectionHasOwner = $hasInspection && !is_null($approval->inspection->owner);
                        $enabledForShow = $authUser->permission == 'admin'
                            || ($inspectionHasOwner && $approval->inspection->owner = $authUser)
                            || ($financingHasOwner && $approval->financing->owner = $authUser);
                        $inspectionStatus = $approval->inspection ? $approval->inspection->status->name : 'Aguardando';
                        $financingStatus = $approval->financing ? $approval->financing->status->name : 'Aguardando';
                        $contractStatus = $approval->contract ? $approval->contract->status->name : 'Aguardando';
                    @endphp
                    @if($enabledForShow && !is_null($approval))
                        <tr>
                            <td>
                                <div class="a-name-cell">
                                    <x-avatar :name="$approval->client->name" />
                                    <span style="font-weight:600;">{{ $approval->client->name }}</span>
                                </div>
                            </td>
                            <td><span class="tag {{ isApproved($inspectionStatus) }}">{{ $inspectionStatus }}</span></td>
                            <td><span class="tag {{ isApproved($financingStatus) }}">{{ $financingStatus }}</span></td>
                            <td><span class="tag {{ isApproved($contractStatus) }}">{{ $contractStatus }}</span></td>
                            <td>{{ $approval->agent ? $approval->agent->name : 'excluído' }}</td>
                            <td style="font-weight:700;">R$ {{ floatToMoney($approval->valueHistory->final_price) }}</td>
                            <td>
                                <div class="a-row-actions">
                                    <a class="a-iconbtn" title="Abrir" href="{{ route('approval.show', [$approval->id]) . '#project' }}">
                                        <ion-icon name="create-outline"></ion-icon>
                                    </a>
                                    <a class="a-iconbtn" title="Excluir" href="{{ route('approval.inactive', [$approval->id]) }}">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7">Não há aprovações disponíveis</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $approvals->appends(request()->all())->links() }}</div>
    </div>
@endsection
