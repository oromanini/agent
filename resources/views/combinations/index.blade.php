@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 mb-5 ml-1">
                <h3 class="title">
                    <img src="/img/logo/alluz-icon.png" width="30" alt="..">
                    Gerenciar Kits Ativos
                </h3>
            </div>

            <a href="{{ route('active-kits.create') }}" class="button is-primary mb-4">Nova Combinação</a>

            <table class="table is-fullwidth is-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Marca do Painel</th>
                    <th>Marca do Inversor</th>
                    <th>Distribuidor</th>
                    <th>Ativo?</th>
                    <th>Última Atualização</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($kits as $kit)
                    <tr data-id="{{ $kit->id }}">
                        <td>{{ $kit->id }}</td>
                        <td class="view-mode">{{ $kit->panel_brand }}</td>
                        <td class="view-mode">{{ $kit->inverter_brand }}</td>
                        <td class="view-mode">{{ $kit->distributor }}</td>

                        <td class="edit-mode" style="display: none;">
                            <input type="text" class="input" name="panel_brand" value="{{ $kit->panel_brand }}">
                        </td>
                        <td class="edit-mode" style="display: none;">
                            <input type="text" class="input" name="inverter_brand" value="{{ $kit->inverter_brand }}">
                        </td>
                        <td class="edit-mode" style="display: none;">
                            <input type="text" class="input" name="distributor" value="{{ $kit->distributor }}">
                        </td>

                        <td>
                            <form action="{{ route('active-kits.toggleActive', $kit) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="button is-text is-small">
                                    <span class="icon is-small">
                                        @if ($kit->is_active)
                                            <ion-icon name="checkbox-outline"></ion-icon>
                                        @else
                                            <ion-icon name="square-outline"></ion-icon>
                                        @endif
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td>{{ $kit->last_updated_time->format('d/m/Y') }}</td>
                        <td>
                            <div class="is-flex is-align-items-center">
                                <button class="button is-info is-small mr-1 edit-button">
                                    <span class="icon is-small">
                                        <ion-icon name="create-outline"></ion-icon>
                                    </span>
                                </button>
                                <button class="button is-success is-small mr-1 save-button" style="display: none;">
                                    <span class="icon is-small">
                                        <ion-icon name="save-outline"></ion-icon>
                                    </span>
                                </button>
                                <button class="button is-warning is-small cancel-button" style="display: none;">
                                    <span class="icon is-small">
                                        <ion-icon name="close-outline"></ion-icon>
                                    </span>
                                </button>
                                <form action="{{ route('active-kits.destroy', $kit) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button is-danger is-small" onclick="return confirm('Tem certeza?');">
                                        <span class="icon is-small">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('input[name="_token"]').value;

            document.querySelectorAll('.edit-button').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    toggleEditMode(row, true);
                });
            });

            document.querySelectorAll('.cancel-button').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    toggleEditMode(row, false);
                });
            });

            document.querySelectorAll('.save-button').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const id = row.dataset.id;
                    const url = `/active-kits/${id}`;

                    const panelBrand = row.querySelector('input[name="panel_brand"]').value;
                    const inverterBrand = row.querySelector('input[name="inverter_brand"]').value;
                    const distributor = row.querySelector('input[name="distributor"]').value;

                    fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            panel_brand: panelBrand,
                            inverter_brand: inverterBrand,
                            distributor: distributor
                        })
                    }).then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        throw new Error('Falha na atualização.');
                    }).then(data => {
                        const viewModeCells = row.querySelectorAll('.view-mode');
                        viewModeCells[0].textContent = panelBrand;
                        viewModeCells[1].textContent = inverterBrand;
                        viewModeCells[2].textContent = distributor;

                        toggleEditMode(row, false);
                        alert('Combinação atualizada com sucesso!');
                    }).catch(error => {
                        alert(error.message);
                    });
                });
            });

            function toggleEditMode(row, isEditing) {
                row.querySelectorAll('.view-mode').forEach(cell => {
                    cell.style.display = isEditing ? 'none' : '';
                });
                row.querySelectorAll('.edit-mode').forEach(cell => {
                    cell.style.display = isEditing ? '' : 'none';
                });
                row.querySelector('.edit-button').style.display = isEditing ? 'none' : '';
                row.querySelector('.save-button').style.display = isEditing ? '' : 'none';
                row.querySelector('.cancel-button').style.display = isEditing ? '' : 'none';
                row.querySelector('.delete-form').style.display = isEditing ? 'none' : '';
            }
        });
    </script>
@endsection
