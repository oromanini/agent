@extends('base')
@section('content')

    <div class="container is-fluid">
        <div class="box">
            <div class="header">
                <h2 class="title is-1">Bem-vindo, {{auth()->user()->name}}!</h2>
                <div class="title-bottom-line"></div>
            </div>
            <div class="body">
                <div class="columns mt50">
                    <img src="/img/logo/alluz-icon.png" width="40" alt=""><h3 class="title">Por onde você quer começar?</h3>
                </div>
                <div class="columns mt50 mb50" id="home-buttons">
                    <div class="column is-flex is-justify-content-center">
                        <a href="https://www.youtube.com/playlist?list=PL-v9iwlHmGNKo6aKbDvzoQaycqSQlYV57" class="p-red-button">
                            <ion-icon name="logo-youtube"></ion-icon>  Treinamento
                        </a>
                    </div>
                    <div class="column is-flex is-justify-content-center">
                        <a href="{{ route('client.index') }}" class="p-yellow-button">
                            <ion-icon name="people-outline"></ion-icon>  Meus clientes
                        </a>
                    </div>
                    <div class="column is-flex is-justify-content-center">
                        <a class="p-yellow-button" href="{{ route('proposal.index') }}">
                            <ion-icon name="documents-outline"></ion-icon>  Minhas propostas
                        </a>
                    </div>
                    <div class="column is-flex is-justify-content-center">
                        <a class="p-yellow-button" href="{{ route('proposal.create') }}">
                            <ion-icon name="document-outline"></ion-icon>  Nova proposta
                        </a>
                    </div>
                </div>

                <div class="columns mt50">
                    <div class="column is-4">
                        <iframe  src="https://www.youtube.com/embed/QHA3yqrzc1E"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>
                    <div class="column is-4">
                        <iframe src="https://www.youtube.com/embed/hbfBBpMcvOk"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>
                    <div class="column is-4">
                        <iframe src="https://www.youtube.com/embed/gqrX3ei6OPk"
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
