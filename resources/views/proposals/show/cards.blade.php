<div class="columns">
    <div class="column">
        <div class="tile">
            <article class="tile is-child notification is-warning">
                <h5 class="title is-5">
                    <ion-icon name="pricetags-outline"></ion-icon>
                    Preço
                </h5>
                <p class="title">R$ {{ floatToMoney($proposal->valueHistory->final_price) }}</p>
            </article>
        </div>
    </div>
    <div class="column">
        <div class="tile">
            <article class="tile is-child notification is-warning">
                <h5 class="title is-7">
                    <ion-icon name="pricetags-outline"></ion-icon>
                    Cartão de crédito
                </h5>
                <p class="title">
                <span style="font-size: 14pt">12x de R$ </span>{{ floatToMoney($valueHistoryInfo->card['finalPriceWithFee'] / 12) }}
                </p>
            </article>
        </div>
    </div>
    <div class="column">
        <div class="tile">
            <article class="tile is-child notification is-warning">
                <h5 class="title is-5">
                    <ion-icon name="flash-outline"></ion-icon>
                    Geração
                </h5>
                <p class="title">{{ ceil($proposal->estimated_generation) }} <span style="font-size: 8pt">kWh/mês</span></p>
            </article>
        </div>
    </div>
    <div class="column">
        <div class="tile">
            <article class="tile is-child notification is-warning">
                <h5 class="title is-5">
                    <ion-icon name="bulb-outline"></ion-icon>
                    Consumo
                </h5>
                <p class="title">{{ ceil($proposal->average_consumption) }} <span style="font-size: 8pt">kWh/mês</span></p>
            </article>
        </div>
    </div>
    <div class="column">
        <div class="tile">
            <article class="tile is-child notification is-warning">
                <h5 class="title is-5">
                    <ion-icon name="sunny-outline"></ion-icon>
                    Potência
                </h5>
                <p class="title">{{ $proposal->kwp }} <span style="font-size: 12pt">kWP</span></p>
            </article>
        </div>
    </div>

</div>
