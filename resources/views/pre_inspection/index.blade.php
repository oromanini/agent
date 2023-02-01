<div class="columns">
    <div class="column">
        <h1 class="title">Pré-vistoria</h1>
    </div>
</div>
<div class="columns" style="margin-bottom: 50px">
    <div class="column is-4" style="padding: 40px">
        <h5 class="subtitle is-5 has-text-justified" style="line-height: 1.8em">
            A <span class="oranged">pré-vistoria</span> é uma etapa muito importante. É por meio dela que você ajudará a
            nossa equipe com algumas
            <span class="oranged">fotos prévias</span> do local de instalação.
        </h5>
    </div>
    <div class="column is-4" style="padding: 40px">
        <h5 class="subtitle is-5 has-text-justified" style="line-height: 1.8em">
            Lembre-se de tirar fotos <span
                class="oranged">boas e nítidas,</span>
            anotar tudo em <span class="oranged">seu croqui</span> e
            colocar no campo de observações <span class="oranged">todos os detalhes necessários</span> (Exemplo:
            Sombreamento no lado leste).

        </h5>
    </div>
    <div class="column is-4" style="padding: 40px">
        <h5 class="subtitle is-5 has-text-justified" style="line-height: 1.8em">
            Se você precisar de ajuda com a pré-vistoria, assista ao nosso vídeo de ajuda <a href="#"> CLICANDO AQUI</a>.
            Ou entre em contato com a nossa
            equipe pelo botão do whatsapp no canto inferior direito
        </h5>
    </div>
</div>

<form action="{{ route('inspection.update', [$proposal->id]) }}" method="post" enctype="multipart/form-data">
    @method('put')
    @csrf
    <div class="columns is-fullwidth is-flex-wrap-wrap ml-3">
        @foreach($fields as $field)
            <div class="column is-3">
                <div id="{{ $field['id'] }}" class="file is-fullwidth is-centered is-boxed is-success has-name">
                    <label class="file-label" style="text-align: center">
                        <input class="file-input" {{ $field['id'] == 'roof' ? 'multiple' : '' }} type="file" name="{{ $field['name'] }}">
                        <span class="file-cta">
                    <ion-icon name="image-outline"></ion-icon>
                    <span class="file-label text-center">Foto {{ $field['label'] }}</span>
                </span>
                        <span class="file-name">
                    Arquivo não selecionado
                </span>
                    </label>
                </div>
            </div>
        @endforeach
    </div>

    <div class="columns is-12" style="margin: 20px 10px;">
        <div class="column is-3">
            <div class="field">
                <label for="circuit_breaker_amperage" class="label">Disjuntor (A)?</label>
                <div
                    class="select is-multiline is-fullwidth is-rounded  @error('circuit_break') is-danger @enderror">
                    <select id="circuit_breaker_amperage" name="circuit_breaker_amperage">
                        @for($i = 10; $i <= 150; $i = $i + 5)
                            <option {{ $proposal->preInspection->circuit_breaker_amperage == $i ? 'selected' : ''}} value="{{ $i }}">{{ $i }}A</option>
                        @endfor
                    </select>
                    @error('circuit_breaker_amperage')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="columns" style="margin: 50px 10px;">
        <div class="column is-12">
            <label class="label" for="observations">Há alguma observação? Algo que você acha importante
                destacar?</label>
            <textarea id="observations" name="observations" class="textarea"
                      placeholder="Sombreamento? Dificuldade de acesso? Estrutura comprometida? Digite suas observações aqui">{{ $proposal->preInspection->observations ?? '' }}</textarea>
        </div>
    </div>

    <div class="columns">
        <div class="column is-flex is-justify-content-center">
            <button type="submit" class="button is-large is-info">
                <ion-icon name="save-outline"></ion-icon> &nbsp; Salvar
            </button>
        </div>
    </div>

</form>

<hr>
<div class="columns is-flex is-flex-wrap-wrap" style="margin-top: 50px">
    @include('pre_inspection.images')
</div>

@include('pre_inspection.images_script')

<style>
    .pre-inspection-video {
        background-color: #a00000;
        border-radius: 50px;
        color: #fff;
        padding: 5px 10px;
    }

    .oranged {
        color: #ff6200;
        font-weight: 800;
    }

</style>
