<form action="{{ route('installation.addPlusCosts', [$installation->id]) }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="columns">
        <div class="column is-4">
            <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Custos Adicionais
            </h3>
        </div>
    </div>
    <div class="columns">
        <div class="column is-2">
            <div class="field">
                <label for="plus_cost_description" class="label">Descrição do custo</label>
                <div class="control">
                    <input name="plus_cost_description" required id="plus_cost_description"
                           class="input @error('plus_cost_description') is-danger @enderror" type="text"
                           placeholder="Digite a descrição do custo"
                           value="{{ $installation->plus_cost_description }}">
                    @error('plus_cost_description')<span
                        class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div class="column is-2">
            <div class="field">
                <label for="plus_cost_value" class="label">Valor do custo adicional</label>
                <div class="control">
                    <input name="plus_cost_value" required id="plus_cost_value"
                           class="input @error('plus_cost_value') is-danger @enderror" type="text"
                           placeholder="Digite o valor do custo"
                           value="{{ $installation->plus_cost_value }}">
                    @error('plus_cost_value')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div class="column is-3">
            <label for="plus_cost_proof_of_payment" class="label">Comprovante Pgto.</label>
            <div class="file has-name">
                <label class="file-label">
                    <input class="file-input" required type="file" name="plus_cost_proof_of_payment"
                           id="plus_cost_proof_of_payment">
                    <span class="file-cta">
                                  <span class="file-icon">
                                    <ion-icon name="folder-outline"></ion-icon>
                                  </span>
                                  <span class="file-label">
                                    Escolher arquivo…
                                  </span>
                                </span>
                    <span class="file-name">
                                    Nenhum arquivo selecionado
                                </span>
                </label>
            </div>
        </div>
        <div class="column is-3">
            <label for="plus_cost_invoice" class="label">Nota Fiscal</label>
            <div class="file has-name">
                <label class="file-label">
                    <input class="file-input" required type="file" name="plus_cost_invoice"
                           id="plus_cost_invoice">
                    <span class="file-cta">
                                  <span class="file-icon">
                                    <ion-icon name="folder-outline"></ion-icon>
                                  </span>
                                  <span class="file-label">
                                    Escolher arquivo…
                                  </span>
                                </span>
                    <span class="file-name">
                                    Nenhum arquivo selecionado
                                </span>
                </label>
            </div>
        </div>
        <div class="column is-3">
            <label class="label" for="add_cost"> &nbsp;</label>
            <button type="submit" id="add_cost" class="button is-danger">
                <ion-icon name="add-circle"></ion-icon> &nbsp;Acrescentar
            </button>
        </div>
    </div>
</form>
@include('installation.plusCostTable')
