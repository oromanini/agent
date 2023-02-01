@extends('base')
@section('content')
    <div class="container is-fluid">
        <div class="box">

            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt="..">Simulador de parcelas</h3>
            </div>
            <hr>

            <iframe
                src="https://meufinanciamentosolar.com.br/iframe?token=a361e7ce-bf1a-4d8e-ae70-4bc7033e6887&origin=iframe"
                style="width:1140px;height:800px;border-style:none"></iframe>
            <div style="width:100%"><p style="text-align:center;color:#666666"> Powered by PV Operation e <a
                        href="https://meufinanciamentosolar.com.br" target="_blank" rel="noopener"
                        style="text-decoration:none;color:#666666">Meu Financiamento Solar</a></p></div>

            <hr>
            <a class="button is-primary" href="/">Voltar</a>

        </div>
    </div>
@endsection
