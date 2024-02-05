@if(auth()->user()->id == 1 || auth()->user()->id == 2)
    <div class="columns box discount is-flex-wrap-wrap">
        <div class="column is-12">
            <button class="button is-rounded " id="button-show-hide" type="button">
                <ion-icon name="eye-outline"></ion-icon>&nbsp; Diretoria
            </button>
        </div>
        <div id="show-hide" class="hide d-flex">
            <div class="columns">
                <div class="column is-2">
                    <label for="">Custo Kit</label>
                    <p class="proposalData">{{ $valueHistoryInfo->kitCost }}</p>
                </div>
                {{--            ---------------------------}}
                <div class="column is-2">
                    <label for="">Custo total À/V</label>
                    <p class="proposalData">{{ $valueHistoryInfo->cash['totalCost'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">Custo total FINANC.</label>
                    <p class="proposalData">{{ $valueHistoryInfo->financing['totalCost'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">Custo total CARTÃO</label>
                    <p class="proposalData">{{ $valueHistoryInfo->card['totalCost'] }}</p>
                </div>
                {{--            ------------------------------}}
                <div class="column is-2">
                    <label for="">Instalação</label>
                    <p class="proposalData">{{ $valueHistoryInfo->card['installation'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">Homologação</label>
                    <p class="proposalData">{{ $valueHistoryInfo->card['homologation'] }}</p>
                </div>
            </div>
            <div class="columns">
                <div class="column is-2">
                    <label for="">Acompanhamento da obra</label>
                    <p class="proposalData">{{ $valueHistoryInfo->card['workMonitoring'] }}</p>
                </div>
                {{--            -----------------------}}
                <div class="column is-2">
                    <label for="">C.A (À/V)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->cash['directCurrent'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">C.A (Financ.)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->financing['directCurrent'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">C.A (Cartão)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->card['directCurrent'] }}</p>
                </div>
                {{--            -------------------}}
                <div class="column is-2">
                    <label for="">Imposto (À/V)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->cash['tax'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">Imposto (Financ)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->financing['tax'] }}</p>
                </div>
            </div>
            <div class="columns">
                <div class="column is-2">
                    <label for="">Imposto (Cartão)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->card['tax'] }}</p>
                </div>
                {{--            ----------------}}
                <div class="column is-2">
                    <label for="">Margem Seg. (À/V)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->cash['safetyMargin'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">Margem Seg. (Financ.)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->financing['safetyMargin'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">Margem Seg. (Cartão)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->card['safetyMargin'] }}</p>
                </div>
                {{--            ------------------------------}}
                <div class="column is-2">
                    <label for="">Rlt (À/V)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->cash['royalties'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">Rlt (Financ.)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->financing['royalties'] }}</p>
                </div>
            </div>
            <div class="columns">
                <div class="column is-2">
                    <label for="">Rlt (Cartão)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->card['royalties'] }}</p>
                </div>
                {{--            -------}}
                <div class="column is-2">
                    <label for="">Int. Co. Comm. (À/V)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->cash['commercialInternalCommission'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">Int. Co. Comm. (Finan.)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->financing['commercialInternalCommission'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">Int. Co. Comm. (Cartão)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->card['commercialInternalCommission'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">Int. FINANC. Comm. (Financ)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->financing['financialInternalCommission'] }}</p>
                </div>
                {{--            -----------}}
                <div class="column is-2">
                    <label for="">Comm. Cons. (À/V)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->cash['externalCommission'] }}</p>
                </div>
            </div>
            <div class="columns">
                <div class="column is-2">
                    <label for="">Comm. Cons. (Financ)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->financing['externalCommission'] }}</p>
                </div>
                <div class="column is-2">
                    <label for="">Comm. Cons. (Cartão)</label>
                    <p class="proposalData">{{ $valueHistoryInfo->card['externalCommission'] }}</p>
                </div>
                {{--            -----------}}
                <div class="column is-2">
                    <label for="">Tx. Antec.</label>
                    <p class="proposalData">{{ $valueHistoryInfo->card['cardFee'] }}</p>
                </div>
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
        flex-wrap: wrap;
    }

</style>
