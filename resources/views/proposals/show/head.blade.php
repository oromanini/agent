<div class="columns mt-2 ml-1">
    <div class="column is-9">
        <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt="..">Proposta</h3>
    </div>
    <div class="column is-1 is-flex is-justify-content-end">
        <a href="{{ route('proposal.pdf', [$proposal->id]) }}" class="button is-primary">Gerar PDF</a>
    </div>
    <div class="column is-2 is-flex is-justify-content-start">
        <a href="{{ route('proposal.approve', [$proposal->id]) }}" class="button is-info">Enviar para
            aprovação</a>
    </div>
</div>
<div class="columns">
    <div class="title-bottom-line" style="margin-left: 50px"></div>
</div>
