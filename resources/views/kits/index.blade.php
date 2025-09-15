@extends('base')

@section('content')
    <div class="container is-fluid">
        <div class="box mt-5">
            <h3 class="title">Lista de Kits Solares</h3>

            <div class="box mb-5">
                <form action="{{ route('kits.index') }}" method="GET">
                    <div class="columns is-multiline">
                        <div class="column is-one-quarter">
                            <div class="field">
                                <label class="label">KWP Mínimo</label>
                                <div class="control">
                                    <input class="input" type="number" step="0.01" name="min_kwp" placeholder="KWP Mínimo" value="{{ request('min_kwp') }}">
                                </div>
                            </div>
                        </div>
                        <div class="column is-one-quarter">
                            <div class="field">
                                <label class="label">KWP Máximo</label>
                                <div class="control">
                                    <input class="input" type="number" step="0.01" name="max_kwp" placeholder="KWP Máximo" value="{{ request('max_kwp') }}">
                                </div>
                            </div>
                        </div>

                        <div class="column is-one-quarter">
                            <div class="field">
                                <label class="label">Telhado</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select name="roof_structure" id="filter-roof">
                                            <option value="">Todos os Telhados</option>
                                            @foreach(\App\Enums\RoofStructure::cases() as $roof)
                                                <option value="{{ $roof->value }}" {{ request('roof_structure') == $roof->value ? 'selected' : '' }}>{{ $roof->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-one-quarter">
                            <div class="field">
                                <label class="label">Tensão</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select name="tension_pattern" id="filter-tension">
                                            <option value="">Todas as Tensões</option>
                                            @foreach(\App\Enums\TensionPattern::cases() as $tension)
                                                <option value="{{ $tension->value }}" {{ request('tension_pattern') == $tension->value ? 'selected' : '' }}>{{ $tension->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field is-grouped">
                        <div class="control">
                            <button type="submit" class="button is-primary">Filtrar</button>
                        </div>
                        <div class="control">
                            <a href="{{ route('kits.index') }}" class="button is-danger">Limpar Filtros</a>
                        </div>
                    </div>
                </form>
            </div>

            <table class="table is-fullwidth is-striped">
                <thead>
                <tr>
                    <th>
                        <a href="{{ route('kits.index', array_merge(request()->query(), ['sort_by' => 'id', 'sort_direction' => request('sort_by') == 'id' && request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}" class="is-flex is-align-items-center">
                            ID
                            @if (request('sort_by') == 'id')
                                <span class="ml-2">{{ request('sort_direction') == 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span class="ml-2">↕</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('kits.index', array_merge(request()->query(), ['sort_by' => 'description', 'sort_direction' => request('sort_by') == 'description' && request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}" class="is-flex is-align-items-center">
                            Descrição
                            @if (request('sort_by') == 'description')
                                <span class="ml-2">{{ request('sort_direction') == 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span class="ml-2">↕</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('kits.index', array_merge(request()->query(), ['sort_by' => 'kwp', 'sort_direction' => request('sort_by') == 'kwp' && request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}" class="is-flex is-align-items-center">
                            KWP
                            @if (request('sort_by') == 'kwp')
                                <span class="ml-2">{{ request('sort_direction') == 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span class="ml-2">↕</span>
                            @endif
                        </a>
                    </th>
                    <th>Telhado</th>
                    <th>Tensão</th>
                    <th>
                        <a href="{{ route('kits.index', array_merge(request()->query(), ['sort_by' => 'cost', 'sort_direction' => request('sort_by') == 'cost' && request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}" class="is-flex is-align-items-center">
                            Custo
                            @if (request('sort_by') == 'cost')
                                <span class="ml-2">{{ request('sort_direction') == 'asc' ? '▲' : '▼' }}</span>
                            @else
                                <span class="ml-2">↕</span>
                            @endif
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
