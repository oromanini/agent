@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">

        <div class="a-page-head">
            <h1>Leads</h1>
            <a href="{{ route('leads.create') }}" class="a-btn-primary">
                <ion-icon name="add-outline"></ion-icon>
                Novo lead
            </a>
        </div>

        <form action="{{ route('leads.index') }}" method="get">
            @csrf
            <div id="client-filters" class="a-filterbar">
                <div class="field" style="flex:1; min-width:220px;">
                    <label for="name-filter" class="label">Nome</label>
                    <div class="control">
                        <input name="name_filter" id="name-filter" class="input" type="text"
                               value="{{ request('name_filter') }}" placeholder="Buscar por nome">
                    </div>
                </div>
                <div class="field" style="width:220px;">
                    <label for="user-filter" class="label">Responsável</label>
                    <div class="select is-fullwidth">
                        <select id="user-filter" name="user_filter">
                            <option value="">Todos</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(request('user_filter') == $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display:flex; gap:.5rem;">
                    <button type="submit" class="a-btn-primary"><ion-icon name="search-outline"></ion-icon> Filtrar</button>
                    <a href="{{ route('leads.index') }}" class="a-btn-ghost">Limpar</a>
                </div>
            </div>
        </form>

        <div class="a-table-wrap">
            <table class="table is-hoverable is-fullwidth">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Responsável</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($leads as $lead)
                    <tr>
                        <td>
                            <div class="a-name-cell">
                                <x-avatar :name="$lead->name" />
                                <span style="font-weight:600;">{{ $lead->name }}</span>
                            </div>
                        </td>
                        <td>{{ $lead->phone_number }}</td>
                        <td>{{ $lead->user->name }}</td>
                        <td>
                            <div class="a-row-actions">
                                <a class="a-iconbtn" title="Abrir" href="{{ route('leads.show', [$lead->id]) }}">
                                    <ion-icon name="create-outline"></ion-icon>
                                </a>
                                <a class="a-iconbtn" title="Excluir"
                                   onclick="return confirm('Deseja realmente excluir o lead?')"
                                   href="{{ route('leads.delete', [$lead->id]) }}">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Não há leads cadastrados</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($leads) && count($leads) > 0)
            <div class="mt-4">{{ $leads->appends(request()->all())->links() }}</div>
        @endif
    </div>
@endsection
