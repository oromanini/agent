@if(auth()->user()->is_admin)
<div class="columns box discount">
    <div class="column">
        <label for="">Custo Kit</label>
        <p class="proposalData">{{ $valueHistoryData['cost'] }}</p>
    </div>
    <div class="column">
        <label for="">Custo Serviços</label>
        <p class="proposalData">R$ {{ floatToMoney($valueHistoryData['totalCost']['services_cost'])  }}</p>
    </div>
    <div class="column">
        <label for="">Lucro bruto</label>
        <p class="proposalData">{{ round($valueHistoryData['gross_profit']*100, 2) }}%</p>
    </div>
    <div class="column">
        <label for="">Lucro Líquido (R$)</label>
        <p class="proposalData">{{ round($valueHistoryData['totalCost']['net_profit_percent'] * 100, 2) }}%</p>
    </div>
    <div class="column">
        <label for="">Lucro Líquido (R$)</label>
        <p class="proposalData">{{ floatToMoney($valueHistoryData['totalCost']['net_profit_value']) }}</p>
    </div>
</div>
@endif

<script>

</script>
