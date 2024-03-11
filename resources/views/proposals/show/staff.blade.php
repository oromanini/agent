@if(auth()->user()->id == 1 || auth()->user()->id == 2)
    <div class="columns box discount is-flex-wrap-wrap">
        <div class="column is-12">
            <button class="button is-rounded " id="button-show-hide" type="button">
                <ion-icon name="eye-outline"></ion-icon>&nbsp; Diretoria
            </button>
        </div>
        <div id="show-hide" class="hide d-flex">
            @include('proposals.show.staff_table')
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
