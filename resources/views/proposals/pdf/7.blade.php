<div class="page page-break" style="background-image: url({{public_path('/img/proposal/4.jpg')}})">
    <div id="installment48"><span class="mini">R$</span> {{ floatToMoney(($finalValue * \App\Enums\InstallmentEnum::FORTY_EIGHT) / 48) }}</div>
    <div id="installment60"><span class="mini">R$</span> {{ floatToMoney(($finalValue * \App\Enums\InstallmentEnum::SIXTY) / 60) }}</div>
    <div id="installment72"><span class="mini">R$</span> {{ floatToMoney(($finalValue * \App\Enums\InstallmentEnum::SEVENTY_TWO) / 72) }}</div>
    <div id="installment84"><span class="mini">R$</span> {{ floatToMoney(($finalValue * \App\Enums\InstallmentEnum::EIGHTY_FOUR) / 84) }}</div>
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
        top: 840px;
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
        top: 975px;
        left: 660px;
    }

    #withoutSolar {
        color: #F9880D;
        position: absolute;
        font-size: 28pt;
        font-weight: 600;
        top: 1600px;
        left: 1040px;
    }

    #withSolar {
        color: #F9880D;
        position: absolute;
        font-size: 28pt;
        font-weight: 600;
        top: 2030px;
        left: 1040px;
    }



</style>
