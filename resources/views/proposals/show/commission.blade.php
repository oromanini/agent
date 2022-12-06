@if(is_null($proposal->send_date))
<form action="{{ route('valueHistory.updatePrice', [$proposal->id]) }}" method="post">
    @csrf
    <div class="columns discount box">
        <div class="column is-2" style="padding-right: 50px">
            <div class="field">
                <label class="label">Comissão</label>
                <div class="control">
                    <input class="input" type="number" step="0.1" min="3" max="10" name="commission_percent"
                           value="{{ $proposal->valueHistory->commission_percent }}">
                </div>
            </div>
        </div>
        <div class="column is-2 ">
            <label for=""> Comissão Inicial</label>
            <div class="control">
                <p class="proposalData">
                    R$ {{ floatToMoney($valueHistoryData['initialCommission']) }}
                </p>
            </div>
        </div>
        <div class="column is-2 ">
            <label for=""> Comissão Final</label>
            <div class="control">
                <p class="proposalData">
                    R$ {{ floatToMoney($valueHistoryData['finalCommission']) }}
                </p>
            </div>
        </div>
        <div class="column is-1 ">
            <label for=""> Descto. Comissão</label>
            <div class="control">
                <p class="proposalData">
                    R$ {{ floatToMoney($valueHistoryData['initialCommission'] - $valueHistoryData['finalCommission']) }}
                </p>
            </div>
        </div>
        <div class="column is-2">
            <label for="">
                Preço Antes</label>
            <div class="control"><p class="proposalData">R$ {{ floatToMoney($valueHistoryData['calculateBase']) }}</p></div>
        </div>
        <div class="column is-2 ">
            <label for="">
                Preço Depois</label>
            <div class="control"><p class="proposalData">
                    R$ {{ floatToMoney($proposal->valueHistory->final_price) }}
                </p></div>
        </div>
        <div class="column is-1">
            <button class="button is-primary is-large is-rounded" type="submit"><ion-icon name="checkmark-outline"></ion-icon></button>
        </div>
    </div>
</form>
@endif
