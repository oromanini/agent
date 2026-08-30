<div class="a-page-head" style="align-items:center;">
    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <h1>Proposta #{{ $proposal->id }}</h1>
        <span class="a-pill {{ !is_null($proposal->send_date) ? 'a-pill--amber' : 'a-pill--pink' }}">
            {{ !is_null($proposal->send_date) ? 'Formalizada' : 'Não formalizada' }}
        </span>
        @if($isPromotional)
            <span class="a-pill a-pill--green">$ Promocional $</span>
        @endif
    </div>
    <div id="action-buttons" style="display:flex; gap:8px; flex-wrap:wrap;">
        <a target="_blank" href="{{ route('proposal.small-pdf', [$proposal->id, true]) }}" class="a-btn-ghost">Gerar resumo</a>
        <a target="_blank" href="{{ route('proposal.pdf', [$proposal->id]) }}" class="a-btn-primary">Proposta em PDF</a>
        @if(is_null($proposal->send_date))
            <a href="{{ route('proposal.approve', [$proposal->id]) }}" class="a-btn-primary">Enviar para aprovação</a>
        @endif
    </div>
</div>
<p style="margin:-0.6rem 0 1.2rem; color:#8A8578; font-size:0.9rem;">
    {{ $proposal->client->name }} · {{ $proposal->client->document }}
</p>
