@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Novo LEAD</h3>
            </div>
            <div class="columns">
                <div class="title-bottom-line" style="margin-left: 50px"></div>
                <div class="column is-flex is-justify-content-end">
                    <a href="{{ route('leads.create') }}" class="button is-info">
                        <ion-icon name="add-circle-outline"></ion-icon>
                        Novo LEAD
                    </a>
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    <h4>Filtrar</h4>
                </div>
            </div>
            <form action="{{ route('leads.index') }}" method="get">
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
                            <label for="agent-filter" class="label">Responsável</label>
                            <div class="select is-multiline is-rounded is-fullwidth">
                                <select id="user-filter" name="user_filter">
                                    <option></option>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="column is-3 mt-1"><br>
                        <button type="submit" class="button is-warning">Filtrar&nbsp;&nbsp;<ion-icon
                                name="search-outline"></ion-icon>
                        </button>
                        <a href="{{ route('leads.index') }}" class="button is-danger">Limpar</a>
                    </div>
                </div>
            </form>

            <table class="table is-hoverable is-fullwidth">
                <thead>
                <tr>
                    <th><abbr title="Position">ID</abbr></th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Responsável</th>
                    <th>Ações</th>

                </tr>
                </thead>
                <tbody>
                @forelse($leads as $lead)
                    <tr class="lh-40">
                        <th>{{ $lead->id }}</th>
                        <td>{{ $lead->name }}</td>
                        <td>{{ $lead->phone_number }}</td>
                        <td>{{ $lead->user->name }}</td>
                        <td>
                            <a class="button is-primary" href="{{ route('leads.show', [$lead->id]) }}">
                                <ion-icon name="create-outline" class="table-icon"></ion-icon>
                            </a>
                            <a class="button is-danger" onclick="return confirm('deseja realmente excluir o LEAD?')"
                               href="{{ route('leads.delete', [$lead->id]) }}">
                                <ion-icon name="trash-outline" class="table-icon"></ion-icon>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td>Não há LEADS cadastrados</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            @if(isset($leads) && count($leads) > 0)
                {{ $leads->appends(request()->all())->links() }}
            @endif
        </div>
    </div>
@endsection
