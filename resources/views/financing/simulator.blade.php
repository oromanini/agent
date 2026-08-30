@extends('base')
@section('content')
    <div class="container is-fluid">

        <div class="a-page-head">
            <h1>Simulador de parcelas</h1>
        </div>

        <div class="a-card" style="max-width:360px; display:flex; flex-direction:column; align-items:flex-start; gap:16px;">
            <img src="/img/simulators/mfs.png" alt="MFS" width="260" style="max-width:100%;">
            <a class="a-btn-primary" href="{{ route('simulator.mfs') }}">Simular</a>
        </div>
    </div>
@endsection
