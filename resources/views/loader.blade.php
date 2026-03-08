<div id="loader">
    <img src="/img/loader.gif" width="70" style="z-index: 10; background-color: #111723; border-radius: 100%; padding: 8px; border: 1px solid #2d3750;">
    <span style="color: #e8edf7; font-size: 24pt; margin-top: 20px">Carregando, aguarde ...</span>
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
        transition: width 1s ease-out;
    }
</style>
