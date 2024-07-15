@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <nav class="tabs is-boxed is-fullwidth is-large" style="margin-bottom: 0">
            <div class="container">
                <ul>
                    <li class=" mytab is-active" onclick="openTab(event,'instalacao')"><a
                            style="color: #6b7280; font-size: 12pt">
                            <ion-icon name="build-outline"></ion-icon>
                            Instalação</a></li>
                    <li class=" mytab" onclick="openTab(event,'fotos')"><a
                            style="color: #6b7280; font-size: 12pt">
                            <ion-icon name="camera-outline"></ion-icon>
                            Fotos</a></li>
                    <li class=" mytab" onclick="openTab(event,'custos')"><a
                            style="color: #6b7280; font-size: 12pt">
                            <ion-icon name="cash-outline"></ion-icon>
                            Custos Adicionais</a></li>
                </ul>
            </div>
        </nav>
        <div class="box overflow-auto">
            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Instalação</h3>
            </div>
            <br>
            <div class="columns">
                <div class="column">
                    <span class="tag is-info is-light" style="font-size: 16pt">
                        <span class="icon"></span>{{ 'Proposta #' . $installation->proposal->id . ' - ' .$installation->proposal->client->name }}
                    </span>
                    <span class="tag is-info" style="font-size: 16pt">
                        <span class="icon"><ion-icon name="pie-chart-outline"></ion-icon></span> &nbsp;&nbsp;{{ 'Status: ' . $installation->status->name }}
                    </span>
                    <span class="tag is-warning" style="font-size: 16pt">
{{--                        TODO: Vinculate with delivery--}}
                        <span class="icon"><ion-icon name="warning-outline"></ion-icon></span> &nbsp;&nbsp;{{ 'Aguardando entrega' }}
                    </span>
                </div>
            </div>
            <br>

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
