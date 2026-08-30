@extends('base')

@section('content')
    <style>
        nav.tabs.a-tabs { margin-bottom: 20px; }
        nav.tabs.a-tabs ul { display: inline-flex; background: #F3EEE4; border-radius: 12px; padding: 3px; gap: 2px; border: 0; }
        nav.tabs.a-tabs li { margin: 0; }
        nav.tabs.a-tabs li a { border: 0 !important; background: transparent; color: #8A8578 !important; padding: .5rem 1.2rem; border-radius: 9px; font-size: .82rem !important; font-weight: 700; }
        nav.tabs.a-tabs li.is-active a { background: #FFFFFF; color: #211F1A !important; box-shadow: 0 1px 3px rgba(33,29,20,.15); }
    </style>

    <div class="container is-fluid overflow-auto">

        <div class="a-page-head">
            <h1>Instalação — Proposta #{{ $installation->proposal->id }}</h1>
            <a class="a-btn-ghost" href="{{ route('installation.index') }}">Voltar</a>
        </div>
        <p style="margin:-0.8rem 0 1rem; color:#8A8578; font-size:0.9rem;">{{ $installation->proposal->client->name }}</p>

        <div style="display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap;">
            <span class="a-pill a-pill--blue">Status: {{ $installation->status->name }}</span>
            <span class="a-pill a-pill--amber">Aguardando entrega</span>
        </div>

        <nav class="tabs a-tabs">
            <ul>
                <li class="mytab is-active" onclick="openTab(event,'instalacao')"><a><ion-icon name="build-outline"></ion-icon> Instalação</a></li>
                <li class="mytab" onclick="openTab(event,'fotos')"><a><ion-icon name="camera-outline"></ion-icon> Fotos</a></li>
                <li class="mytab" onclick="openTab(event,'custos')"><a><ion-icon name="cash-outline"></ion-icon> Custos adicionais</a></li>
            </ul>
        </nav>
        <div class="overflow-auto">

            <div id="instalacao" class="content-tab">
                @include('installation.tabs.general')
            </div>
            <div id="fotos" class="content-tab">
                @include('installation.tabs.images')
            </div>
            <div id="custos" class="content-tab">
                @include('installation.tabs.plusCosts')
            </div>

        </div>
    </div>

    <script>
        function openTab(event, tabName) {
            var i, x, tablinks;
            x = document.getElementsByClassName("content-tab");
            for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("mytab");
            for (i = 0; i < x.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" is-active", "");
            }
            document.getElementById(tabName).style.display = "block";
            event.currentTarget.className += " is-active";
        }

        function checkHash(path) {
            if (path.search('#installation') !== -1) {
                openTab(event, 'instalacao')

            } else if (path.search('#images') !== -1) {
                openTab(event, 'fotos')

            } else if (path.search('#costs') !== -1) {
                openTab(event, 'custos')
            } else {

            }
        }

        $(function () {
            let path = window.location.href;
            checkHash(path)
        })
    </script>
@endsection
