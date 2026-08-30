@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">

        <div class="a-page-head">
            <h1>Agentes</h1>
            <a href="{{ route('user.create') }}" class="a-btn-primary">
                <ion-icon name="add-outline"></ion-icon>
                Novo usuário
            </a>
        </div>

        <form action="{{ route('user.index') }}" method="get">
            @csrf
            <div class="a-filterbar">
                <div class="field" style="flex:1; min-width:220px;">
                    <label for="name-filter" class="label">Nome</label>
                    <div class="control">
                        <input name="name_filter" id="name-filter" class="input" type="text"
                               value="{{ request('name_filter') }}" placeholder="Buscar por nome">
                    </div>
                </div>
                <div class="field" style="width:200px;">
                    <label for="cnpj-filter" class="label">CNPJ</label>
                    <div class="control">
                        <input name="cnpj_filter" id="cnpj-filter" class="input" type="text"
                               value="{{ request('cnpj_filter') }}" placeholder="Digite o CNPJ">
                    </div>
                </div>
                <div class="field" style="width:200px;">
                    <label for="phone-filter" class="label">Telefone</label>
                    <div class="control">
                        <input name="phone_number_filter" id="phone-filter" class="input" type="text"
                               value="{{ request('phone_number_filter') }}" placeholder="Buscar por telefone">
                    </div>
                </div>
                <div style="display:flex; gap:.5rem;">
                    <button type="submit" class="a-btn-primary"><ion-icon name="search-outline"></ion-icon> Filtrar</button>
                    <a href="{{ route('user.index') }}" class="a-btn-ghost">Limpar</a>
                </div>
            </div>
        </form>

        <div class="a-table-wrap">
            <table class="table is-hoverable is-fullwidth">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Cidade/UF</th>
                    <th>Ascendente</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($agents as $agent)
                    <tr>
                        <td>
                            <div class="a-name-cell">
                                <x-avatar :name="$agent->name" />
                                <span style="font-weight:600;">{{ $agent->name }}</span>
                            </div>
                        </td>
                        <td>{{ $agent->email }}</td>
                        <td>{{ $agent->phone_number }}</td>
                        <td>{{ getNameAndFederalUnit($agent->city) }}</td>
                        <td>{{ getAscendantName($agent->ascendant) }}</td>
                        <td>
                            <span class="a-pill {{ $agent->trashed() ? 'a-pill--neutral' : 'a-pill--green' }}">
                                {{ $agent->trashed() ? 'Inativo' : 'Ativo' }}
                            </span>
                        </td>
                        <td>
                            <div class="a-row-actions">
                                <a class="a-iconbtn" title="Editar" href="{{ route('user.edit', [$agent->id]) }}">
                                    <ion-icon name="create-outline"></ion-icon>
                                </a>
                                <a class="a-iconbtn" title="Inativar" href="{{ route('user.inactive', [$agent->id]) }}">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Não há agentes cadastrados</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $agents->appends(request()->all())->links() }}</div>
    </div>
@endsection
