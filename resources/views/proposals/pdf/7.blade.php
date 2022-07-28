<div class="page page-break" style="background-image: url({{public_path('/img/proposal/7.jpg')}})">
    <div id="serviceCost">R$ {{ floatToMoney($proposal->valueHistory->final_price * 0.2) }}</div>
    <div id="kitCost">R$ {{ floatToMoney($proposal->valueHistory->final_price * 0.8) }}</div>
    <div id="finalValue">R$ {{ floatToMoney($proposal->valueHistory->final_price) }} <span class="minifiedText">*à vista</span></div>
    <div id="validate">Válido até {{ $proposal->created_at->addDays(7)->format('d/m/Y') }}</div>
    <div id="withoutSolar">R$ {{ $withoutSolar }}</div>
    <div id="withSolar">R$ {{ $withSolar }}</div>
</div>

<style>
    #kitCost, #serviceCost {
        color: #F9880D;
        font-size: 28pt;
        font-weight: 600;
        position: absolute;
        top: 550px;
    }

    #kitCost {
        left: 110px;
    }

    #serviceCost {
        left: 980px;
    }

    #finalValue {
        color: #F9880D;
        font-size: 40pt;
        font-weight: 900;
        position: absolute;
        top: 900px;
        left: 220px;
    }

    #validate {
        color: #F9880D;
        font-size: 10pt;
        font-weight: 900;
        position: absolute;
        top: 1040px;
        left: 220px;
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
