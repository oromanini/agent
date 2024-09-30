@extends('base')
@section('content')

    @php
        $authUserPermission = \Illuminate\Support\Facades\Auth::user()->permission;
        $permission = match ($authUserPermission) {
          'admin' => ['Administrador', 'is-black'],
          'agent' => ['Agente de vendas', 'is-warning'],
          'technical' => ['Responsável Técnico(a)', 'is-link'],
          'financial' => ['Analista de financiamento', 'is-info'],
          'installer' => ['Coordenador de instalação', 'is-success'],
          'contract' => ['Gestor de contratos', 'is-danger'],
        };
    @endphp

    <div class="container is-fluid">
        <div class="box">
{{--            @include('notices')--}}
            <div class="header">
                <h2 class="title is-1">Bem-vindo, {{auth()->user()->name}}! <br><span class="tag is-large {{ $permission[1] }}">{{ $permission[0] }}</span>
                </h2>
                <div class="title-bottom-line"></div>
            </div>
            <div class="body">
                <div class="columns mt50">
                    <h3 class="title">Por onde você quer começar?</h3>
                </div>
                <div class="columns mt50 mb50 is-flex is-flex-wrap-wrap is-wra" id="home-buttons">
                    <div class="column is-flex is-justify-content-center">
                        <a href="https://www.youtube.com/playlist?list=PL-v9iwlHmGNKo6aKbDvzoQaycqSQlYV57" class="p-red-button">
                            <ion-icon name="logo-youtube"></ion-icon>  Treinamento
                        </a>
                    </div>
                    <div class="column is-flex is-justify-content-center">
                        <a class="p-yellow-button" href="{{ route('proposal.create') }}">
                            <ion-icon name="document-outline"></ion-icon>  Nova proposta
                        </a>
                    </div>
                    <div class="column is-flex is-justify-content-center">
                        <a href="{{ route('simulator.index') }}" class="p-yellow-button">
                            <ion-icon name="cash-outline"></ion-icon>  Simulador
                        </a>
                    </div>
                    <div class="column is-flex is-justify-content-center">
                        <a href="{{ route('client.index') }}" class="p-yellow-button">
                            <ion-icon name="people-outline"></ion-icon> clientes
                        </a>
                    </div>
                    <div class="column is-flex is-justify-content-center">
                        <a class="p-yellow-button" href="{{ route('proposal.index') }}">
                            <ion-icon name="documents-outline"></ion-icon> Propostas
                        </a>
                    </div>

                </div>

                <div class="columns mt50">
                    <div class="column is-4">
                        <iframe  src="https://www.youtube.com/embed/d1rXQf4FNR8?si=fiQ6YuJOnHlcsVdf"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>
                    <div class="column is-4">
                        <iframe src="https://www.youtube.com/embed/lAisaTltY3E?si=O6KXrQQ2nWu7Ucrg"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>
                    <div class="column is-4">
                        <iframe src="https://www.youtube.com/embed/4XDZLUKwZsc?si=DQ6R-F8dlvBcTIM_"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>
                    <div class="column is-4"></div>
                </div>
            </div>
        </div>
    </div>

@endsection
