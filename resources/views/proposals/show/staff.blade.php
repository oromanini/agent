@if(auth()->user()->is_admin)
    <div class="discount box">
        <div class="columns">
            <label for="" class="label">Área do administrador</label>
        </div>
        <hr>
        <div class="columns">
            <div class="column is-2">
                <label class="label">Custo do kit</label>
                <p>R$ {{ floatToMoney($kit['cost_value']) }}</p>
            </div>
            <div class="column is-3">
                <label class="label">Instalação</label>
                <p>R$ {{ floatToMoney($kit['cost_value']) }}</p>
            </div>
        </div>
    </div>
@endif
