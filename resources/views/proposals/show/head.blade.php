<div class="columns mt-2 ml-1">
    <div class="column is-7">
        <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt="..">Proposta &nbsp;&nbsp;<div
                class="tag @if(!is_null($proposal->send_date)) is-success @else is-danger @endif">{{ !is_null($proposal->send_date) ? 'Formalizada' : 'Não formalizada' }}</div>
            @if($isPromotional)
                <span class="tag is-success">
                   $ Promocional $
                </span>
            @endif
        </h3>
    </div>
    <div id="action-buttons" class="column is-5 is-flex is-justify-content-space-around">
        <a target="_blank" href="{{ route('proposal.pdf', [$proposal->id]) }}" class="button is-primary">Proposta em
            PDF</a>
        <a target="_blank" href="{{ route('proposal.small-pdf', [$proposal->id, true]) }}"
           class="button is-success">Gerar Resumo</a>
        @if(is_null($proposal->send_date))
                <a href="{{ route('proposal.approve', [$proposal->id]) }}" class="button is-info">
                    Enviar para aprovação</a>
        @endif
    </div>

</div>
<div class="columns">
    <div class="title-bottom-line" style="margin-left: 50px"></div>
</div>
