<div style="display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:20px;" class="proposal-metric-grid">
    <div class="a-kpi" style="padding:1rem 1.15rem;">
        <div class="a-kpi__meta" style="text-transform:uppercase;">Preço</div>
        <div class="a-kpi__value" style="font-size:1.15rem;">R$ {{ floatToMoney($proposal->valueHistory->final_price) }}</div>
    </div>
    <div class="a-kpi" style="padding:1rem 1.15rem;">
        <div class="a-kpi__meta" style="text-transform:uppercase;">Cartão · 12x</div>
        <div class="a-kpi__value" style="font-size:1.15rem;">R$ {{ floatToMoney($valueHistoryInfo->card['finalPriceWithFee'] / 12) }}</div>
    </div>
    <div class="a-kpi" style="padding:1rem 1.15rem;">
        <div class="a-kpi__meta" style="text-transform:uppercase;">Geração</div>
        <div class="a-kpi__value" style="font-size:1.15rem;">{{ ceil($proposal->estimated_generation) }} kWh/mês</div>
    </div>
    <div class="a-kpi" style="padding:1rem 1.15rem;">
        <div class="a-kpi__meta" style="text-transform:uppercase;">Consumo</div>
        <div class="a-kpi__value" style="font-size:1.15rem;">{{ ceil($proposal->average_consumption) }} kWh/mês</div>
    </div>
    <div class="a-kpi" style="padding:1rem 1.15rem;">
        <div class="a-kpi__meta" style="text-transform:uppercase;">Potência</div>
        <div class="a-kpi__value" style="font-size:1.15rem;">{{ $proposal->kwp }} kWp</div>
    </div>
</div>

<style>
    @media (max-width: 1100px) { .proposal-metric-grid { grid-template-columns: repeat(2, 1fr) !important; } }
</style>
