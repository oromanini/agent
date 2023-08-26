<div id="loader" class="show">
    <img src="/img/loader.gif" width="70" style="z-index: 10;background-color: #fff; border-radius: 100%">
    <span id="loader-text" style="color: #fff; font-size: 40pt; margin-top: 20px">Carregando, aguarde ...</span>
</div>


<style>
    #loader {
        width: 100%;
        height: 100%;
        background: #F0B921;
        z-index: 10;
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        transition: width 1s ease-out;
    }

    #loader-text {
        opacity: 1; /* Começa com opacidade 1 para exibição imediata */
        transition: opacity 0.5s;
    }

    .hide {
        opacity: 0; /* Aplica opacidade 0 para ocultar */
    }
</style>

<script>
    const loader = document.getElementById('loader');
    const loaderText = document.getElementById('loader-text');
    const phrases = ["Carregando, aguarde ...", "Só mais um pouco ...", "Estamos quase lá ...", "Buscando as melhores opções...", "Só mais um pouquinho...", "Vai dar certo, falta pouco..."];
    let currentIndex = 0;

    function changePhrase() {
        loader.classList.remove('show'); // Remove a classe .show para iniciar o fade-out

        setTimeout(() => {
            loaderText.textContent = phrases[currentIndex];
            currentIndex = (currentIndex + 1) % phrases.length;

            loader.classList.add('show'); // Adiciona a classe .show para iniciar o fade-in
        }, 500);
    }

    setInterval(changePhrase, 5000);
</script>
