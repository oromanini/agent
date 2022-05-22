<form action="{{ route('valueHistory.updatePrice', [$proposal->id]) }}" method="post">
    @csrf
    <div class="columns discount box">
        <div class="column is-2 " style="padding-right: 50px">
            <div class="field">
                <label class="label">Desconto (Máx. 2%)</label>
                <div class="control">
                    <input class="input" type="number" step="0.25" min="0" max="2"
                           value="{{$proposal->valueHistory->discount_percent}}">
                </div>
            </div>
        </div>
        <div class="column is-2">
            <label for=""> Valor do desconto</label>
            <div class="control"><p class="proposalData">
                    R$ {{ floatToMoney($proposal->valueHistory->final_price * ($proposal->valueHistory->discount_percent / 100)) }}</p>
            </div>
        </div>
        <div class="column is-2">
            <label for="">Antes</label>
            <div class="control"><p class="proposalData">R$ {{ formatFloat($proposal->valueHistory->final_price) }}</p>
            </div>
        </div>
        <div class="column is-2">
            <label for="">Depois</label>
            <div class="control"><p class="proposalData">
                    R$ {{ floatToMoney($proposal->valueHistory->final_price - ($proposal->valueHistory->final_price * ($proposal->valueHistory->discount_percent / 100))) }}</p>
            </div>
        </div>
        <div class="column is-1">
            <br>
            <button class="button is-primary is-large is-rounded" type="submit"><ion-icon name="checkmark-outline"></ion-icon></button>
        </div>
    </div>
</form>
