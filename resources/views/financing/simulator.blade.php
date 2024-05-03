@extends('base')
@section('content')
    <div class="container is-fluid">
        <div class="box">

            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt="..">Simulador de parcelas</h3>
            </div>
            <hr>
            <div class="columns row">
                <div class="column is-3 is-flex is-flex-direction-column">
                    <img src="/img/simulators/mfs.png" alt="" width="300">
                    <a class="button is-info m-4" href="{{ route('simulator.mfs') }}">
                        Simular
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
