@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 mb-5 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt="..">
                    Atualização de custos
                </h3>
            </div>

            @if (session('success'))
                <div class="notification is-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table is-striped is-fullwidth">
                <thead>
                <tr>
                    <th>Classificação</th>
                    <th>Custos Atuais</th>
                    <th>Última Atualização</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($workCosts as $cost)
                    <tr>
                        <td>
                            <strong>{{ $cost->classification_name }}</strong>
                        </td>
                        <td>
                            <div style="font-family: monospace; font-size: 0.9em;">
                                @foreach ($cost->costs as $key => $value)
                                    <div>
                                        <span>{{ $key }}:</span>
                                        @if(is_array($value))
                                            <span>{{ json_encode($value) }}</span>
                                        @else
                                            <strong style="color: #3273dc;">{{ $value }}</strong>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            {{-- Alterado para exibir a data completa no formato solicitado --}}
                            <span class="tag is-light">
                                    {{ $cost->updated_at->format('d/m/Y H:i') }}
                                </span>
                        </td>
                        <td>
                            <a href="{{ route('work_costs.edit', $cost->id) }}" class="button is-info is-small">Editar</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
