<form action="{{ route('valueHistory.updatePrice', [$proposal->id]) }}" method="post">
    @csrf
    <div class="columns discount box">
        <div class="column is-2" style="padding-right: 50px">
            <div class="field">
                <label class="label">Comissão <span style="font-size: 8pt">(Máx. 12%)</span></label>
                <div class="control">
                    <input {{ $proposal->send_date !== null ? 'disabled' : '' }}
                           class="input" type="number" step="0.01" min="3" max="12" name="commission_percent"
                           value="{{ jsonToArray($proposal->valueHistory->commission)['commission_percentage'] * 100}}">
                </div>
            </div>
        </div>
        <div class="column is-2 ">
            <label for=""> Comissão Inicial</label>
            <div class="control">
                <p class="proposalData">
                    R$ {{ floatToMoney($valueHistoryInfo->cash['InitialExternalCommission']) }}
                </p>
            </div>
        </div>
        <div class="column is-2 ">
            <label for=""> Comissão Final</label>
            <div class="control">
                <p class="proposalData">
                    R$ {{ floatToMoney($valueHistoryInfo->cash['externalCommission']) }}
                </p>
            </div>
        </div>
        <div class="column is-1 ">
            <label for=""> Descto. Comissão</label>
            <div class="control">
                <p class="proposalData">
                    R$ {{ floatToMoney($valueHistoryInfo->cash['commissionDiscount']) }}
                </p>
            </div>
        </div>
        <div class="column is-2">
            <label for="">
                Preço Antes</label>
            <div class="control">
                <p class="proposalData">
                    R$ {{ floatToMoney($valueHistoryInfo->financing['initialPrice'] - $valueHistoryInfo->financing['defaultDiscount']) }}
                </p>
            </div>
        </div>
        <div class="column is-2 ">
            <label for="">
                Preço Depois</label>
            <div class="control"><p class="proposalData">
                    R$ {{ floatToMoney($valueHistoryInfo->cash['finalPrice']) }}
                </p></div>
        </div>
        <div class="column is-1">
            <button {{ $proposal->send_date !== null ? 'disabled' : '' }}
                class="button is-primary is-large is-rounded" type="submit"><ion-icon name="checkmark-outline"></ion-icon></button>
        </div>
    </div>
</form>
