@extends('base')
@section('content')

    @php
        $authUserPermission = \Illuminate\Support\Facades\Auth::user()->permission;
        $permission = match ($authUserPermission) {
          'admin' => ['Administrador'],
          'agent' => ['Agente de vendas'],
          'technical' => ['Responsável técnico(a)'],
          'financial' => ['Analista de financiamento'],
          'installer' => ['Coordenador de instalação'],
          'contract' => ['Gestor de contratos'],
          default => ['Usuário'],
        };
    @endphp

    <style>
        .dash-kpis { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px; }
        .dash-2col { display: grid; grid-template-columns: 1.2fr 1fr; gap: 16px; margin-bottom: 20px; }
        .dash-ranking-row { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
        .dash-ranking-row .top { display: flex; justify-content: space-between; font-size: 13px; }
        .dash-list { display: flex; flex-direction: column; gap: 10px; max-height: 240px; overflow-y: auto; }
        .dash-list .row { display: flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 500; }
        .quick-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin: 18px 0 26px; }
        .home-toggle-row { display: flex; align-items: center; gap: 10px; }
        @media (max-width: 900px) {
            .dash-kpis, .dash-2col, .quick-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="container is-fluid">

        <div class="a-page-head">
            <div>
                <h1>Bem-vindo, {{ auth()->user()->name }}</h1>
                <p>{{ $permission[0] }}</p>
            </div>
            @if(auth()->user()->isAdmin)
                <label class="home-toggle-row" style="cursor:pointer; font-size:13px; font-weight:600; color:#8A8578;">
                    <input id="dashboard-toggle-checkbox" type="checkbox" style="width:16px; height:16px;">
                    Ver métricas
                </label>
            @endif
        </div>

        @if(auth()->user()->isAdmin)
            {{-- Dashboard --}}
            <div id="admin-dashboard-body">
                @isset($dashboard)
                    <div class="dash-kpis">
                        <div class="a-kpi a-kpi--accent">
                            <div class="a-kpi__value">{{ $dashboard['proposals'] }}</div>
                            <div class="a-kpi__label">Orçamentos únicos gerados</div>
                            <div class="a-kpi__meta">últimos 60 dias</div>
                        </div>
                        <div class="a-kpi">
                            <div class="a-kpi__value">R$ {{ number_format($dashboard['average_ticket'], 2, ',', '.') }}</div>
                            <div class="a-kpi__label">Ticket médio dos orçamentos</div>
                            <div class="a-kpi__meta">últimos 60 dias</div>
                        </div>
                        <div class="a-kpi">
                            <div class="a-kpi__value">R$ {{ number_format($dashboard['total_sales'], 2, ',', '.') }}</div>
                            <div class="a-kpi__label">Total orçado</div>
                            <div class="a-kpi__meta">últimos 60 dias</div>
                        </div>
                    </div>

                    <div class="dash-2col">
                        <div class="a-card">
                            <div style="font-size:15px; font-weight:700; margin-bottom:16px;">Propostas: comparativo mensal</div>
                            <canvas id="proposalsComparisonChart" height="140"></canvas>
                        </div>
                        <div class="a-card">
                            <div style="font-size:15px; font-weight:700; margin-bottom:16px;">Ranking de agentes</div>
                            @forelse($dashboard['ranking_users'] as $user)
                                @php
                                    $max = max(1, $dashboard['ranking_users']->max('proposals_count'));
                                    $pct = round(($user->proposals_count / $max) * 100);
                                @endphp
                                <div class="dash-ranking-row">
                                    <div class="top">
                                        <span style="font-weight:600;">{{ $user->name }}</span>
                                        <span style="color:#B4AC98;">{{ $user->proposals_count }} propostas</span>
                                    </div>
                                    <div class="a-bar"><span style="width: {{ $pct }}%;"></span></div>
                                </div>
                            @empty
                                <p style="color:#8A8578;">Nenhum dado de ranking.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="a-card" style="margin-bottom:16px;">
                        <div style="font-size:15px; font-weight:700; margin-bottom:14px;">
                            Valor de propostas por dia
                        </div>
                        <canvas id="proposalsValueChart" height="120"></canvas>
                    </div>

                    <div class="dash-2col" style="grid-template-columns:1fr 1fr;">
                        <div class="a-card">
                            <div style="font-size:15px; font-weight:700; margin-bottom:14px;">
                                Propostas para aprovação ({{ $dashboard['proposals_sent_count'] }})
                            </div>
                            <div class="dash-list">
                                @forelse($dashboard['proposals_sent_clients'] as $clientName)
                                    <div class="row"><x-avatar :name="$clientName" /> {{ $clientName }}</div>
                                @empty
                                    <p style="color:#8A8578;">Nenhuma proposta para aprovação.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="a-card">
                            <div style="font-size:15px; font-weight:700; margin-bottom:14px;">
                                Propostas fechadas ({{ $dashboard['closed_proposals_count'] }})
                            </div>
                            <div class="dash-list">
                                @forelse($dashboard['closed_proposals_clients'] as $clientName)
                                    <div class="row"><x-avatar :name="$clientName" /> {{ $clientName }}</div>
                                @empty
                                    <p style="color:#8A8578;">Nenhuma proposta fechada.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endisset
            </div>

            {{-- Default body --}}
            <div id="default-body" style="display: none;">
                @include('partials.home-quickstart')
            </div>
        @else
            @include('partials.home-quickstart')
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dashboard = @json($dashboard ?? []);
                const dashboardBody = document.getElementById('admin-dashboard-body');
                const defaultBody = document.getElementById('default-body');
                const toggleCheckbox = document.getElementById('dashboard-toggle-checkbox');
                const adminPermission = '{{ auth()->user()->isAdmin }}';

                if (adminPermission && dashboardBody && defaultBody && toggleCheckbox) {
                    const view = localStorage.getItem('admin-view') || 'dashboard';
                    const updateView = (currentView) => {
                        const isDash = currentView === 'dashboard';
                        dashboardBody.style.display = isDash ? 'block' : 'none';
                        defaultBody.style.display = isDash ? 'none' : 'block';
                        toggleCheckbox.checked = isDash;
                    };
                    updateView(view);
                    toggleCheckbox.addEventListener('change', function () {
                        const newView = this.checked ? 'dashboard' : 'default';
                        localStorage.setItem('admin-view', newView);
                        updateView(newView);
                    });
                }

                const AMBER = '#F5B942';
                const AMBER_SOFT = 'rgba(245, 185, 66, 0.18)';
                const GRID = 'rgba(33, 29, 20, 0.06)';
                const TICK = '#8A8578';

                if (dashboard.comparison_chart_data && document.getElementById('proposalsComparisonChart')) {
                    new Chart(document.getElementById('proposalsComparisonChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: dashboard.comparison_chart_data.labels,
                            datasets: [{
                                label: 'Propostas',
                                data: dashboard.comparison_chart_data.data,
                                backgroundColor: AMBER,
                                borderRadius: 8,
                                maxBarThickness: 52
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, grid: { color: GRID }, ticks: { color: TICK } },
                                x: { grid: { display: false }, ticks: { color: TICK } }
                            }
                        }
                    });
                }

                if (dashboard.value_by_day_chart_data && document.getElementById('proposalsValueChart')) {
                    const valueData = dashboard.value_by_day_chart_data.map(item => item.total_value);
                    const valueLabels = dashboard.value_by_day_chart_data.map(item => new Date(item.date).toLocaleDateString('pt-BR'));
                    new Chart(document.getElementById('proposalsValueChart').getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: valueLabels,
                            datasets: [{
                                label: 'Valor por dia',
                                data: valueData,
                                borderColor: AMBER,
                                backgroundColor: AMBER_SOFT,
                                fill: true,
                                tension: 0.25,
                                pointRadius: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, grid: { color: GRID }, ticks: { color: TICK } },
                                x: { grid: { display: false }, ticks: { color: TICK } }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
