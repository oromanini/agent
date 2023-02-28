<div class="side-navigation">

    <div class="side-logo">
        <img src="/img/logo/alluz-icon.png" class="side-logo-img" alt="...">
    </div>

    <ul>

        <li class="side-list" id="side-home">
            <a href="{{ route('home') }}">
                <span class="side-icon">
                    <ion-icon name="home-outline"></ion-icon>
                </span>
                <span class="side-title">Início</span>
            </a>
        </li>

        <li class="side-list">
            <a href="{{ route('client.index') }}">
                <span class="side-icon">
                    <ion-icon name="people-outline"></ion-icon>
                </span>
                <span class="side-title">Clientes</span>
            </a>
        </li>

        <li class="side-list">
            <a href="{{ route('proposal.create') }}">
                <span class="side-icon">
                    <ion-icon name="document-outline"></ion-icon>
                </span>
                <span class="side-title">Nova Proposta</span>
            </a>
        </li>

        <li class="side-list">
            <a href="{{ route('proposal.index') }}">
                <span class="side-icon">
                    <ion-icon name="documents-outline"></ion-icon>
                </span>
                <span class="side-title">Minhas propostas</span>
            </a>
        </li>

        <li class="side-list">
            <a href="{{ route('simulator.index') }}">
                <span class="side-icon">
                    <ion-icon name="wallet-outline"></ion-icon>
                </span>
                <span class="side-title">Simular financiamento</span>
            </a>
        </li>

        <li class="side-list">
            <a href="#">
                <span class="side-icon">
                    <ion-icon name="link-outline"></ion-icon>
                </span>
                <span class="side-title">Links</span>
            </a>
        </li>

        @if(!is_null(auth()->user()) && auth()->user()->is_admin)
            <hr style="margin-bottom: 2px !important;">
            <li class="side-list">
                <a href="{{ route('approval.index') }}">
                <span class="side-icon">
                    <ion-icon name="thumbs-up-outline"></ion-icon>
                </span>
                    <span class="side-title">Aprovações</span>
                </a>
            </li>

            <li class="side-list">
                <a href="{{ route('homologation.index') }}">
                <span class="side-icon">
                   <ion-icon name="ribbon-outline"></ion-icon>
                </span>
                    <span class="side-title">Homologação</span>
                </a>
            </li>
            <li class="side-list">
                <a href="{{ route('installation.index') }}">
                <span class="side-icon">
                    <ion-icon name="build-outline"></ion-icon>
                </span>
                    <span class="side-title">Instalação</span>
                </a>
            </li>

            <hr style="margin-bottom: 2px !important;">

            <li class="side-list">
                <a href="{{ route('proposal.manual.create') }}">
                <span class="side-icon">
                    <ion-icon name="star-outline"></ion-icon>
                </span>
                    <span class="side-title">Proposta Manual</span>
                </a>
            </li>

            <li class="side-list">
                <a href="{{ route('user.index') }}">
                <span class="side-icon">
                    <ion-icon name="briefcase-outline"></ion-icon>
                </span>
                    <span class="side-title">Agentes</span>
                </a>
            </li>
        @endif
        <hr style="margin-top: 2px !important;">
        <li class="side-list">
            <a href="{{ route('logout') }}">
                <span class="side-icon">
                    <ion-icon name="log-out-outline"></ion-icon>
                </span>
                <span class="side-title">Sair</span>
            </a>
        </li>

    </ul>
</div>


<section class="top-nav mob-navigation">
    <div>
        <a href="/"><img src="/img/logo/mob-logo.png" width="150" alt=""></a>
    </div>
    <input id="menu-toggle" type="checkbox" />
    <label class='menu-button-container' for="menu-toggle">
        <div class='menu-button'></div>
    </label>
    <ul class="menu">
        <li><a style="color: #fff" href="/">Home</a></li>
        <li><a style="color: #fff" href="{{ route('proposal.create') }}">Novo orçamento</a></li>
        <li><a style="color: #fff" href="{{ route('proposal.index') }}">Meus Orçamentos</a></li>
        <li><a style="color: #fff" href="{{ route('client.create') }}">Novo cliente</a></li>
        <li><a style="color: #fff" href="{{ route('client.index') }}">Meus Clientes</a></li>
        <li><a style="color: #fff" href="/logout">Sair</a></li>
    </ul>
</section>
<br>

