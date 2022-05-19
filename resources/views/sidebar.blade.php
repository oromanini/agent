<div class="side-navigation">

    <div class="side-logo">
        <img src="/img/logo/alluz-icon.png" class="side-logo-img" alt="...">
    </div>

    <ul>

        <li class="side-list side-active">
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
            <a href="#">
                <span class="side-icon">
                    <ion-icon name="link-outline"></ion-icon>
                </span>
                <span class="side-title">Links</span>
            </a>
        </li>

        @if(auth()->user()->is_admin)
            <li class="side-list">
                <a href="{{ route('proposal.manual.create') }}">
                <span class="side-icon">
                    <ion-icon name="star-outline"></ion-icon>
                </span>
                    <span class="side-title">Proposta Manual</span>
                </a>
            </li>
        @endif

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


