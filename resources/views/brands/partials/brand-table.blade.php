<h4 class="title is-5">Adicionar Nova Marca de {{ $title }}</h4>
<form class="add-brand-form" data-type="{{ $type }}">
    {{-- Usando o sistema de colunas para alinhar os campos --}}
    <div class="columns is-vcentered">
        <div class="column is-7">
            <div class="control">
                <input class="input" type="text" name="brand" placeholder="Nome da Marca" required>
            </div>
        </div>
        <div class="column is-3">
            <div class="control">
                <input class="input" type="number" name="warranty" placeholder="Garantia (anos)" required>
            </div>
        </div>
        <div class="column is-2">
            <div class="control">
                <button class="button is-success is-fullwidth" type="submit">Adicionar</button>
            </div>
        </div>
    </div>
</form>

<hr>

<h4 class="title is-5">Marcas de {{ $title }} Cadastradas</h4>
<table class="table is-striped is-fullwidth" id="{{ $type }}-brands-table">
    <thead>
    <tr>
        <th>Marca</th>
        <th>Garantia (anos)</th>
        <th class="has-text-right">Ações</th>
    </tr>
    </thead>
    <tbody>
    @foreach($brands as $brand)
        <tr data-id="{{ $brand->id }}" data-type="{{ $type }}" data-brand="{{ $brand->brand }}" data-warranty="{{ $brand->warranty }}">
            <td class="brand-name">{{ $brand->brand }}</td>
            <td class="brand-warranty">{{ $brand->warranty }}</td>
            <td>
                {{-- Alinhando os botões à direita --}}
                <div class="buttons is-justify-content-flex-end">
                    <button class="button is-info is-small edit-btn">Editar</button>
                    <button class="button is-danger is-small delete-btn">Excluir</button>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
