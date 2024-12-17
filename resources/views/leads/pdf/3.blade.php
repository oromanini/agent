<div class="page page-break" style="background-image: url({{public_path('/img/leads/3.jpg')}})">

    <div id="financing">
        <span id="currency-code">R$</span> {{ floatToMoney(($lead->pricing()['final_price']['finalPrice'] * 1.9524) / 60) }}
        <span style="font-size: 8pt">Estimado*</span>
    </div>

    <div id="card">
        <span id="currency-code">R$</span> {{ floatToMoney($cardInstallment[18]) }}
    </div>

    <div id="cash">
        <span id="currency-code">R$</span> {{ floatToMoney(($lead->pricing()['final_price']['finalPrice'])) }}
    </div>

</div>

<style>

    #financing {
        position: absolute;
        padding: 540px 0 0 100px;
        font-size: 24pt;
    }

    #card {
        position: absolute;
        padding: 540px 0 0 620px;
        font-size: 24pt;
    }

    #cash {
        position: absolute;
        padding: 540px 0 0 1150px;
        font-size: 24pt;
    }

    #currency-code {
        font-size: 11pt;
    }

</style>
