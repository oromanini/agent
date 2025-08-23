@extends('base')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <style>
        .switch { position: relative; display: inline-block; width: 50px; height: 28px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
        .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 4px; bottom: 4px; background-color: white; transition: .4s; }
        input:checked + .slider { background-color: #23d160; }
        input:focus + .slider { box-shadow: 0 0 1px #23d160; }
        input:checked + .slider:before { transform: translateX(22px); }
        .slider.round { border-radius: 34px; }
        .slider.round:before { border-radius: 50%; }
    </style>

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
                        <td class="view-mode panel-brand">{{ $kit->panel_brand }}</td>
                        <td class="view-mode inverter-brand">{{ $kit->inverter_brand }}</td>
                        <td class="view-mode distributor">{{ $kit->distributor }}</td>

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
                            <label class="switch">
                                <input type="checkbox" class="toggle-active-kit-btn" {{ $kit->is_active ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
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
                                <button class="button is-warning is-small mr-1 cancel-button" style="display: none;">
                                    <span class="icon is-small">
                                        <ion-icon name="close-outline"></ion-icon>
                                    </span>
                                </button>
                                <button class="button is-danger is-small delete-button">
                                    <span class="icon is-small">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="delete-confirm-modal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Confirmar Exclusão</p>
                <button class="delete" aria-label="close"></button>
            </header>
            <section class="modal-card-body">
                <p>Tem certeza que deseja excluir esta combinação? Esta ação não pode ser desfeita.</p>
            </section>
            <footer class="modal-card-foot is-justify-content-flex-end">
                <button class="button" id="cancel-delete-btn">Cancelar</button>
                <button class="button is-danger" id="confirm-delete-btn">Excluir</button>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = '{{ csrf_token() }}';
            const notyf = new Notyf({
                duration: 5000,
                position: { x: 'right', y: 'top' },
                types: [
                    { type: 'success', backgroundColor: '#23d160', icon: false },
                    { type: 'error', backgroundColor: '#ff3860', icon: false }
                ]
            });

            const deleteConfirmModal = document.getElementById('delete-confirm-modal');
            let kitToDelete = null;

            async function ensureAuthenticated() {
                let token = localStorage.getItem('auth_token');
                if (!token) {
                    try {
                        const response = await fetch('/api/authorize', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({
                                email: 'oscar.romanini@alluzenergia.com.br',
                                password: 'Neia@vida.2022!'
                            })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            localStorage.setItem('auth_token', data.access_token);
                            return true;
                        } else {
                            notyf.error(`Falha no login automático: ${data.message || 'Credenciais inválidas.'}`);
                            return false;
                        }
                    } catch (error) {
                        notyf.error(`Erro de rede no login automático: ${error.message}`);
                        return false;
                    }
                }
                return true;
            }

            const openDeleteModal = () => deleteConfirmModal.classList.add('is-active');
            const closeDeleteModal = () => deleteConfirmModal.classList.remove('is-active');

            document.body.addEventListener('click', async function(e) {
                if (e.target.closest('.edit-button')) {
                    const row = e.target.closest('tr');
                    toggleEditMode(row, true);
                }
                if (e.target.closest('.cancel-button')) {
                    const row = e.target.closest('tr');
                    toggleEditMode(row, false);
                }
                if (e.target.closest('.save-button')) {
                    if (!await ensureAuthenticated()) return;
                    const row = e.target.closest('tr');
                    saveChanges(row);
                }
                if (e.target.closest('.delete-button')) {
                    kitToDelete = e.target.closest('tr');
                    openDeleteModal();
                }
            });

            document.getElementById('confirm-delete-btn').addEventListener('click', async function() {
                if (!kitToDelete) return;
                if (!await ensureAuthenticated()) return;

                const id = kitToDelete.dataset.id;
                const url = `/active-kits/${id}`;

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                }).then(response => {
                    if (response.ok) {
                        kitToDelete.remove();
                        notyf.success('Combinação excluída com sucesso!');
                    } else {
                        throw new Error('Falha ao excluir.');
                    }
                }).catch(error => notyf.error(error.message))
                    .finally(() => {
                        closeDeleteModal();
                        kitToDelete = null;
                    });
            });

            deleteConfirmModal.querySelector('.modal-background').addEventListener('click', closeDeleteModal);
            deleteConfirmModal.querySelector('.delete').addEventListener('click', closeDeleteModal);
            document.getElementById('cancel-delete-btn').addEventListener('click', closeDeleteModal);

            function saveChanges(row) {
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
                    row.querySelector('.view-mode.panel-brand').textContent = panelBrand;
                    row.querySelector('.view-mode.inverter-brand').textContent = inverterBrand;
                    row.querySelector('.view-mode.distributor').textContent = distributor;

                    toggleEditMode(row, false);
                    notyf.success('Combinação atualizada com sucesso!');
                }).catch(error => {
                    notyf.error(error.message);
                });
            }

            document.body.addEventListener('change', async function(e) {
                if (e.target.classList.contains('toggle-active-kit-btn')) {
                    if (!await ensureAuthenticated()) {
                        e.target.checked = !e.target.checked;
                        return;
                    }

                    const row = e.target.closest('tr');
                    const id = row.dataset.id;
                    const url = `/api/active-kits/${id}/toggle`;
                    const authToken = localStorage.getItem('auth_token');

                    fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Authorization': `Bearer ${authToken}`
                        },
                    }).then(response => {
                        if (response.ok) {
                            notyf.success('Status alterado com sucesso!');
                            return response.json();
                        }
                        e.target.checked = !e.target.checked;
                        if (response.status === 401) {
                            throw new Error('Não autorizado. Faça o login novamente.');
                        }
                        throw new Error('Falha ao alterar o status.');
                    }).catch(error => {
                        notyf.error(error.message);
                    });
                }
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

                const deleteButton = row.querySelector('.delete-button');
                if (deleteButton) deleteButton.style.display = isEditing ? 'none' : '';
            }
        });
    </script>
@endsection
