@if(
    (!$isPromotional && !$proposal->is_manual)
    || ($proposal->is_manual && auth()->user()->is_admin)
    )
    <form action="{{ route('valueHistory.updatePrice', [$proposal->id]) }}" method="post">
        @csrf
        <div class="columns discount box">
            <div class="column is-2 " style="padding-right: 50px">
                <div class="field">
                    <label class="label">Desconto <span style="font-size: 8pt">(Máx. 3%)</span></label>
                    <div class="control">
                        <input {{ $proposal->send_date !== null ? 'disabled' : '' }}
                            class="input" type="number" step="0.25" min="0" max="3" name="discount_percent"
                               value="{{$proposal->valueHistory->discount_percent}}">
                    </div>
                </div>
            </div>
            <div class="column is-2">
                <label for=""> Valor do desconto</label>
                <div class="control"><p class="proposalData">
                        R$ {{ floatToMoney($valueHistoryInfo->financing['defaultDiscount']) }}</p>
                </div>
            </div>
            <div class="column is-2">
                <label for="">Antes</label>
                <div class="control"><p class="proposalData">
                        R$ {{ floatToMoney($valueHistoryInfo->financing['initialPrice']) }}</p>
                </div>
            </div>
            <div class="column is-2">
                <label for="">Depois</label>
                <div class="control"><p class="proposalData">
                        R$ {{ floatToMoney($valueHistoryInfo->financing['initialPrice'] - $valueHistoryInfo->financing['defaultDiscount']) }}</p>
                </div>
            </div>
            <div class="column is-1">
                <br>
                <button {{ $proposal->send_date !== null ? 'disabled' : '' }}
                    class="button is-primary is-large is-rounded" type="submit">
                    <ion-icon name="checkmark-outline"></ion-icon>
                </button>
            </div>
        </div>
    </form>
@endif
