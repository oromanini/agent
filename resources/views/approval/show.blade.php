@extends('base')

@section('content')
        <div class="container is-fluid overflow-auto">
            <nav class="tabs is-boxed is-fullwidth is-large" style="margin-bottom: 0">
                <div class="container">
                    <ul>
                        <li id="project-li" class="mytab is-active" onclick="openTab(event,'projeto')"><a
                                style="color: #6b7280; font-size: 12pt">
                                <ion-icon name="flash-outline"></ion-icon>
                                Projeto</a></li>
                        <li id="technical-li" class="mytab" onclick="openTab(event,'vistoria')"><a
                                style="color: #6b7280; font-size: 12pt">
                                <ion-icon name="camera-outline"></ion-icon>
                                Vistoria</a></li>
                        <li id="financing-li" class="mytab" onclick="openTab(event,'financiamento')"><a
                                style="color: #6b7280; font-size: 12pt">
                                <ion-icon name="card-outline"></ion-icon>
                                Financiamento</a></li>
                        <li id="contract-li" class="mytab" onclick="openTab(event,'contrato')"><a
                                style="color: #6b7280; font-size: 12pt">
                                <ion-icon name="document-text-outline"></ion-icon>
                                Contrato</a></li>
                    </ul>
                </div>
            </nav>
            <div class="box overflow-auto">
                <div id="projeto" class="content-tab">
                    @include('approval.tabs.proposal')
                </div>
                <div id="vistoria" class="content-tab" style="display:none">
                    @include('approval.tabs.technical')
                </div>
                <div id="financiamento" class="content-tab" style="display:none">
                    @include('approval.tabs.financing')
                </div>
                <div id="contrato" class="content-tab" style="display:none">
                    @include('approval.tabs.contract')
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
                if (path.search('#technical') !== -1) {
                    openTab(event, 'vistoria')
                    $('#technical-li').addClass("is-active")

                } else if (path.search('#project') !== -1) {
                    openTab(event, 'projeto')
                    $('#project-li').addClass("is-active")
                    console.log('oiu')

                } else if (path.search('#financing') !== -1) {
                    openTab(event, 'financiamento')
                    $('#financing-li').addClass("is-active")

                } else if (path.search('#contract') !== -1) {
                    openTab(event, 'contrato')
                    $('#contract-li').addClass("is-active")
                }
            }

            $(function () {
                let path = window.location.href;
                checkHash(path)
            })
        </script>
@endsection
