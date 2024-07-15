<table class="table">
    <tr>
        <th>Custo Kit</th>
        <th>Custo total À/V</th>
        <th>Custo total FINANC.</th>
        <th>Custo total CARTÃO</th>
    </tr>
    <tr>
        <td>R$ {{ floatToMoney($valueHistoryInfo->kitCost) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->cash['totalCost']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->financing['totalCost']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->card['totalCost']) }}</td>
    </tr>
</table>

<table class="table">
    <tr>
        <th>Instalação</th>
        <th>Homologação</th>
        <th>Acompanhamento da obra</th>
        <th>Antecipação</th>
    </tr>
    <tr>
        <td>R$ {{ floatToMoney($valueHistoryInfo->card['installation']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->card['homologation']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->card['workMonitoring']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->card['cardFee']) }}</td>
    </tr>
</table>

<table class="table">
    <tr>
        <th>C.A (À/V)</th>
        <th>C.A (Financ.)</th>
        <th>C.A (Cartão)</th>
    </tr>
    <tr>
        <td>R$ {{ floatToMoney($valueHistoryInfo->cash['directCurrent']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->financing['directCurrent']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->card['directCurrent']) }}</td>
    </tr>
</table>

<table class="table">
    <tr>
        <th>Imposto (À/V)</th>
        <th>Imposto (Financ)</th>
        <th>Imposto (Cartão)</th>
    </tr>
    <tr>
        <td>R$ {{ floatToMoney($valueHistoryInfo->cash['tax']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->financing['tax']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->card['tax']) }}</td>
    </tr>
</table>

<table class="table">
    <tr>
        <th>Margem Seg. (À/V)</th>
        <th>Margem Seg. (Financ.)</th>
        <th>Margem Seg. (Cartão)</th>
    </tr>
    <tr>
        <td>R$ {{ floatToMoney($valueHistoryInfo->cash['safetyMargin']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->financing['safetyMargin']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->card['safetyMargin']) }}</td>
    </tr>
</table>

<table class="table">
    <tr>
        <th>ROY (À/V)</th>
        <th>ROY (Financ.)</th>
        <th>ROY (Cartão)</th>
    </tr>
    <tr>
        <td>R$ {{ floatToMoney($valueHistoryInfo->cash['royalties']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->financing['royalties']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->card['royalties']) }}</td>
    </tr>
</table>

<table class="table">
    <tr>
        <th>Comissão comercial (À/V)</th>
        <th>Comissão comercial (Financ.)</th>
        <th>Comissão FINANCIAMENTO (Financ)</th>
        <th>Comissão comercial (Cartão)</th>
    </tr>
    <tr>
        <td>R$ {{ floatToMoney($valueHistoryInfo->cash['commercialInternalCommission']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->financing['commercialInternalCommission']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->card['commercialInternalCommission']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->financing['financialInternalCommission']) }}</td>
    </tr>
</table>

<table class="table">
    <tr>
        <th>Comissão Consultor (À/V)</th>
        <th>Comissão Consultor (Financ.)</th>
        <th>Comissão Consultor (Cartão)</th>
    </tr>
    <tr>
        <td>R$ {{ floatToMoney($valueHistoryInfo->cash['externalCommission']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->financing['externalCommission']) }}</td>
        <td>R$ {{ floatToMoney($valueHistoryInfo->card['externalCommission']) }}</td>
    </tr>
</table>

@php
    $liquidFinancingProfit = $valueHistoryInfo->financing['finalPrice'] - $valueHistoryInfo->financing['totalCost'];
    $liquidCardProfit = $valueHistoryInfo->card['finalPrice'] - $valueHistoryInfo->financing['totalCost'];
    $percentFinancingProfit = $liquidFinancingProfit / $valueHistoryInfo->financing['finalPrice'];
    $percentCardProfit = $liquidCardProfit / $valueHistoryInfo->financing['finalPrice'];
@endphp


<table class="table">
    <tr>
        <th>Lucro R$ (À/V)</th>
        <th>Lucro R$ (Cartão)</th>
        <th>Lucro % (À/V)</th>
        <th>Lucro % (Cartão)</th>
    </tr>
    <tr>
        <td>R$ {{ floatToMoney($liquidFinancingProfit) }} </td>
        <td>R$ {{ floatToMoney($liquidCardProfit) }} </td>
        <td> {{ round($percentFinancingProfit * 100, 2)}} %</td>
        <td> {{ round($percentCardProfit * 100, 2) }} %</td>
    </tr>
</table>

