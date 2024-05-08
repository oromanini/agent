@php
    function sendInfo(\App\Models\Lead $lead): array
    {
        if ($lead->send_date !== null) {
            return ['color' => 'is-success', 'message' => 'enviada'];
        }
        return ['color' => 'is-warning', 'message' => 'pendente'];
    }
@endphp

<div class="columns mt-2 ml-1">
    <div class="column is-6">
        <h3 class="title">
            <img src="/img/logo/alluz-icon.png" width="30" alt="..">
            {{ $lead->name }}
            <div class="tag {{ sendInfo($lead)['color'] }}">
                {{sendInfo($lead)['message']}}
            </div>
        </h3>
    </div>
    <div class="column is-6 is-flex is-justify-content-end">
        <a class="button is-info" target="_blank" href="{{route('proposal.leadpdf', [$lead->id])}}">Gerar proposta</a>&nbsp;
        <a class="button is-success" href="">solicitar vistoria</a>
    </div>

</div>
