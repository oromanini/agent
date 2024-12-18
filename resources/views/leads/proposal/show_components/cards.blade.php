{{--AVERAGE--}}
<div class="column is-3">
    <div id="average-card">
        <div class="card-content is-flex is-justify-content-center is-align-items-center is-flex-direction-column">
            <div id="title">
                {{ $lead->average_consumption }}
            </div>
            <span id="sub-title">kwh</span>
        </div>
        <div class="card-footer is-flex is-justify-content-center">
            <span id="footer-title">
                <ion-icon name="flash-outline"></ion-icon>
                Média mês/ano
            </span>
        </div>
    </div>
</div>

{{--PRODUCTION--}}
<div class="column is-3">
    <div id="average-card">
        <div class="card-content is-flex is-justify-content-center is-align-items-center is-flex-direction-column">
            <div id="title">
                {{ floatToMoney($lead->pricing()['final_price']['finalPrice']) }}
            </div>
            <span id="sub-title">R$</span>
        </div>
        <div class="card-footer is-flex is-justify-content-center">
            <span id="footer-title">
                <ion-icon name="cash-outline"></ion-icon>
                Financiamento/ À vista
            </span>
        </div>
    </div>
</div>

{{--SYSTEM PRICE--}}
<div class="column is-3">
    <div id="average-card">
        <div class="card-content is-flex is-justify-content-center is-align-items-center is-flex-direction-column">
            <div id="title">
                {{ ceil($average) }}
            </div>
            <span id="sub-title">kWh</span>
        </div>
        <div class="card-footer is-flex is-justify-content-center">
            <span id="footer-title">
                <ion-icon name="sunny-outline"></ion-icon>
                Média mês/ano</span>
        </div>
    </div>
</div>


<style>
    #average-card {
        background-color: hsl(0deg 0% 70.87%);
        border-radius: 5px;
        margin-top: 20px;
    }

    #title {
        color: #fff;
        font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 30pt;
    }

    #sub-title {
        font-size: 12pt;
        color: #fff;
    }

    #footer-title {
        color: #fff;
        font-size: 12pt;
        margin: 5px 0;
    }

</style>
