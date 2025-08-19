@extends('base')

@section('content')
    {{-- Estilos para o Toggle Switch --}}
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
        <nav class="tabs is-boxed is-fullwidth is-large" style="margin-bottom: 0">
            <div class="container">
                <ul>
                    <li id="modules-li" class="mytab is-active" onclick="openTab(event,'modules')">
                        <a style="color: #6b7280; font-size: 12pt">
                            <ion-icon name="flash-outline"></ion-icon>
                            Marcas de Módulos
                        </a>
                    </li>
                    <li id="inverters-li" class="mytab" onclick="openTab(event,'inverters')">
                        <a style="color: #6b7280; font-size: 12pt">
                            <ion-icon name="camera-outline"></ion-icon>
                            Marcas de Inversores
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="box overflow-auto">
            {{-- Conteúdo da Aba de Módulos --}}
            <div id="modules" class="content-tab">
                <h4 class="title is-5">Adicionar Nova Marca de Módulo</h4>
                <form class="add-brand-form" data-type="module">
                    <div class="columns is-vcentered">
                        <div class="column is-7"><input class="input" type="text" name="brand" placeholder="Nome da Marca" required></div>
                        <div class="column is-3"><input class="input" type="number" name="warranty" placeholder="Garantia (anos)" required></div>
                        <div class="column is-2"><button class="button is-success is-fullwidth" type="submit">Adicionar</button></div>
                    </div>
                </form>
                <hr>
                <h4 class="title is-5">Marcas de Módulo Cadastradas</h4>
                <table class="table is-striped is-fullwidth" id="module-brands-table">
                    <thead><tr><th>Marca</th><th>Garantia (anos)</th><th>Ativo</th><th class="has-text-right">Ações</th></tr></thead>
                    <tbody>
                    @forelse($moduleBrands as $brand)
                        <tr data-id="{{ $brand->id }}" data-type="module" data-brand="{{ $brand->brand }}" data-warranty="{{ $brand->warranty }}">
                            <td class="brand-name">{{ $brand->brand }}</td>
                            <td class="brand-warranty">{{ $brand->warranty }}</td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" class="toggle-active-btn" {{ $brand->active ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td><div class="buttons is-justify-content-flex-end"><button class="button is-info is-small edit-btn">Editar</button><button class="button is-danger is-small delete-btn">Excluir</button></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="has-text-centered">Nenhum registro encontrado.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Conteúdo da Aba de Inversores --}}
            <div id="inverters" class="content-tab" style="display:none">
                <h4 class="title is-5">Adicionar Nova Marca de Inversor</h4>
                <form class="add-brand-form" data-type="inverter">
                    <div class="columns is-vcentered">
                        <div class="column is-7"><input class="input" type="text" name="brand" placeholder="Nome da Marca" required></div>
                        <div class="column is-3"><input class="input" type="number" name="warranty" placeholder="Garantia (anos)" required></div>
                        <div class="column is-2"><button class="button is-success is-fullwidth" type="submit">Adicionar</button></div>
                    </div>
                </form>
                <hr>
                <h4 class="title is-5">Marcas de Inversor Cadastradas</h4>
                <table class="table is-striped is-fullwidth" id="inverter-brands-table">
                    <thead><tr><th>Marca</th><th>Garantia (anos)</th><th>Ativo</th><th class="has-text-right">Ações</th></tr></thead>
                    <tbody>
                    @forelse($inverterBrands as $brand)
                        <tr data-id="{{ $brand->id }}" data-type="inverter" data-brand="{{ $brand->brand }}" data-warranty="{{ $brand->warranty }}">
                            <td class="brand-name">{{ $brand->brand }}</td>
                            <td class="brand-warranty">{{ $brand->warranty }}</td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" class="toggle-active-btn" {{ $brand->active ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td><div class="buttons is-justify-content-flex-end"><button class="button is-info is-small edit-btn">Editar</button><button class="button is-danger is-small delete-btn">Excluir</button></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="has-text-centered">Nenhum registro encontrado.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL DE EDIÇÃO --}}
    <div class="modal" id="edit-modal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head"><p class="modal-card-title" id="edit-modal-title">Editar Marca</p><button class="delete" aria-label="close"></button></header>
            <section class="modal-card-body">
                <form id="edit-form">
                    <div class="field"><label class="label">Nome da Marca</label><div class="control"><input class="input" type="text" id="edit-brand-name" required></div></div>
                    <div class="field"><label class="label">Garantia (anos)</label><div class="control"><input class="input" type="number" id="edit-brand-warranty" required></div></div>
                </form>
            </section>
            <footer class="modal-card-foot"><button class="button is-success" id="save-changes-btn">Salvar</button><button class="button" id="cancel-edit-btn">Cancelar</button></footer>
        </div>
    </div>

    <script>
        function openTab(event, tabName) {
            let i, x, tablinks;
            x = document.getElementsByClassName("content-tab");
            for (i = 0; i < x.length; i++) { x[i].style.display = "none"; }
            tablinks = document.getElementsByClassName("mytab");
            for (i = 0; i < tablinks.length; i++) { tablinks[i].classList.remove("is-active"); }
            document.getElementById(tabName).style.display = "block";
            event.currentTarget.classList.add("is-active");
        }

        document.addEventListener('DOMContentLoaded', function () {
            const editModal = document.getElementById('edit-modal');
            const saveChangesBtn = document.getElementById('save-changes-btn');
            let currentEditData = {};

            function getAuthHeaders() {
                const token = localStorage.getItem('auth_token');
                return {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': `Bearer ${token}`
                };
            }

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
                            alert(`Falha no login automático: ${data.message || 'Credenciais inválidas.'}`);
                            return false;
                        }
                    } catch (error) {
                        alert(`Erro de rede no login automático: ${error.message}`);
                        return false;
                    }
                }
                return true;
            }

            function openModal() { editModal.classList.add('is-active'); }
            function closeModal() { editModal.classList.remove('is-active'); }
            editModal.querySelector('.modal-background').addEventListener('click', closeModal);
            editModal.querySelector('.delete').addEventListener('click', closeModal);
            document.getElementById('cancel-edit-btn').addEventListener('click', closeModal);

            async function handleFetchError(response) {
                let errorMessage = 'Erro desconhecido no servidor.';
                try {
                    const errorData = await response.json();
                    if (errorData.message) {
                        errorMessage = errorData.message;
                    } else if (errorData.errors) {
                        let messages = [];
                        for (const field in errorData.errors) {
                            messages.push(errorData.errors[field].join(', '));
                        }
                        errorMessage = messages.join('\n');
                    }
                } catch (e) {
                    const rawText = await response.text();
                    console.error("Raw server response:", rawText);
                    errorMessage = "Ocorreu um erro inesperado. Verifique o console do navegador (F12) para mais detalhes.";
                }
                alert('Ocorreu um erro:\n' + errorMessage);
            }

            document.body.addEventListener('submit', async function(e) {
                if (e.target && e.target.classList.contains('add-brand-form')) {
                    e.preventDefault();
                    if (!await ensureAuthenticated()) return;
                    const form = e.target;
                    const type = form.dataset.type;
                    const brandInput = form.querySelector('input[name="brand"]');
                    const warrantyInput = form.querySelector('input[name="warranty"]');
                    const response = await fetch(`/api/brands/${type}`, {
                        method: 'POST', headers: getAuthHeaders(),
                        body: JSON.stringify({ brand: brandInput.value, warranty: warrantyInput.value })
                    });
                    if (response.ok) {
                        const newBrand = await response.json();
                        const newRow = createTableRow(newBrand, type);
                        const emptyRow = document.querySelector(`#${type}-brands-table tbody tr td[colspan='4']`);
                        if (emptyRow) { emptyRow.parentElement.remove(); }
                        document.querySelector(`#${type}-brands-table tbody`).appendChild(newRow);
                        form.reset();
                    } else { await handleFetchError(response); }
                }
            });

            document.body.addEventListener('click', async function(e) {
                if (e.target && e.target.classList.contains('edit-btn')) {
                    e.preventDefault();
                    const row = e.target.closest('tr');
                    currentEditData = { id: row.dataset.id, type: row.dataset.type };
                    document.getElementById('edit-modal-title').textContent = `Editar Marca (${row.dataset.type})`;
                    document.getElementById('edit-brand-name').value = row.dataset.brand;
                    document.getElementById('edit-brand-warranty').value = row.dataset.warranty;
                    openModal();
                }
                if (e.target && e.target.classList.contains('delete-btn')) {
                    e.preventDefault();
                    if (confirm('Tem certeza que deseja excluir esta marca?')) {
                        if (!await ensureAuthenticated()) return;
                        const row = e.target.closest('tr');
                        const { id, type } = row.dataset;
                        const response = await fetch(`/api/brands/${type}/${id}`, {
                            method: 'DELETE', headers: getAuthHeaders()
                        });
                        if (response.ok) {
                            row.remove();
                            const tbody = document.querySelector(`#${type}-brands-table tbody`);
                            if (tbody.children.length === 0) {
                                const emptyRow = document.createElement('tr');
                                emptyRow.innerHTML = `<td colspan="4" class="has-text-centered">Nenhum registro encontrado.</td>`;
                                tbody.appendChild(emptyRow);
                            }
                        } else { await handleFetchError(response); }
                    }
                }
            });

            document.body.addEventListener('change', async function(e) {
                if (e.target && e.target.classList.contains('toggle-active-btn')) {
                    if (!await ensureAuthenticated()) {
                        e.target.checked = !e.target.checked;
                        return;
                    }
                    const row = e.target.closest('tr');
                    const { id, type } = row.dataset;
                    const response = await fetch(`/api/brands/${type}/${id}/toggle`, {
                        method: 'PATCH',
                        headers: getAuthHeaders()
                    });
                    if (!response.ok) {
                        e.target.checked = !e.target.checked;
                        await handleFetchError(response);
                    }
                }
            });

            saveChangesBtn.addEventListener('click', async () => {
                if (!await ensureAuthenticated()) return;
                const { id, type } = currentEditData;
                const updatedBrand = document.getElementById('edit-brand-name').value;
                const updatedWarranty = document.getElementById('edit-brand-warranty').value;
                const response = await fetch(`/api/brands/${type}/${id}`, {
                    method: 'PUT', headers: getAuthHeaders(),
                    body: JSON.stringify({ brand: updatedBrand, warranty: updatedWarranty })
                });
                if (response.ok) {
                    const data = await response.json();
                    const rowToUpdate = document.querySelector(`tr[data-id='${id}'][data-type='${type}']`);
                    if (rowToUpdate) {
                        rowToUpdate.querySelector('.brand-name').textContent = data.brand;
                        rowToUpdate.querySelector('.brand-warranty').textContent = data.warranty;
                        rowToUpdate.dataset.brand = data.brand;
                        rowToUpdate.dataset.warranty = data.warranty;
                    }
                    closeModal();
                } else { await handleFetchError(response); }
            });

            function createTableRow(brand, type) {
                const tr = document.createElement('tr');
                tr.dataset.id = brand.id;
                tr.dataset.type = type;
                tr.dataset.brand = brand.brand;
                tr.dataset.warranty = brand.warranty;
                const checked = brand.active ? 'checked' : '';
                tr.innerHTML = `
                    <td class="brand-name">${brand.brand}</td>
                    <td class="brand-warranty">${brand.warranty}</td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" class="toggle-active-btn" ${checked}>
                            <span class="slider round"></span>
                        </label>
                    </td>
                    <td>
                        <div class="buttons is-justify-content-flex-end">
                            <button class="button is-info is-small edit-btn">Editar</button>
                            <button class="button is-danger is-small delete-btn">Excluir</button>
                        </div>
                    </td>
                `;
                return tr;
            }
        });
    </script>
@endsection
