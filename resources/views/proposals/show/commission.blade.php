<form action="{{ route('valueHistory.commission', [$proposal->id]) }}" method="post">
    @csrf
    <div class="columns discount box">
        <div class="column is-2" style="padding-right: 50px">
            <div class="field">
                <label class="label">Comissão (3 a 10%)</label>
                <div class="control">
                    <input class="input" type="number" step="0,5" min="3" max="10"
                           value="{{ $proposal->valueHistory->commission_percent }}">
                </div>
            </div>
        </div>
        <div class="column is-2 ">
            <label for=""> Valor da comissão</label>
            <div class="control"><p class="proposalData">
                    R$ {{ floatToMoney(
                        (($proposal->valueHistory->final_price)
                        - (($proposal->valueHistory->final_price * ($proposal->valueHistory->discount_percent / 100)))) * ($proposal->valueHistory->commission_percent / 100)
                        )
                    }}</p></div>
        </div>
        <div class="column is-2">
            <label for="">
                Antes</label>
            <div class="control"><p class="proposalData">R$ {{ formatFloat(
                        (($proposal->valueHistory->final_price)
                        - (($proposal->valueHistory->final_price * ($proposal->valueHistory->discount_percent / 100))))
                    ) }}</p></div>
        </div>
        <div class="column is-2 ">
            <label for="">
                Depois</label>
            <div class="control"><p class="proposalData">
                    R$ {{ floatToMoney(
                    (($proposal->valueHistory->final_price)
                        - (($proposal->valueHistory->final_price * ($proposal->valueHistory->discount_percent / 100)))) - (
                            (($proposal->valueHistory->final_price)
                        - (($proposal->valueHistory->final_price * ($proposal->valueHistory->discount_percent / 100)))) * (1)
                        )
                    )
                    }}</p></div>
        </div>
        <div class="column is-1">
            <button class="button is-primary is-large is-rounded" type="submit"><ion-icon name="checkmark-outline"></ion-icon></button>
        </div>
    </div>
</form>
