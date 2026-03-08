<div id="loader">
    <div class="loader-spinner" aria-label="Carregando"></div>
    <span class="loader-text">Carregando, aguarde ...</span>
</div>

<style>
    #loader {
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at top, #0b1020 0%, #06080f 35%, #03050a 100%);
        z-index: 10;
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        transition: opacity 0.3s ease;
    }

    .loader-spinner {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        border: 5px solid rgba(255, 188, 14, 0.25);
        border-top-color: #ffbc0e;
        animation: spin 0.8s linear infinite;
    }

    .loader-text {
        color: #e8edf7;
        font-size: 18pt;
        margin-top: 20px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
