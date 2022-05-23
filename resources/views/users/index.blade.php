@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Usuários</h3>
            </div>
            <div class="columns">
                <div class="title-bottom-line" style="margin-left: 50px"></div>
                <div class="column is-flex is-justify-content-end">
                    <a href="{{ route('user.create') }}" class="button is-info">
                        <ion-icon name="add-circle-outline"></ion-icon>
                        Novo Usuário
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
                <div class="columns is-flex is-flex-direction-row mt-4 mb-4">
                    <div class="column is-3">
                        <div class="field">
                            <label for="name-filter" class="label">Nome</label>
                            <div class="control">
                                <input name="name_filter" id="name-filter" class="input is-rounded" type="text"
                                       placeholder="Digite o nome">
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="cnpj-filter" class="label">CNPJ</label>
                            <div class="control">
                                <input name="cnpj_filter" id="cnpj-filter" class="input is-rounded"
                                       type="text"
                                       placeholder="Digite o CNPJ">
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="phone-filter" class="label">Telefone</label>
                            <div class="control">
                                <input name="phone_number_filter" id="phone-filter" class="input is-rounded"
                                       type="text"
                                       placeholder="Digite o Telefone">
                            </div>
                        </div>
                    </div>

                    <div class="column is-3 mt-1"><br>
                        <button type="submit" class="button is-warning">Filtrar&nbsp;&nbsp;<ion-icon
                                name="search-outline"></ion-icon>
                        </button>
                        <a href="{{ route('user.index') }}" class="button is-danger">Limpar</a>
                    </div>
                </div>
            </form>


            <table class="table is-hoverable is-fullwidth">
                <thead>
                <tr>
                    <th><abbr title="Position">ID</abbr></th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Cidade/Estado</th>
                    <th>Ascendente</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @forelse($agents as $agent)
                    <tr class="lh-40">
                        <th>{{$agent->id}}</th>
                        <td>{{ $agent->name }}</td>
                        <td>{{ $agent->email }}</td>
                        <td>{{ $agent->phone_number}}</td>
                        <td>{{ getNameAndFederalUnit($agent->city)}}</td>
                        <td>{{ getAscendantName($agent->ascendant) }}</td>
                        <td>
                            <span class="tag @if($agent->trashed()) is-success @else is-danger @endif"> @if($agent->trashed()) Inativo @else Ativo @endif</span>
                        </td>
                        <td>
                            <a class="button is-primary" href="{{ route('user.edit', [$agent->id]) }}">
                                <ion-icon name="create-outline" class="table-icon"></ion-icon>
                            </a>
                            <a class="button is-danger" href="{{ route('user.inactive', [$agent->id]) }}">
                                <ion-icon name="trash-outline" class="table-icon"></ion-icon>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td>Não há clientes cadastrados</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
