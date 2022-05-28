@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <nav class="tabs is-boxed is-fullwidth is-large" style="margin-bottom: 0">
            <div class="container">
                <ul>
                    <li class="tab mytab is-active" onclick="openTab(event,'projeto')"><a style="color: #6b7280; font-size: 12pt"><ion-icon name="flash-outline"></ion-icon> Projeto</a></li>
                    <li class="tab mytab" onclick="openTab(event,'previstoria')"><a style="color: #6b7280; font-size: 12pt"><ion-icon name="camera-outline"></ion-icon> Pré-vistoria</a></li>
                </ul>
            </div>
        </nav>
        <div class="box overflow-auto">
            <div id="projeto" class="content-tab">
                @include('proposals.show.head')
                @include('proposals.show.cards')
                @include('proposals.show.client_data')
                <br><br>
                @include('proposals.show.staff')
                <br>@include('proposals.show.discount')
                <br><br>
                @include('proposals.show.commission')
                <br><br>
                @include('proposals.show.kit_data')
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



