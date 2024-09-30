<div id="loader">
    <img src="/img/loader.gif" width="70" style="z-index: 10;background-color: #fff; border-radius: 100%">
    <span style="color: #fff; font-size: 24pt; margin-top: 20px">Carregando, aguarde ...</span>
</div>


<style>
    #loader {
        width: 100%;
        height: 100%;
        background: rgb(255,213,0);
        background: linear-gradient(30deg, rgba(255,213,0,1) 0%, rgb(255, 177, 0) 35%, rgba(255,222,0,1) 100%);
        border-right: 5px solid $alluz-white;
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
