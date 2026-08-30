<div id="loader">
    <div class="loader-spinner" aria-label="Carregando"></div>
    <span class="loader-text">Carregando, aguarde ...</span>
</div>

<style>
    #loader {
        width: 100%;
        height: 100%;
        background: #F7F3EC;
        z-index: 50;
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
        border: 5px solid rgba(245, 185, 66, 0.25);
        border-top-color: #F5B942;
        animation: spin 0.8s linear infinite;
    }

    .loader-text {
        color: #8A8578;
        font-size: 16pt;
        margin-top: 20px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
