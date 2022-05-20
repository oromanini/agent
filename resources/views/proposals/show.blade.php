@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <nav class="tabs is-boxed is-fullwidth is-large" style="margin-bottom: 0">
            <div class="container">
                <ul>
                    <li class="tab is-active" onclick="openTab(event,'projeto')"><a style="color: #6b7280; font-size: 12pt">Projeto</a></li>
                    <li class="tab" onclick="openTab(event,'previstoria')"><a style="color: #6b7280; font-size: 12pt">Pré-vistoria</a></li>
                </ul>
            </div>
        </nav>
        <div class="box overflow-auto">


            <div id="projeto" class="content-tab">
                @include('proposals.show.head')
                @include('proposals.show.cards')
                @include('proposals.show.client_data')
                <br><br>
                @include('proposals.show.commission')
                <br><br>
                @include('proposals.show.discount')
                <br><br>
                @include('proposals.show.kit_data')
            </div>
            <div id="previstoria" class="content-tab" style="display:none">
                <p>aosijdiajfoiasifjdasdj</p>
            </div>
        </div>
    </div>
@endsection


<script>
    function openTab(event, tabName) {
        var i, x, tablinks;
        x = document.getElementsByClassName("content-tab");
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tab");
        for (i = 0; i < x.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" is-active", "");
        }
        document.getElementById(tabName).style.display = "block";
        event.currentTarget.className += " is-active";
    }
</script>
