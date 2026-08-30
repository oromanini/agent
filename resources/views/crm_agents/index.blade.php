@extends('base')

@section('content')
    <style>
        .crm-filters,
        .crm-new-lead-form { margin-bottom: 0; }

        .crm-kanban {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(240px, 240px);
            gap: 14px;
            overflow-x: auto;
            padding: 6px 2px 12px;
        }

        .crm-column {
            min-height: 320px;
            background: #F3EEE4;
            border: 1px solid #EFE9DA;
            border-radius: 16px;
            padding: 14px !important;
            box-shadow: none !important;
            margin-bottom: 0 !important;
        }
        .crm-column .title.is-6 { font-size: .8rem; font-weight: 700 !important; color: #4A473E; margin-bottom: 12px; }

        .crm-card {
            background: #FFFFFF !important;
            border: 1px solid #EFE9DA !important;
            border-radius: 12px !important;
            box-shadow: 0 1px 2px rgba(33, 29, 20, 0.06) !important;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .crm-card .card-content { min-width: 0; word-break: break-word; padding: 12px; }
        .crm-card strong { color: #211F1A; font-size: .85rem; }
        .crm-card p { color: #8A8578; font-size: .78rem; overflow-wrap: anywhere; }
        .crm-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -8px rgba(33, 29, 20, 0.15) !important; }

        .crm-lead-modal .tabs li.is-active a { color: #B9740A; border-bottom-color: #B9740A; }
        .crm-new-lead-card { margin-top: 16px; }
    </style>

    <div class="a-page-head">
        <h1>CRM Agentes</h1>
    </div>

    <div class="a-card" style="margin-bottom:20px;">

        <form method="get" action="{{ route('crm-agentes.index') }}" class="columns is-multiline crm-filters">
            <div class="column is-3">
                <label class="label">Nome</label>
                <input class="input" name="name" value="{{ request('name') }}" placeholder="Nome do candidato">
            </div>
            <div class="column is-3">
                <label class="label">Telefone</label>
                <input class="input js-phone-mask" name="phone_number" value="{{ request('phone_number') }}" placeholder="Telefone" maxlength="15" inputmode="numeric">
            </div>
            <div class="column is-2">
                <label class="label">Cadastro de</label>
                <input class="input" type="date" name="registered_from" value="{{ request('registered_from') }}">
            </div>
            <div class="column is-2">
                <label class="label">Cadastro até</label>
                <input class="input" type="date" name="registered_to" value="{{ request('registered_to') }}">
            </div>
            <div class="column is-2 is-flex is-align-items-flex-end">
                <button class="button is-info mr-2" type="submit">Filtrar</button>
                <a class="button is-light" href="{{ route('crm-agentes.index') }}">Limpar</a>
            </div>
        </form>

        <hr>

        <form method="post" action="{{ route('crm-agentes.store') }}" class="columns is-multiline crm-new-lead-form">
            @csrf
            <div class="column is-4">
                <input class="input" name="name" placeholder="Nome" required>
            </div>
            <div class="column is-3">
                <input class="input" name="email" placeholder="Email">
            </div>
            <div class="column is-3">
                <input class="input js-phone-mask" name="phone_number" placeholder="Telefone" maxlength="15" inputmode="numeric">
            </div>
            <div class="column is-2">
                <button class="button is-primary is-fullwidth" type="submit">Novo candidato</button>
            </div>
        </form>
    </div>

    @php
        $statuses = [
            'novo',
            'em_atendimento',
            'aguardando_resposta',
            'confeccao_de_contrato',
            'contrato_assinado',
            'desistencia',
        ];
    @endphp

    <div class="crm-kanban">
        @foreach($statuses as $status)
            <div class="box crm-column" data-status="{{ $status }}">
                <h2 class="title is-6">{{ $statusLabels[$status] }}</h2>

                @foreach($leads->where('status', $status) as $lead)
                    <div class="card crm-card mb-3" draggable="true" data-lead-id="{{ $lead->id }}"
                         data-name="{{ $lead->name }}"
                         data-email="{{ $lead->email }}"
                         data-phone="{{ $lead->phone_number }}"
                         data-created="{{ $lead->created_at->format('d/m/Y H:i') }}"
                         data-password="{{ $lead->generated_password }}"
                         data-user="{{ optional($lead->user)->name }}"
                         data-user-id="{{ $lead->user_id }}"
                    >
                        <div class="card-content">
                            <p><strong>{{ $lead->name }}</strong></p>
                            <p>{{ $lead->email ?: '-' }}</p>
                            <p>{{ $lead->phone_number ? preg_replace('/^(\d{2})(\d{4,5})(\d{4})$/', '($1) $2-$3', preg_replace('/\D/', '', $lead->phone_number)) : '-' }}</p>
                            <button class="button is-small is-link mt-2 open-lead-modal" type="button" data-target="lead-modal-{{ $lead->id }}">Detalhes</button>
                        </div>
                    </div>

                    <div class="modal crm-lead-modal" id="lead-modal-{{ $lead->id }}">
                        <div class="modal-background close-modal"></div>
                        <div class="modal-card" style="width:700px;max-width:95%;">
                            <header class="modal-card-head">
                                <p class="modal-card-title">{{ $lead->name }}</p>
                                <button class="delete close-modal" type="button"></button>
                            </header>
                            <section class="modal-card-body">
                                <div class="tabs is-boxed">
                                    <ul>
                                        <li class="is-active"><a>Dados</a></li>
                                    </ul>
                                </div>
                                <p><strong>Email:</strong> {{ $lead->email ?: '-' }}</p>
                                <p><strong>Telefone:</strong> {{ $lead->phone_number ? preg_replace('/^(\d{2})(\d{4,5})(\d{4})$/', '($1) $2-$3', preg_replace('/\D/', '', $lead->phone_number)) : '-' }}</p>
                                <p><strong>Data cadastro:</strong> {{ $lead->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Usuário criado:</strong> {{ optional($lead->user)->name ?: 'Ainda não cadastrado' }}</p>
                                @if($lead->generated_password)
                                    <div class="mt-2">
                                        <strong>Senha gerada:</strong>
                                        <span id="pwd-hidden-{{ $lead->id }}">••••••••••</span>
                                        <span id="pwd-show-{{ $lead->id }}" style="display:none">{{ $lead->generated_password }}</span>
                                        <button class="button is-small is-light" type="button" onclick="togglePassword({{ $lead->id }})">
                                            <ion-icon name="eye-outline"></ion-icon>
                                        </button>
                                    </div>
                                @endif

                                <hr>
                                <h3 class="title is-6">Adicionar interação</h3>
                                <form method="post" action="{{ route('crm-agentes.interactions.store', $lead->id) }}">
                                    @csrf
                                    <input type="hidden" name="name" value="{{ request('name') }}">
                                    <input type="hidden" name="phone_number" value="{{ request('phone_number') }}">
                                    <input type="hidden" name="registered_from" value="{{ request('registered_from') }}">
                                    <input type="hidden" name="registered_to" value="{{ request('registered_to') }}">
                                    <textarea class="textarea" name="content" required></textarea>
                                    <button class="button is-info mt-2" type="submit">Salvar interação</button>
                                </form>

                                <hr>
                                <h3 class="title is-6">Histórico</h3>
                                @forelse($lead->interactions as $interaction)
                                    <article class="message is-light">
                                        <div class="message-body">
                                            <small>{{ $interaction->created_at->format('d/m/Y H:i') }} - {{ optional($interaction->user)->name ?: 'Sistema' }}</small>
                                            <p>{{ $interaction->content }}</p>
                                        </div>
                                    </article>
                                @empty
                                    <p>Sem interações registradas.</p>
                                @endforelse
                            </section>
                            <footer class="modal-card-foot">
                                @if(!$lead->user_id)
                                    <form method="post" action="{{ route('crm-agentes.register-agent', $lead->id) }}">
                                        @csrf
                                        <button class="button is-success" type="submit">Cadastrar no sistema</button>
                                    </form>
                                @endif
                                <button class="button close-modal" type="button">Fechar</button>
                            </footer>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.open-lead-modal').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById(button.dataset.target).classList.add('is-active');
            });
        });

        document.querySelectorAll('.close-modal').forEach((button) => {
            button.addEventListener('click', () => {
                button.closest('.modal').classList.remove('is-active');
            });
        });

        const cards = document.querySelectorAll('.crm-card');
        const columns = document.querySelectorAll('.crm-column');

        cards.forEach((card) => {
            card.addEventListener('dragstart', (event) => {
                event.dataTransfer.setData('leadId', card.dataset.leadId);
            });
        });

        columns.forEach((column) => {
            column.addEventListener('dragover', (event) => event.preventDefault());
            column.addEventListener('drop', (event) => {
                event.preventDefault();
                const leadId = event.dataTransfer.getData('leadId');
                if (!leadId) return;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/crm-agentes/${leadId}/status`;

                const csrf = document.createElement('input');
                csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                const method = document.createElement('input');
                method.type = 'hidden'; method.name = '_method'; method.value = 'PUT';
                form.appendChild(method);

                const status = document.createElement('input');
                status.type = 'hidden'; status.name = 'status'; status.value = column.dataset.status;
                form.appendChild(status);

                document.body.appendChild(form);
                form.submit();
            });
        });

        function togglePassword(leadId) {
            const hidden = document.getElementById(`pwd-hidden-${leadId}`);
            const visible = document.getElementById(`pwd-show-${leadId}`);
            const showing = visible.style.display === 'inline';
            visible.style.display = showing ? 'none' : 'inline';
            hidden.style.display = showing ? 'inline' : 'none';
        }

        const applyPhoneMask = (value) => {
            const digits = value.replace(/\D/g, '').slice(0, 11);
            if (digits.length <= 2) return digits;
            if (digits.length <= 6) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
            if (digits.length <= 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
            return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
        };

        document.querySelectorAll('.js-phone-mask').forEach((input) => {
            input.addEventListener('input', () => {
                input.value = applyPhoneMask(input.value);
            });

            if (input.value) {
                input.value = applyPhoneMask(input.value);
            }
        });
    </script>
@endpush
