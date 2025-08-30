@props([
    'progress' => 0, // Valor padrão de 0%
])

<div {{ $attributes->merge(['class' => 'progress-bar-container']) }}>
    <div class="progress-bar stripes animated">
        <span class="progress-bar-inner" style="width: {{ $progress }}%;"></span>
    </div>
    <div class="progress-bar stripes animated reverse slower">
        <span class="progress-bar-inner" style="width: {{ $progress }}%;"></span>
    </div>
</div>


<style>
    @keyframes animate-stripes {
        0% { background-position: 0 0; }
        100% { background-position: 60px 0; }
    }

    .progress-bar-container {
        width: 100%; /* Ocupa todo o espaço do container pai */
        margin: 20px 0;
    }

    .progress-bar {
        background-color: #1a1a1a;
        height: 35px; /* Altura um pouco menor para ficar mais elegante */
        width: 100%;
        margin: 10px auto;
        border-radius: 5px;
        box-shadow: 0 1px 5px #000 inset, 0 1px 0 #444;
    }

    .stripes {
        background-size: 30px 30px;
        background-image: linear-gradient(
            135deg,
            rgba(255, 255, 255, .15) 25%, transparent 25%,
            transparent 50%, rgba(255, 255, 255, .15) 50%,
            rgba(255, 255, 255, .15) 75%, transparent 75%,
            transparent
        );
    }

    .stripes.animated {
        animation: animate-stripes 0.6s linear infinite;
    }

    .stripes.animated.slower {
        animation-duration: 1.25s;
    }

    .stripes.reverse {
        animation-direction: reverse;
    }

    .progress-bar-inner {
        display: block;
        height: 100%;
        width: 0%; /* O valor será definido pelo style inline */
        border-radius: 3px;
        box-shadow: 0 1px 0 rgba(255, 255, 255, .5) inset;
        position: relative;
        transition: width 0.4s ease-in-out, background-color 0.4s;

        /* >>>>>>>> ALTERAÇÕES PRINCIPAIS AQUI <<<<<<<<<< */

        /* 1. Cor padrão alterada para amarelo */
        background-color: #F3E600;

        /* 2. Animação 'auto-progress' foi REMOVIDA para o controle via JS funcionar */
    }

    /* 3. Classes de estado para controlar a cor via JS */
    .progress-bar-inner.is-success {
        background-color: #48c774; /* Verde */
    }

    .progress-bar-inner.is-danger {
        background-color: #f14668; /* Vermelho */
    }
</style>
