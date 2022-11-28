<div class="columns mt-2 ml-1">
    <div class="column is-7">
        <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt="..">Proposta &nbsp;&nbsp;<div
                class="tag @if(!is_null($proposal->send_date)) is-success @else is-danger @endif">{{ !is_null($proposal->send_date) ? 'Formalizada' : 'Não formalizada' }}</div>
        </h3>
    </div>
    <div id="generate-proposal" class="column is-1 is-flex is-justify-content-center">
        <a target="_blank" href="{{ route('proposal.pdf', [$proposal->id]) }}" class="button is-primary">Gerar PDF</a>
    </div>
    <div id="generate-small-proposal" class="column is-2 is-flex is-justify-content-center">
        <a target="_blank" href="{{ route('proposal.small-pdf', [$proposal->id, true]) }}" class="button is-success">Gerar Resumo</a>
    </div>
    @if(is_null($proposal->send_date))
        <div class="column is-2 is-flex">
            <a href="{{ route('proposal.approve', [$proposal->id]) }}" class="button is-info">
                Enviar para aprovação</a>
        </div>
    @endif

</div>
<div class="columns">
    <div class="title-bottom-line" style="margin-left: 50px"></div>
</div>
