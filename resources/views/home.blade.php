@extends('base')
@section('content')

    @php
        $authUserPermission = \Illuminate\Support\Facades\Auth::user()->permission;
        $permission = match ($authUserPermission) {
          'admin' => ['Administrador', 'is-dark'],
          'agent' => ['Agente de vendas', 'is-warning'],
          'technical' => ['Responsável Técnico(a)', 'is-link'],
          'financial' => ['Analista de financiamento', 'is-info'],
          'installer' => ['Coordenador de instalação', 'is-success'],
          'contract' => ['Gestor de contratos', 'is-danger'],
        };
    @endphp

    <style>
        .toggle-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            border-radius: .5em;
            padding: .125em;
            background-image: linear-gradient(to bottom, #d0c4b8, #f5ece5);
            box-shadow: 0 1px 1px rgb(255 255 255 / .6);
        }
        .toggle-checkbox {
            -webkit-appearance: none;
            appearance: none;
            position: absolute;
            z-index: 1;
            border-radius: inherit;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .toggle-container {
            display: flex;
            align-items: center;
            position: relative;
            border-radius: .375em;
            width: 3em;
            height: 1.5em;
            background-color: #e1dacd;
            box-shadow: inset 0 0 .0625em .125em rgb(255 255 255 / .2), inset 0 .0625em .125em rgb(0 0 0 / .4);
            transition: background-color .4s linear;
        }
        .toggle-checkbox:checked + .toggle-container {
            background-color: #f3b519;
        }
        .toggle-button {
            display: flex;
            justify-content: center;
            align-items: center;
            position: absolute;
            left: .0625em;
            border-radius: .3125em;
            width: 1.375em;
            height: 1.375em;
            background-color: #e4ddcf;
            box-shadow: inset 0 -.0625em .0625em .125em rgb(0 0 0 / .1), inset 0 -.125em .0625em rgb(0 0 0 / .2), inset 0 .1875em .0625em rgb(255 255 255 / .3), 0 .125em .125em rgb(0 0 0 / .5);
            transition: left .4s;
        }
        .toggle-checkbox:checked + .toggle-container > .toggle-button {
            left: 1.5625em;
        }
        .toggle-button-circles-container {
            display: grid;
            grid-template-columns: repeat(3, min-content);
            gap: .125em;
            position: absolute;
            margin: 0 auto;
        }
        .toggle-button-circle {
            border-radius: 50%;
            width: .125em;
            height: .125em;
            background-image: radial-gradient(circle at 50% 0, #f6f0e9, #bebcb0);
        }
    </style>

    <div class="container is-fluid">
        <div class="box">
            <div class="header">
                <div class="level">
                    <div class="level-left">
                        <h2 class="title is-1 pb-4">
                            Bem-vindo, {{auth()->user()->name}}! <br>
                            <span class="tag is-large {{ $permission[1] }}">
                                @if(auth()->user()->isAdmin) <ion-icon name="person-add-outline"></ion-icon> @endif &nbsp; {{ $permission[0] }}
                            </span>
                        </h2>
                    </div>
                    <br>
                    @if(auth()->user()->isAdmin)
                        <div class="level-right">
                            <div class="toggle-wrapper">
                                <input id="dashboard-toggle-checkbox" class="toggle-checkbox" type="checkbox">
                                <div class="toggle-container">
                                    <div class="toggle-button">
                                        <div class="toggle-button-circles-container">
                                            <div class="toggle-button-circle"></div>
                                            <div class="toggle-button-circle"></div>
                                            <div class="toggle-button-circle"></div>
                                            <div class="toggle-button-circle"></div>
                                            <div class="toggle-button-circle"></div>
                                            <div class="toggle-button-circle"></div>
                                            <div class="toggle-button-circle"></div>
                                            <div class="toggle-button-circle"></div>
                                            <div class="toggle-button-circle"></div>
                                            <div class="toggle-button-circle"></div>
                                            <div class="toggle-button-circle"></div>
                                            <div class="toggle-button-circle"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if(auth()->user()->isAdmin)
                {{-- Conteúdo da Dashboard --}}
                <div id="admin-dashboard-body">
                    @isset($dashboard)
                        <div class="body">
                            {{-- Seção de Contadores --}}
                            <div class="columns is-multiline is-centered">
                                <div class="column is-4">
                                    <div class="box has-text-centered p-card">
                                        <p class="title is-3">{{ $dashboard['proposals'] }}</p>
                                        <p class="subtitle is-6">Orçamentos únicos gerados (60 dias)</p>
                                    </div>
                                </div>
                                <div class="column is-4">
                                    <div class="box has-text-centered p-card">
                                        <p class="title is-3">R$ {{ number_format($dashboard['average_ticket'], 2, ',', '.') }}</p>
                                        <p class="subtitle is-6">Ticket Médio orçamentos gerados (60 dias)</p>
                                    </div>
                                </div>
                                <div class="column is-4">
                                    <div class="box has-text-centered p-card">
                                        <p class="title is-3">R$ {{ number_format($dashboard['total_sales'], 2, ',', '.') }}</p>
                                        <p class="subtitle is-6">Total orçado (60 dias)</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Seção de Gráficos --}}
                            <div class="columns is-multiline is-centered">
                                <div class="column is-6">
                                    <div class="box p-card">
                                        <h4 class="title is-5">Propostas: Comparativo</h4>
                                        <canvas id="proposalsComparisonChart"></canvas>
                                    </div>
                                </div>
                                <div class="column is-6">
                                    <div class="box p-card">
                                        <h4 class="title is-5">Valor de Propostas por Dia</h4>
                                        <canvas id="proposalsValueChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            {{-- Seção de Tabelas/Listagens --}}
                            <div class="columns is-centered">
                                <div class="column is-12">
                                    <div class="box p-card">
                                        <h4 class="title is-5">Propostas para Aprovação ({{ $dashboard['proposals_sent_count'] }})</h4>
                                        <div class="list-clients-wrapper" style="max-height: 200px; overflow-y: auto;">
                                            @if($dashboard['proposals_sent_clients']->isNotEmpty())
                                                <table class="table is-fullwidth is-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Cliente</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($dashboard['proposals_sent_clients'] as $clientName)
                                                        <tr>
                                                            <td>{{ $clientName }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p>Nenhuma proposta para aprovação.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="columns is-centered">
                                <div class="column is-12">
                                    <div class="box p-card">
                                        <h4 class="title is-5">Propostas Fechadas ({{ $dashboard['closed_proposals_count'] }})</h4>
                                        <div class="list-clients-wrapper" style="max-height: 200px; overflow-y: auto;">
                                            @if($dashboard['closed_proposals_clients']->isNotEmpty())
                                                <table class="table is-fullwidth is-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Cliente</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($dashboard['closed_proposals_clients'] as $clientName)
                                                        <tr>
                                                            <td>{{ $clientName }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p>Nenhuma proposta fechada.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="columns is-centered">
                                <div class="column is-12">
                                    <div class="box p-card">
                                        <h4 class="title is-5">Ranking de Agentes</h4>
                                        <div class="list-clients-wrapper" style="max-height: 200px; overflow-y: auto;">
                                            @if($dashboard['ranking_users']->isNotEmpty())
                                                <table class="table is-fullwidth is-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Agente</th>
                                                        <th>Propostas</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($dashboard['ranking_users'] as $user)
                                                        <tr>
                                                            <td>{{ $user->name }}</td>
                                                            <td>{{ $user->proposals_count }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p>Nenhum dado de ranking.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endisset
                </div>

                {{-- Conteúdo do Body Padrão --}}
                <div id="default-body" style="display: none;">
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
            @else
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
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const dashboard = @json($dashboard ?? []);
                const dashboardBody = document.getElementById('admin-dashboard-body');
                const defaultBody = document.getElementById('default-body');
                const toggleCheckbox = document.getElementById('dashboard-toggle-checkbox');
                const adminPermission = '{{ auth()->user()->isAdmin }}';

                if (adminPermission) {
                    const view = localStorage.getItem('admin-view') || 'dashboard';

                    const updateView = (currentView) => {
                        if (currentView === 'dashboard') {
                            dashboardBody.style.display = 'block';
                            defaultBody.style.display = 'none';
                            toggleCheckbox.checked = true;
                        } else {
                            dashboardBody.style.display = 'none';
                            defaultBody.style.display = 'block';
                            toggleCheckbox.checked = false;
                        }
                    };

                    updateView(view);

                    toggleCheckbox.addEventListener('change', function() {
                        const newView = this.checked ? 'dashboard' : 'default';
                        localStorage.setItem('admin-view', newView);
                        updateView(newView);
                    });
                }


                if (dashboard.comparison_chart_data) {
                    const ctxComparison = document.getElementById('proposalsComparisonChart').getContext('2d');
                    new Chart(ctxComparison, {
                        type: 'bar',
                        data: {
                            labels: dashboard.comparison_chart_data.labels,
                            datasets: [{
                                label: 'Propostas',
                                data: dashboard.comparison_chart_data.data,
                                backgroundColor: [
                                    'rgba(54, 162, 235, 0.6)',
                                    'rgba(255, 206, 86, 0.6)',
                                    'rgba(75, 192, 192, 0.6)'
                                ],
                                borderColor: [
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                }

                if (dashboard.value_by_day_chart_data) {
                    const ctxValue = document.getElementById('proposalsValueChart').getContext('2d');
                    const valueData = dashboard.value_by_day_chart_data.map(item => item.total_value);
                    const valueLabels = dashboard.value_by_day_chart_data.map(item => new Date(item.date).toLocaleDateString('pt-BR'));

                    new Chart(ctxValue, {
                        type: 'line',
                        data: {
                            labels: valueLabels,
                            datasets: [{
                                label: 'Valor de Vendas por Dia',
                                data: valueData,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                fill: true,
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                }

                if (dashboard.ranking_chart_data) {
                    const ctxRanking = document.getElementById('rankingChart').getContext('2d');
                    const rankingLabels = Object.keys(dashboard.ranking_chart_data);
                    const rankingData = Object.values(dashboard.ranking_chart_data);

                    new Chart(ctxRanking, {
                        type: 'bar',
                        data: {
                            labels: rankingLabels,
                            datasets: [{
                                label: 'Propostas Emitidas',
                                data: rankingData,
                                backgroundColor: 'rgba(153, 102, 255, 0.6)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            scales: {
                                x: { beginAtZero: true }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
