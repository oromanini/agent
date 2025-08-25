@extends('base')

@section('content')
    <div class="container is-fluid">
        <div class="box mt-5">
            <h3 class="title">Lista de Kits Solares</h3>

            <form action="{{ route('kits.index') }}" method="GET">
                <div class="field is-grouped mb-5">
                    {{-- Filtro de Range de KWP --}}
                    <div class="control">
                        <input class="input" type="number" step="0.01" name="min_kwp" placeholder="KWP Mínimo" value="{{ request('min_kwp') }}">
                    </div>
                    <div class="control">
                        <input class="input" type="number" step="0.01" name="max_kwp" placeholder="KWP Máximo" value="{{ request('max_kwp') }}">
                    </div>

                    {{-- Filtros de Telhado e Tensão --}}
                    <div class="control">
                        <div class="select">
                            <select name="roof_structure" id="filter-roof">
                                <option value="">Todos os Telhados</option>
                                @foreach(\App\Enums\RoofStructure::cases() as $roof)
                                    <option value="{{ $roof->value }}" {{ request('roof_structure') == $roof->value ? 'selected' : '' }}>{{ $roof->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="control">
                        <div class="select">
                            <select name="tension_pattern" id="filter-tension">
                                <option value="">Todas as Tensões</option>
                                @foreach(\App\Enums\TensionPattern::cases() as $tension)
                                    <option value="{{ $tension->value }}" {{ request('tension_pattern') == $tension->value ? 'selected' : '' }}>{{ $tension->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Botões de Filtro e Limpar --}}
                    <div class="control">
                        <button type="submit" class="button is-primary">Filtrar</button>
                    </div>
                    <div class="control">
                        <a href="{{ route('kits.index') }}" class="button is-danger">Limpar Filtros</a>
                    </div>
                </div>
            </form>

            <table class="table is-fullwidth is-striped">
                <thead>
                <tr>
                    <th>
                        <a href="{{ route('kits.index', array_merge(request()->query(), ['sort_by' => 'id', 'sort_direction' => request('sort_by') == 'id' && request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}" class="is-flex is-align-items-center">
                                <span class="order-arrows">
                                    <ion-icon name="chevron-up-outline" class="{{ request('sort_by') == 'id' && request('sort_direction') == 'asc' ? 'active' : '' }}"></ion-icon>
                                    <ion-icon name="chevron-down-outline" class="{{ request('sort_by') == 'id' && request('sort_direction') == 'desc' ? 'active' : '' }}"></ion-icon>
                                </span>
                            <span class="ml-2">ID</span>
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('kits.index', array_merge(request()->query(), ['sort_by' => 'description', 'sort_direction' => request('sort_by') == 'description' && request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}" class="is-flex is-align-items-center">
                                <span class="order-arrows">
                                    <ion-icon name="chevron-up-outline" class="{{ request('sort_by') == 'description' && request('sort_direction') == 'asc' ? 'active' : '' }}"></ion-icon>
                                    <ion-icon name="chevron-down-outline" class="{{ request('sort_by') == 'description' && request('sort_direction') == 'desc' ? 'active' : '' }}"></ion-icon>
                                </span>
                            <span class="ml-2">Descrição</span>
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('kits.index', array_merge(request()->query(), ['sort_by' => 'kwp', 'sort_direction' => request('sort_by') == 'kwp' && request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}" class="is-flex is-align-items-center">
                                <span class="order-arrows">
                                    <ion-icon name="chevron-up-outline" class="{{ request('sort_by') == 'kwp' && request('sort_direction') == 'asc' ? 'active' : '' }}"></ion-icon>
                                    <ion-icon name="chevron-down-outline" class="{{ request('sort_by') == 'kwp' && request('sort_direction') == 'desc' ? 'active' : '' }}"></ion-icon>
                                </span>
                            <span class="ml-2">KWP</span>
                        </a>
                    </th>
                    <th>Telhado</th>
                    <th>Tensão</th>
                    <th>
                        <a href="{{ route('kits.index', array_merge(request()->query(), ['sort_by' => 'cost', 'sort_direction' => request('sort_by') == 'cost' && request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}" class="is-flex is-align-items-center">
                                <span class="order-arrows">
                                    <ion-icon name="chevron-up-outline" class="{{ request('sort_by') == 'cost' && request('sort_direction') == 'asc' ? 'active' : '' }}"></ion-icon>
                                    <ion-icon name="chevron-down-outline" class="{{ request('sort_by') == 'cost' && request('sort_direction') == 'desc' ? 'active' : '' }}"></ion-icon>
                                </span>
                            <span class="ml-2">Custo</span>
                        </a>
                    </th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($kits as $kit)
                    <tr>
                        <td>{{ $kit->id }}</td>
                        <td>{{ $kit->description }}</td>
                        <td>{{ $kit->kwp }}</td>
                        <td>{{ \App\Enums\RoofStructure::from($kit->roof_structure)->name }}</td>
                        <td>{{ \App\Enums\TensionPattern::from($kit->tension_pattern)->name }}</td>
                        <td>R$ {{ number_format($kit->cost, 2, ',', '.') }}</td>
                        <td>
                            <button class="button is-info is-small show-json" data-json='@json($kit)'>Ver</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="has-text-centered">Nenhum kit encontrado.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="columns is-centered">
                <div class="column is-one-third">
                    {{ $kits->appends(request()->input())->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="json-modal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Detalhes do Kit</p>
                <button class="delete" aria-label="close"></button>
            </header>
            <section class="modal-card-body">
                <pre id="json-content"></pre>
            </section>
        </div>
    </div>

    <style>
        .order-arrows {
            display: flex;
            flex-direction: column;
            line-height: 0.5;
            position: relative;
        }

        .order-arrows ion-icon {
            font-size: 0.8rem;
            color: #b5b5b5; /* Cor padrão para ícones inativos */
        }

        .order-arrows ion-icon.active {
            color: #363636; /* Cor para ícone ativo */
        }

        .order-arrows ion-icon[name="chevron-up-outline"] {
            position: absolute;
            top: -0.2em;
        }

        .order-arrows ion-icon[name="chevron-down-outline"] {
            position: absolute;
            top: 0.2em;
        }
    </style>

    <script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const jsonModal = document.getElementById('json-modal');
            const jsonContent = document.getElementById('json-content');

            document.querySelectorAll('.show-json').forEach(button => {
                button.addEventListener('click', function () {
                    const kitData = JSON.parse(this.getAttribute('data-json'));

                    kitData.components = JSON.parse(kitData.components);
                    kitData.panel_specs = JSON.parse(kitData.panel_specs);
                    kitData.inverter_specs = JSON.parse(kitData.inverter_specs);

                    jsonContent.textContent = JSON.stringify(kitData, null, 2);
                    jsonModal.classList.add('is-active');
                });
            });

            jsonModal.querySelector('.delete, .modal-background').addEventListener('click', function () {
                jsonModal.classList.remove('is-active');
            });
        });
    </script>
@endsection
