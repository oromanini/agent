@php
    $forty_eight = floatToMoney(($finalValue * \App\Enums\InstallmentEnum::FORTY_EIGHT) / 48);
    $sixty = floatToMoney(($finalValue * \App\Enums\InstallmentEnum::SIXTY) / 60);
    $seventy_two = floatToMoney(($finalValue * \App\Enums\InstallmentEnum::SEVENTY_TWO) / 72);
    $eighty_four = floatToMoney(($finalValue * \App\Enums\InstallmentEnum::EIGHTY_FOUR) / 84);

@endphp

<div class="page page-break" style="background-image: url({{public_path('/img/proposal/4.jpg')}})">
    <div id="installment48" @if (strlen($forty_eight) >= 9) style="font-size: 18pt !important;" @endif><span class="mini">R$</span> {{ $forty_eight }}</div>
    <div id="installment60" @if (strlen($sixty) >= 9) style="font-size: 18pt !important;" @endif><span class="mini">R$</span> {{ $sixty }}</div>
    <div id="installment72"@if (strlen($seventy_two) >= 9) style="font-size: 18pt !important;" @endif><span class="mini">R$</span> {{ $seventy_two }}</div>
    <div id="installment84"@if (strlen($eighty_four) >= 9) style="font-size: 18pt !important;" @endif><span class="mini">R$</span> {{ $eighty_four }}</div>
    <div id="finalValue">R$ {{ floatToMoney($proposal->valueHistory->final_price) }} <span class="minifiedText">*à vista</span></div>
    <div id="validate">Válido até {{ $proposal->created_at->addDays(7)->format('d/m/Y') }}</div>
</div>

<style>
    #kitCost, #installment48, #installment60, #installment72, #installment84 {
        color: #F9880D;
        font-size: 22pt;
        font-weight: 600;
        position: absolute;
        top: 650px;
    }

    .mini {
        font-size: 8pt !important;
    }

    #installment48 {
        left: 100px;
    }

    #installment60 {
        left: 450px;
    }

    #installment72 {
        left: 830px;
    }

    #installment84 {
        left: 1220px;
    }

    #finalValue {
        color: #F9880D;
        font-size: 28pt;
        font-weight: 900;
        position: absolute;
        top: 830px;
        left: 640px;
        background-color: #fff;
        padding: 15px 15px 50px 15px;
        border-radius: 30px;
    }

    #validate {
        color: #F9880D;
        font-size: 10pt;
        font-weight: 900;
        position: absolute;
        top: 945px;
        left: 660px;
    }

</style>
