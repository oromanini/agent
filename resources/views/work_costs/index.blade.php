@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">

        <div class="a-page-head">
            <h1>Atualizar custos</h1>
        </div>

        @if (session('success'))
            <div class="notification is-success">{{ session('success') }}</div>
        @endif

        <div class="a-table-wrap">
            <table class="table is-fullwidth">
                <thead>
                <tr>
                    <th>Classificação</th>
                    <th>Custos atuais</th>
                    <th>Última atualização</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($workCosts as $cost)
                    <tr>
                        <td style="font-weight:700;">{{ $cost->classification_name }}</td>
                        <td>
                            <div style="font-size: 0.82rem;">
                                @foreach ($cost->costs as $key => $value)
                                    <div>
                                        <span>{{ $key }}:</span>
                                        @if(is_array($value))
                                            <span>{{ json_encode($value) }}</span>
                                        @else
                                            <strong style="color: #B9740A;">
                                                {{ $key === 'profit' ? number_format($value * 100, 1, ',', '') . '%' : $value }}
                                            </strong>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td><span class="a-pill a-pill--neutral">{{ $cost->updated_at->format('d/m/Y H:i') }}</span></td>
                        <td>
                            <a href="{{ route('work_costs.edit', $cost->id) }}" class="a-btn-ghost" style="padding:.4rem .9rem;">Editar</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
