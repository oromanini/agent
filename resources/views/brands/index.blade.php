@extends('base')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <style>
        .switch { display: none; }
        .brand-image { max-width: 100px; max-height: 40px; object-fit: contain; }
    </style>

    <div class="container is-fluid overflow-auto">
        <nav class="tabs is-boxed is-fullwidth is-large" style="margin-bottom: 0">
            <div class="container">
                <ul>
                    <li id="panels-li" class="mytab is-active" data-tab="panels">
                        <a><ion-icon name="flash-outline"></ion-icon> Marcas de Módulos</a>
                    </li>
                    <li id="inverters-li" class="mytab" data-tab="inverters">
                        <a><ion-icon name="camera-outline"></ion-icon> Marcas de Inversores</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="box overflow-auto">
            <div id="panels" class="content-tab">
                <div class="level">
                    <div class="level-left"><h4 class="title is-5">Marcas de Módulo Cadastradas</h4></div>
                    <div class="level-right"><button class="button is-success open-modal-btn" data-type="panel" data-mode="add">Adicionar Nova Marca</button></div>
                </div>
                <table class="table is-striped is-fullwidth" id="panel-brands-table">
                    <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Foto</th>
                        <th>Marca</th>
                        <th>Garantia</th>
                        <th>Garantia Linear</th>
                        <th>Enum</th>
                        <th class="has-text-right">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($panelBrands as $brand)
                        <tr data-id="{{ $brand->id }}" data-type="panel" data-brand="{{ $brand->name }}" data-warranty="{{ $brand->warranty }}" data-linear-warranty="{{ $brand->linear_warranty }}" data-brand-enum="{{ $brand->brand_enum }}">
                            <td><img src="{{ $brand->logo ? asset('storage/' . $brand->logo) : 'https://via.placeholder.com/100x40?text=S/Logo' }}" alt="Logo" class="brand-image"></td>
                            <td><img src="{{ $brand->picture ? asset('storage/' . $brand->picture) : 'https://via.placeholder.com/100x40?text=S/Foto' }}" alt="Foto" class="brand-image"></td>
                            <td class="brand-name">{{ $brand->name }}</td>
                            <td class="brand-warranty">{{ $brand->warranty }}</td>
                            <td class="brand-linear-warranty">{{ $brand->linear_warranty }}</td>
                            <td class="brand-enum">{{ $brand->brand_enum }}</td>
                            <td>
                                <div class="buttons is-justify-content-flex-end">
                                    <button class="button is-info is-small open-modal-btn" data-mode="edit">Editar</button>
                                    <button class="button is-danger is-small delete-btn">Excluir</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="has-text-centered">Nenhuma marca encontrada.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div id="inverters" class="content-tab" style="display:none">
                <div class="level">
                    <div class="level-left"><h4 class="title is-5">Marcas de Inversor Cadastradas</h4></div>
                    <div class="level-right"><button class="button is-success open-modal-btn" data-type="inverter" data-mode="add">Adicionar Nova Marca</button></div>
                </div>
                <table class="table is-striped is-fullwidth" id="inverter-brands-table">
                    <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Foto</th>
                        <th>Marca</th>
                        <th>Garantia (anos)</th>
                        <th>Overload</th>
                        <th>Enum</th>
                        <th class="has-text-right">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($inverterBrands as $brand)
                        <tr data-id="{{ $brand->id }}" data-type="inverter" data-brand="{{ $brand->name }}" data-warranty="{{ $brand->warranty }}" data-overload="{{ $brand->overload }}" data-brand-enum="{{ $brand->brand_enum }}">
                            <td><img src="{{ $brand->logo ? asset('storage/' . $brand->logo) : 'https://via.placeholder.com/100x40?text=S/Logo' }}" alt="Logo" class="brand-image"></td>
                            <td><img src="{{ $brand->picture ? asset('storage/' . $brand->picture) : 'https://via.placeholder.com/100x40?text=S/Foto' }}" alt="Foto" class="brand-image"></td>
                            <td class="brand-name">{{ $brand->name }}</td>
                            <td class="brand-warranty">{{ $brand->warranty }}</td>
                            <td class="brand-overload">{{ $brand->overload }}</td>
                            <td class="brand-enum">{{ $brand->brand_enum }}</td>
                            <td>
                                <div class="buttons is-justify-content-flex-end">
                                    <button class="button is-info is-small open-modal-btn" data-mode="edit">Editar</button>
                                    <button class="button is-danger is-small delete-btn">Excluir</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="has-text-centered">Nenhum registro encontrado.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal" id="brand-modal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title" id="modal-title">Adicionar/Editar Marca</p>
                <button class="delete" aria-label="close"></button>
            </header>
            <section class="modal-card-body">
                <form id="brand-form" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="field">
                        <label class="label">Nome da Marca</label>
                        <div class="control">
                            <input class="input" type="text" name="brand" placeholder="Nome da Marca" required oninput="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Garantia (anos)</label>
                        <div class="control"><input class="input" type="number" name="warranty" required></div>
                    </div>
                    <div class="field" id="linear-warranty-field" style="display: none;">
                        <label class="label">Garantia Linear (anos)</label>
                        <div class="control"><input class="input" type="number" name="linear_warranty"></div>
                    </div>
                    <div class="field" id="overload-field" style="display: none;">
                        <label class="label">Overload</label>
                        <div class="control"><input class="input" type="number" step="0.01" name="overload"></div>
                    </div>

                    <div class="field">
                        <label class="label">Logo</label>
                        <div class="control">
                            <div class="file has-name is-fullwidth">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="logo" accept="image/*">
                                    <span class="file-cta"><span class="file-icon"><ion-icon name="cloud-upload-outline"></ion-icon></span><span class="file-label">Escolher logo...</span></span>
                                    <span class="file-name">Nenhum arquivo</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Foto</label>
                        <div class="control">
                            <div class="file has-name is-fullwidth">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="picture" accept="image/*">
                                    <span class="file-cta"><span class="file-icon"><ion-icon name="cloud-upload-outline"></ion-icon></span><span class="file-label">Escolher foto...</span></span>
                                    <span class="file-name">Nenhum arquivo</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot">
                <button class="button is-success" id="save-btn">Salvar</button>
                <button class="button" id="cancel-btn">Cancelar</button>
            </footer>
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
                <p>Tem certeza que deseja excluir esta marca? Esta ação não pode ser desfeita.</p>
                <p id="delete-brand-info" class="has-text-weight-bold mt-2"></p>
            </section>
            <footer class="modal-card-foot is-justify-content-flex-end">
                <button class="button" id="cancel-delete-btn">Cancelar</button>
                <button class="button is-danger" id="confirm-delete-btn">Excluir</button>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notyf = new Notyf({
                duration: 5000,
                position: { x: 'right', y: 'top' },
                types: [
                    { type: 'success', backgroundColor: '#23d160', icon: false },
                    { type: 'error', backgroundColor: '#ff3860', icon: false }
                ]
            });

            const modal = document.getElementById('brand-modal');
            const modalTitle = document.getElementById('modal-title');
            const form = document.getElementById('brand-form');
            const saveBtn = document.getElementById('save-btn');
            const overloadField = document.getElementById('overload-field');
            const linearWarrantyField = document.getElementById('linear-warranty-field');
            let currentBrand = { id: null, type: null, mode: null };

            const deleteConfirmModal = document.getElementById('delete-confirm-modal');
            const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
            const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
            let brandToDelete = { id: null, type: null, rowElement: null };

            const getAuthHeaders = () => ({
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
            });

            async function ensureAuthenticated() { return true; }

            const openModal = () => modal.classList.add('is-active');
            const closeModal = () => modal.classList.remove('is-active');
            const openDeleteModal = () => deleteConfirmModal.classList.add('is-active');
            const closeDeleteModal = () => deleteConfirmModal.classList.remove('is-active');

            document.querySelectorAll('.mytab').forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabName = tab.dataset.tab;

                    document.querySelectorAll('.content-tab').forEach(content => content.style.display = 'none');
                    document.querySelectorAll('.mytab').forEach(t => t.classList.remove('is-active'));

                    document.getElementById(tabName).style.display = 'block';
                    tab.classList.add('is-active');
                });
            });

            document.body.addEventListener('click', function(e) {
                const openModalButton = e.target.closest('.open-modal-btn');
                const deleteButton = e.target.closest('.delete-btn');

                if (openModalButton) {
                    const mode = openModalButton.dataset.mode;
                    const row = openModalButton.closest('tr');
                    currentBrand.type = row ? row.dataset.type : openModalButton.dataset.type;
                    currentBrand.mode = mode;

                    form.reset();
                    document.querySelectorAll('.file-name').forEach(el => el.textContent = 'Nenhum arquivo');

                    overloadField.style.display = currentBrand.type === 'inverter' ? 'block' : 'none';
                    linearWarrantyField.style.display = currentBrand.type === 'panel' ? 'block' : 'none';
                    form.querySelector('input[name="overload"]').required = currentBrand.type === 'inverter';

                    if (mode === 'add') {
                        modalTitle.textContent = `Adicionar Nova Marca de ${currentBrand.type === 'panel' ? 'Módulo' : 'Inversor'}`;
                        currentBrand.id = null;
                    } else {
                        modalTitle.textContent = `Editar Marca de ${currentBrand.type === 'panel' ? 'Módulo' : 'Inversor'}`;
                        currentBrand.id = row.dataset.id;

                        form.querySelector('input[name="brand"]').value = row.dataset.brand;
                        form.querySelector('input[name="warranty"]').value = row.dataset.warranty;

                        if(currentBrand.type === 'panel') {
                            form.querySelector('input[name="linear_warranty"]').value = row.dataset.linearWarranty;
                        }
                        if(currentBrand.type === 'inverter') {
                            form.querySelector('input[name="overload"]').value = row.dataset.overload;
                        }
                    }
                    openModal();
                }

                if (deleteButton) {
                    const row = deleteButton.closest('tr');
                    brandToDelete.id = row.dataset.id;
                    brandToDelete.type = row.dataset.type;
                    brandToDelete.rowElement = row;

                    const brandName = row.dataset.brand;
                    document.getElementById('delete-brand-info').textContent = `Marca: ${brandName}`;

                    openDeleteModal();
                }
            });

            modal.querySelector('.modal-background').addEventListener('click', closeModal);
            modal.querySelector('.delete').addEventListener('click', closeModal);
            document.getElementById('cancel-btn').addEventListener('click', closeModal);

            deleteConfirmModal.querySelector('.modal-background').addEventListener('click', closeDeleteModal);
            deleteConfirmModal.querySelector('.delete').addEventListener('click', closeDeleteModal);
            cancelDeleteBtn.addEventListener('click', closeDeleteModal);

            document.querySelectorAll('.file-input').forEach(input => {
                input.addEventListener('change', (event) => {
                    const file = event.target.files[0];
                    const fileNameEl = input.closest('.file').querySelector('.file-name');
                    fileNameEl.textContent = file ? file.name : 'Nenhum arquivo';
                });
            });

            saveBtn.addEventListener('click', async () => {
                if (!await ensureAuthenticated()) return;

                const formData = new FormData(form);
                let url = `/api/brands/${currentBrand.type}`;

                if (currentBrand.mode === 'edit') {
                    url = `/api/brands/${currentBrand.id}`;
                    formData.append('_method', 'PUT');
                }

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: getAuthHeaders(),
                        body: formData,
                    });

                    if (response.ok) {
                        const savedBrand = await response.json();
                        if (currentBrand.mode === 'add') {
                            appendTableRow(savedBrand, currentBrand.type);
                        } else {
                            updateTableRow(savedBrand, currentBrand.type);
                        }
                        closeModal();
                        notyf.success('Marca salva com sucesso!');
                    } else {
                        await handleFetchError(response);
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    notyf.error('Erro de conexão. Verifique o console.');
                }
            });

            confirmDeleteBtn.addEventListener('click', async () => {
                if (!brandToDelete.id || !brandToDelete.type) return;
                if (!await ensureAuthenticated()) return;

                const { id, type, rowElement } = brandToDelete;

                const response = await fetch(`/api/brands/${id}`, {
                    method: 'DELETE',
                    headers: getAuthHeaders()
                });

                if (response.ok) {
                    rowElement.remove();
                    notyf.success('Marca excluída com sucesso!');
                } else {
                    await handleFetchError(response);
                }
                closeDeleteModal();
            });

            function createTableRowHTML(brand, type) {
                const logoUrl = brand.logo ? `{{ asset('storage') }}/${brand.logo}` : `https://via.placeholder.com/100x40?text=S/Logo`;
                const pictureUrl = brand.picture ? `{{ asset('storage') }}/${brand.picture}` : `https://via.placeholder.com/100x40?text=S/Foto`;

                let overloadCell = '';
                let linearWarrantyCell = '';
                if (type === 'inverter') {
                    overloadCell = `<td class="brand-overload">${brand.overload}</td>`;
                }
                if (type === 'panel') {
                    linearWarrantyCell = `<td class="brand-linear-warranty">${brand.linear_warranty || ''}</td>`;
                }

                return `
                    <td><img src="${logoUrl}" alt="Logo" class="brand-image"></td>
                    <td><img src="${pictureUrl}" alt="Foto" class="brand-image"></td>
                    <td class="brand-name">${brand.name}</td>
                    <td class="brand-warranty">${brand.warranty}</td>
                    ${type === 'panel' ? linearWarrantyCell : ''}
                    ${type === 'inverter' ? overloadCell : ''}
                    <td>
                        <div class="buttons is-justify-content-flex-end">
                            <button class="button is-info is-small open-modal-btn" data-mode="edit">Editar</button>
                            <button class="button is-danger is-small delete-btn">Excluir</button>
                        </div>
                    </td>
                `;
            }

            function appendTableRow(brand, type) {
                const tableBody = document.querySelector(`#${type}-brands-table tbody`);
                const emptyRow = tableBody.querySelector('td[colspan]');
                if(emptyRow) emptyRow.parentElement.remove();

                const newRow = tableBody.insertRow();
                newRow.dataset.id = brand.id;
                newRow.dataset.type = type;
                newRow.dataset.brand = brand.name;
                newRow.dataset.warranty = brand.warranty;
                if (type === 'panel') {
                    newRow.dataset.linearWarranty = brand.linear_warranty;
                }
                if (type === 'inverter') {
                    newRow.dataset.overload = brand.overload;
                }
                newRow.innerHTML = createTableRowHTML(brand, type);
            }

            function updateTableRow(brand, type) {
                const row = document.querySelector(`tr[data-id='${brand.id}'][data-type='${type}']`);
                if (row) {
                    row.dataset.brand = brand.name;
                    row.dataset.warranty = brand.warranty;
                    if (type === 'panel') {
                        row.dataset.linearWarranty = brand.linear_warranty;
                    }
                    if (type === 'inverter') {
                        row.dataset.overload = brand.overload;
                    }
                    row.innerHTML = createTableRowHTML(brand, type);
                }
            }

            async function handleFetchError(response) {
                const errorData = await response.json();
                const message = errorData.message || 'Ocorreu um erro.';
                const errors = errorData.errors ? Object.values(errorData.errors).flat().join('\n') : '';
                notyf.error(errors || message);
            }
        });
    </script>
@endsection
