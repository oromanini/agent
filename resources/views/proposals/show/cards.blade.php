<div class="columns">
    <div class="column is-3">
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
    <div class="column is-3">
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
    <div class="column is-3">
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
    <div class="column is-3">
        <div class="tile">
            <article class="tile is-child notification is-warning">
                <h5 class="title is-5">
                    <ion-icon name="sunny-outline"></ion-icon>
                    Quantidade de painéis
                </h5>
                <p class="title">{{ $proposal->number_of_panels }}</p>
            </article>
        </div>
    </div>

</div>
