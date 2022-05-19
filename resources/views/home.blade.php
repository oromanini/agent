@extends('base')
@section('content')

    <div class="container is-fluid">
        <div class="box">
            <div class="header">
                <h2 class="title is-1">Bem-vindo, agente!</h2>
                <div class="title-bottom-line"></div>
            </div>
            <div class="body">
                <div class="columns">
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
                    <div class="column is-4"></div>
                </div>
                <div class="columns mt50">
                    <h3 class="title">Por onde você quer começar?</h3>
                </div>
                <div class="columns mt50 mb20">
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
            </div>
        </div>
    </div>

@endsection
