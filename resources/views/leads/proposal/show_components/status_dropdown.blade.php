@php

    $statuses = [
        '#6e6e6e' => [
            'Tratativa inicial',
            '2ª tentativa de contato',
            '3ª tentativa de contato',
            'Aguardando simulação de financiamento',
            'Aguardando visita técnica',
            'Aguardando análise engenharia',
        ],
        '#FF4933' => [
            'Desistência',
            'Sem resposta',
            'Fechou com concorrente',
        ],
        '#AEDC6E' => [
            'Aguardando contrato',
            'Venda fechada',
        ],
    ];

    function getColorByStatus(array $statuses, string $status): string
    {
        foreach ($statuses as $key => $values) {
            if (in_array($status, $values)) {
                return $key;
            }
        }
        return '';
    }


@endphp

    <div class="column is-3">
    <div id="average-card" style="background-color: {{ getColorByStatus($statuses, $lead->status) }} !important;">
        <form method="post" action="{{ route('leads.status', [$lead->id]) }}">
            @method('PUT')
            @csrf
            <input type="hidden" name="lead_id" value="{{ $lead->id }}">
        <div class="card-content is-flex is-justify-content-center is-align-items-center is-flex-direction-column">

            <div class="field">
                <label style="color: #fff" for="status" class="label">Status</label>
                <div class="select is-multiline is-fullwidth">
                    <select id="status" name="status">
                        @foreach(array_merge(...array_values($statuses)) as $status)
                            <option {{ $lead->status === $status ? 'selected' : '' }}
                                    value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                @error('status')<span class="error-message">{{ $message }}</span>@enderror
            </div>
            <span id="sub-title"> </span>
        </div>
        <div class="card-footer is-flex is-justify-content-center is-align-items-center"
        style="background-color: #48c78e; padding-bottom: 3px;padding-top: 2px;">
            <span id="footer-title">
                <button id="save-status-button" type="submit">
                    <ion-icon name="save-outline"></ion-icon> &nbsp;
                </button>
            </span>
        </div>
        </form>
    </div>
</div>


<style>

    #save-status-button {
        background-color: transparent;
        border: none;
        width: 100%;
        height: 100%;
        color: #fff;
        font-size: 12pt;
        display: flex;
        align-items: center;
    }
    #save-status-button:hover {
        cursor: pointer;
    }

    #save-status-button::after {
        content: 'alterar status';
        transition: ease-in-out 0.3s;
    }

    #save-status-button:hover::after {
        content: 'clique para alterar';
    }

</style>
