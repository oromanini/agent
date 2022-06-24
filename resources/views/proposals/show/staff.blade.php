@if(auth()->user()->is_admin)
    <div class="columns box discount is-flex-wrap-wrap">
        <div class="column is-12">
            <button class="button is-rounded is-static" id="button-show-hide" type="button">
                <ion-icon name="eye-outline"></ion-icon>&nbsp; Diretoria
            </button>
        </div>
        <div id="show-hide" class="hide">
            <div class="column is-2">
                <label for="">Custo Kit</label>
                <p class="proposalData">{{ $valueHistoryData['cost'] }}</p>
            </div>
            <div class="column is-2">
                <label for="">Custo Serviços</label>
                <p class="proposalData">R$ {{ floatToMoney($valueHistoryData['totalCost']['services_cost'])  }}</p>
            </div>
            <div class="column is-2">
                <label for="">Lucro bruto</label>
                <p class="proposalData">{{ round($valueHistoryData['gross_profit']*100, 2) }}%</p>
            </div>
            <div class="column is-2">
                <label for="">Lucro Líquido (R$)</label>
                <p class="proposalData">{{ round($valueHistoryData['totalCost']['net_profit_percent'] * 100, 2) }}%</p>
            </div>
            <div class="column is-2">
                <label for="">Lucro Líquido (R$)</label>
                <p class="proposalData">{{ floatToMoney($valueHistoryData['totalCost']['net_profit_value']) }}</p>
            </div>
        </div>
    </div>
@endif

<script>

    $('#button-show-hide').on('click', function () {

        if ($('#show-hide').hasClass('hide')) {
            $('#show-hide').removeClass('hide')
            $('#show-hide').addClass('show')
        } else {
            $('#show-hide').addClass('hide')
            $('#show-hide').removeClass('show')
        }
    })


</script>

<style>

    .hide {
        display: none;
    }

    .show {
        display: flex;
    }

</style>
