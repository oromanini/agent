<div class="columns">
    <div class="column">
        <div class="tile">
            <article class="tile is-child notification @if(isset($proposal->inspection) && ($proposal->inspection->status == 'Aprovado' || $proposal->inspection->status == 'Aprovado com adequação')) is-success @else is-warning @endif">
                <h5>
                    <ion-icon name="construct-outline"></ion-icon>
                    Vistoria
                </h5>
                <p class="title"> {{ !is_null($proposal->inspection) ? $proposal->inspection->status : 'Aguardando'}}</p>
            </article>
        </div>
    </div>
    <div class="column">
        <div class="tile">
            <article class="tile is-child notification
                     @if(isset($proposal->financing) && ($proposal->financing->status == 'Aprovado')) is-success
                     @elseif(isset($proposal->financing) && ($proposal->financing->status == 'Reprovado')) is-danger
                     @else is-warning
                     @endif">
                <h5>
                    <ion-icon name="wallet-outline"></ion-icon>
                    Financiamento
                </h5>
                <p class="title"> {{ !is_null($proposal->financing) ? $proposal->financing->status : 'Aguardando'}}</p>
            </article>
        </div>
    </div>
    <div class="column">
        <div class="tile">
            <article class="tile is-child notification @if(isset($proposal->contract) && $proposal->contract->status == 'Finalizado') is-success @else is-warning @endif">
                <h5>
                    <ion-icon name="document-text-outline"></ion-icon>
                    Contrato
                </h5>
                <p class="title"> {{ !is_null($proposal->contract) ? $proposal->contract->status : 'Aguardando'}}</p>
            </article>
        </div>
    </div>
</div>
