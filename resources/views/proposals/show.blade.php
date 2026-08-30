@extends('base')

@section('content')
    <style>
        nav.tabs.proposal-tabs { margin-bottom: 20px; }
        nav.tabs.proposal-tabs ul {
            display: inline-flex; background: #F3EEE4; border-radius: 12px; padding: 3px; gap: 2px; border: 0;
        }
        nav.tabs.proposal-tabs li { margin: 0; }
        nav.tabs.proposal-tabs li a {
            border: 0 !important; background: transparent; color: #8A8578 !important;
            padding: .5rem 1.25rem; border-radius: 9px; font-size: .82rem !important; font-weight: 700;
        }
        nav.tabs.proposal-tabs li.is-active a {
            background: #FFFFFF; color: #211F1A !important; box-shadow: 0 1px 3px rgba(33,29,20,.15);
        }
    </style>

    <div class="container is-fluid overflow-auto">
        <nav class="tabs proposal-tabs">
            <ul>
                <li class="mytab is-active" onclick="openTab(event,'projeto')">
                    <a><ion-icon name="flash-outline"></ion-icon> Projeto</a>
                </li>
                <li class="mytab" onclick="openTab(event,'previstoria')">
                    <a><ion-icon name="camera-outline"></ion-icon> Pré-vistoria</a>
                </li>
            </ul>
        </nav>
        <div class="overflow-auto">
            <div id="projeto" class="content-tab">
                @include('proposals.show.head')
                @include('proposals.show.cards')
                @include('proposals.show.client_data')

                @if(!is_null($proposal->send_data))
                    @include('proposals.statuses')
                @endif
                <br><br>
                @include('proposals.show.kit_data')
                <br>
                @include('proposals.show.staff')
                <br>
                @include('proposals.show.discount')
                <br><br>
                @include('proposals.show.commission')
                <br><br>
                @include('proposals.show.card_commission')
            </div>
            <div id="previstoria" class="content-tab" style="display:none">
                @include('pre_inspection.index')
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
    </script>
@endsection



