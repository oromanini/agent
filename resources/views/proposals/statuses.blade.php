<div class="columns">
    <div class="column">
        <div class="tile">
            <article class="tile is-child notification is-warning">
                <h5 class="title is-5">
                    <ion-icon name="construct-outline"></ion-icon><ion-icon name="calculator-outline"></ion-icon>
                    Vistoria
                </h5>
                <p class="title"> {{ $proposal->inspection->status }}</p>
            </article>
        </div>
    </div>
    <div class="column">
        <div class="tile">
            <article class="tile is-child notification is-warning">
                <h5 class="title is-5">
                    <ion-icon name="cash-outline"></ion-icon><ion-icon name="wallet-outline"></ion-icon>
                    Financiamento
                </h5>
                <p class="title"> {{ $proposal->financial->status }}</p>
            </article>
        </div>
    </div>
    <div class="column">
        <div class="tile">
            <article class="tile is-child notification is-warning">
                <h5 class="title is-5">
                    <ion-icon name="document-text-outline"></ion-icon><ion-icon name="pencil-outline"></ion-icon>
                    Contrato
                </h5>
                <p class="title"> {{ $proposal->contract->status }}</p>
            </article>
        </div>
    </div>
</div>
