@php

    $three_years_economy = 0;
        for ($i = 1; $i<=3; $i++) {
            $three_years_economy += $payback['data'][$i]['economy'];
        }

    $twenty_five_years_economy = 0;
        foreach ($payback['data'] as $key => $value) {
            $twenty_five_years_economy += $value['economy'];
        }
        $twenty_five_years_economy -= $proposal->valueHistory->final_price;
@endphp

<div class="page page-break" style="background-image: url({{public_path('/img/proposal_v2/3.jpg')}})">
    @include('proposals.pdf_v2.number')

    <div id="finalValue">R$ {{ floatToMoney($proposal->valueHistory->final_price) }} <span class="minifiedText">à vista</span></div>
    <div id="3yearseconomy">R$ {{ floatToMoney($three_years_economy) }}</div>
    <div id="25yearseconomy">R$ {{ floatToMoney($twenty_five_years_economy) }}</div>
    <div id="validate">Preço válido até {{ $proposal->created_at->addDays(7)->format('d/m/Y') }}</div>
</div>

<style>
    #finalValue {
        color: #F9880D;
        font-size: 20pt;
        font-weight: 900;
        position: absolute;
        top: 830px;
        left: 95px;
        background-color: #fff;
        padding: 15px 15px 50px 15px;
        border-radius: 30px;
    }

    #3yearseconomy {
        color: #F9880D;
        font-size: 20pt;
        font-weight: 900;
        position: absolute;
        top: 830px;
        left: 640px;
        background-color: #fff;
        padding: 15px 15px 50px 15px;
        border-radius: 30px;
    }

    #25yearseconomy {
        color: #F9880D;
        font-size: 20pt;
        font-weight: 900;
        position: absolute;
        top: 830px;
        left: 1140px;
        background-color: #fff;
        padding: 15px 15px 50px 15px;
        border-radius: 30px;
    }


    #validate {
        color: #F9880D;
        font-size: 10pt;
        font-weight: 900;
        position: absolute;
        top: 1040px;
        left: 660px;
    }

    .minifiedText {
        font-size: 8pt !important;
        position: absolute;
        top: 80px;
        left: 20px;
    }

</style>
