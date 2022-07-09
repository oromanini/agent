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
            Se você precisar de ajuda com a pré-vistoria, assista ao nosso vídeo de ajuda <a href="#"> CLICANDO AQUI</a>. Ou entre em contato com a nossa
            equipe pelo botão do whatsapp no canto inferior direito
        </h5>
    </div>
</div>

<form action="{{ route('inspection.update', [$proposal->id]) }}" method="post" enctype="multipart/form-data">
    @method('put')
    @csrf
    <div class="columns is-fullwidth">
        <div class="column is-3">
            <div id="croqui" class="file is-centered is-boxed is-success has-name">
                <label class="file-label">
                    <input class="file-input" type="file" name="inspection[croqui]">
                    <span class="file-cta">
                    <span class="file-icon">
                        <ion-icon name="document-outline"></ion-icon>
                    </span>
                    <span class="file-label">Croqui</span>
                </span>
                    <span class="file-name">
                    Arquivo não selecionado
                </span>
                </label>
            </div>
        </div>
        <div class="column is-3">
            <div id="roof" class="file is-centered is-boxed is-success has-name">
                <label class="file-label">
                    <input class="file-input" type="file" name="inspection[roof][]" multiple>
                    <span class="file-cta">
                    <span class="file-icon">
                      <ion-icon name="home-outline"></ion-icon>
                  </span>
                  <span class="file-label">Telhado</span>
                </span>
                    <span class="file-name">
                    Arquivo não selecionado
                </span>
                </label>
            </div>
        </div>

        <div class="column is-3">
            <div id="pattern" class="file is-centered is-boxed is-success has-name">
                <label class="file-label">
                    <input class="file-input" type="file" name="inspection[pattern]">
                    <span class="file-cta">
                    <span class="file-icon">
                      <ion-icon name="time-outline"></ion-icon>
                    </span>
                    <span class="file-label">Padrão</span>
                </span>
                    <span class="file-name">
                    Arquivo não selecionado
                </span>
                </label>
            </div>
        </div>

        <div class="column is-3">
            <div id="pattern_circuit_break" class="file is-centered is-boxed is-success has-name">
                <label class="file-label">
                    <input class="file-input" type="file" name="inspection[circuit_breaker]">
                    <span class="file-cta">
                  <span class="file-icon">
                      <ion-icon name="toggle-outline"></ion-icon>                  </span>
                    <span class="file-label">Disjuntor do padrão</span>
                </span>
                    <span class="file-name">
                    Arquivo não selecionado
                </span>
                </label>
            </div>
        </div>

    </div>

    <div class="columns is-fullwidth">

        <div class="column is-3">
            <div id="switchboard" class="file is-centered is-boxed is-success has-name">
                <label class="file-label">
                    <input class="file-input" type="file" name="inspection[switchboard]">
                    <span class="file-cta">
                  <span class="file-icon">
                      <ion-icon name="options-outline"></ion-icon>
                  </span>
                    <span class="file-label">Quadro de distrib.</span>
                </span>
                    <span class="file-name">
                    Arquivo não selecionado
                </span>
                </label>
            </div>
        </div>

        <div class="column is-3">
            <div id="post" class="file is-centered is-boxed is-success has-name">
                <label class="file-label">
                    <input class="file-input" type="file" name="inspection[post]">
                    <span class="file-cta">
                  <span class="file-icon">
                      <ion-icon name="bulb-outline"></ion-icon>
                  </span>
                    <span class="file-label">Poste</span>
                </span>
                    <span class="file-name">
                    Arquivo não selecionado
                </span>
                </label>
            </div>
        </div>

        <div class="column is-3">
            <div id="compass" class="file is-centered is-boxed is-success has-name">
                <label class="file-label">
                    <input class="file-input" type="file" name="inspection[compass]">
                    <span class="file-cta">
                    <span class="file-icon">
                      <ion-icon name="compass-outline"></ion-icon>
                    </span>
                    <span class="file-label">Print Bússola</span>
                </span>
                    <span class="file-name">
                    Arquivo não selecionado
                </span>
                </label>
            </div>
        </div>

    </div>

    <div class="columns" style="margin: 50px 10px;">
        <div class="column is-12">
            <label class="label" for="observations">Há alguma observação? Algo que você acha importante
                destacar?</label>
            <textarea id="observations" name="observations" class="textarea"
                      placeholder="Sombreamento? Dificuldade de acesso? Estrutura comprometida? Digite suas observações aqui"></textarea>
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

<script>
    const croqui = document.querySelector('#croqui input[type=file]');
    const pattern = document.querySelector('#pattern input[type=file]');
    const pattern_circuit_break = document.querySelector('#pattern_circuit_break input[type=file]');
    const roof = document.querySelector('#roof input[type=file]');

    const switchboard = document.querySelector('#switchboard input[type=file]');
    const post = document.querySelector('#post input[type=file]');
    const compass = document.querySelector('#compass input[type=file]');

    croqui.onchange = () => {
        if (croqui.files.length > 0) {
            const fileName = document.querySelector('#croqui .file-name');
            fileName.textContent = croqui.files[0].name;
        }
    }

    pattern.onchange = () => {
        if (pattern.files.length > 0) {
            const fileName = document.querySelector('#pattern .file-name');
            fileName.textContent = pattern.files[0].name;
        }
    }

    pattern_circuit_break.onchange = () => {
        if (pattern_circuit_break.files.length > 0) {
            const fileName = document.querySelector('#pattern_circuit_break .file-name');
            fileName.textContent = pattern_circuit_break.files[0].name;
        }
    }

    roof.onchange = () => {
        if (roof.files.length > 0) {
            const fileName = document.querySelector('#roof .file-name');
            fileName.textContent = roof.files[0].name;
        }
    }

    switchboard.onchange = () => {
        if (switchboard.files.length > 0) {
            const fileName = document.querySelector('#switchboard .file-name');
            fileName.textContent = switchboard.files[0].name;
        }
    }

    post.onchange = () => {
        if (post.files.length > 0) {
            const fileName = document.querySelector('#post .file-name');
            fileName.textContent = post.files[0].name;
        }
    }

    compass.onchange = () => {
        if (compass.files.length > 0) {
            const fileName = document.querySelector('#compass .file-name');
            fileName.textContent = compass.files[0].name;
        }
    }

</script>

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
