<div class="columns" style="padding: 10px 10px">
    <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt="..">Financiamento</h3>
</div>
<br>
<form action="{{ route('approval.update.financing', [$proposal->id]) }}" method="post">
    @method('PUT')
    @csrf

    <div class="columns is-flex is-flex-wrap-wrap">
        <div class="column">
            <div class="field">
                <label for="type" class="label">Tipo*</label>
                <div
                    class="select is-multiline is-fullwidth is-rounded @error('type') is-danger @enderror">
                    <select id="type" name="type">
                        <option value="person">Pessoa Física</option>
                        <option
                            value="company" {{ isset($client) && $client->type == 'company' ? 'selected' : '' }}>
                            Pessoa Jurídica
                        </option>
                    </select>
                </div>
                @error('type')<span class="error-message">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label id="nameLabel" for="full_name" class="label">Nome*</label>
                <div class="control">
                    <input name="full_name" id="name"
                           class="input is-rounded @error('name') is-danger @enderror" type="text"
                           placeholder="Digite o nome"
                           value="{{ isset($client) ? $client->name : '' }}">
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label id="documentLabel" for="owner_document" class="label">CPF*</label>
                <div class="control">
                    <input name="owner_document" id="document"
                           class="input is-rounded @error('document') is-danger @enderror" type="text"
                           placeholder="Digite o documento"
                           value="{{ isset($financing) ? $financing->owner_document : $proposal->client->document }}">
                    @error('document')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label for="email" class="label">E-mail</label>
                <div class="control">
                    <input name="email" id="email"
                           class="input is-rounded @error('email') is-danger @enderror" type="email"
                           placeholder="Digite o email"
                           value="{{ isset($financing) ? $financing->email : $proposal->client->email }}">
                    @error('email')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label for="phone_number" class="label">Telefone/Whatsapp*</label>
                <div class="control">
                    <input name="phone_number" id="phone_number"
                           class="input is-rounded @error('phone_number') is-danger @enderror"
                           type="text"
                           placeholder="Digite o telefone/whatsapp"
                           value="{{ isset($financing) ? $financing->phone_number : $proposal->client->phone_number }}">
                    @error('phone_number')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
    </div>
    <div class="columns">
        <div class="column">
            <div class="field">
                <label id="addressLabel" for="address" class="label">Endereço completo*</label>
                <div class="control">
                    <input name="address" id="address"
                           class="input is-rounded @error('address') is-danger @enderror" type="text"
                           placeholder="Digite o endereço completo"
                           value="{{ setStringFromAddress($proposal->client->addresses->first()) }}">
                    @error('address')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div class="column">
            <label for="document_file" class="label">CPF/RG/CNG em PDF</label>
            <div class="file has-name">
                <label class="file-label">
                    <input class="file-input" type="file" name="document_file" id="document_file">
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
        <div class="column">
            <label for="proof_of_income" class="label">Comprovante de renda</label>
            <div class="file has-name">
                <label class="file-label">
                    <input class="file-input" type="file" name="proof_of_income" id="proof_of_income">
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
    </div>
    <div class="columns">
        <div class="column">
            <div class="field">
                <label id="nameLabel" for="birthdate" class="label">Data de nascimento</label>
                <div class="control">
                    <input name="birthdate" id="birthdate"
                           class="input is-rounded @error('birthdate') is-danger @enderror" type="date"
                           value="{{ isset($financing) ? $financing->birthdate : '' }}">
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label for="type" class="label">Tipo de imóvel</label>
                <div
                    class="select is-multiline is-fullwidth is-rounded @error('type') is-danger @enderror">
                    <select id="type" name="type">
                        <option value="proprio" {{ isset($financing) && $financing->property_situation == 'proprio' ? 'selected' : '' }}>Próprio</option>
                        <option value="alugado" {{ isset($financing) && $financing->property_situation == 'alugado' ? 'selected' : '' }}>Alugado</option>
                    </select>
                </div>
                @error('type')<span class="error-message">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label for="income" class="label">Renda</label>
                <div class="control">
                    <input name="income" id="income"
                           class="input is-rounded @error('income') is-danger @enderror" type="text"
                           placeholder="Digite a renda mensal"
                           value="{{ isset($financing) ? $financing->income : '0' }}">
                    @error('income')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label for="patrimony" class="label">Patrimônio</label>
                <div class="control">
                    <input name="patrimony" id="patrimony"
                           class="input is-rounded @error('patrimony') is-danger @enderror" type="text"
                           placeholder="Digite o patrimonio"
                           value="{{ isset($financing) ? $financing->patrimony : '0' }}">
                    @error('patrimony')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label for="profession" class="label">Profissão</label>
                <div class="control">
                    <input name="profession" id="profession"
                           class="input is-rounded @error('profession') is-danger @enderror" type="text"
                           placeholder="Digite a profissão"
                           value="{{ isset($financing) ? $financing->profession : '0' }}">
                    @error('profession')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
    </div>
    <div class="columns">
        <div class="column">
            <div class="field">
                <label for="bank" class="label">Banco</label>
                <div class="control">
                    <input name="bank" id="bank"
                           class="input is-rounded @error('bank') is-danger @enderror" type="text"
                           placeholder="Digite o banco"
                           value="{{ isset($financing) ? $financing->bank : '0' }}">
                    @error('bank')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label for="installment" class="label">Parcelas</label>
                <div class="control">
                    <input name="installment" id="installment"
                           class="input is-rounded @error('installment') is-danger @enderror" type="text"
                           placeholder="Digite a quantia de parcelas"
                           value="{{ isset($financing) ? $financing->installment : '0' }}">
                    @error('profession')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label for="payment_grace" class="label">Carência</label>
                <div class="control">
                    <input name="payment_grace" id="payment_grace"
                           class="input is-rounded @error('payment_grace') is-danger @enderror" type="text"
                           placeholder="Digite a Carência"
                           value="{{ isset($financing) ? $financing->payment_grace : '0' }}">
                    @error('payment_grace')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>
    <div class="columns">
        <div class="column is-12">
            <label class="label" for="observations">Observações</label>
            <textarea id="note" name="note" class="textarea">{{ isset($financing) ? $financing->note : ''  }}</textarea>
        </div>
    </div>
    <div class="columns">
        <div class="column">
            <div class="field">
                <label for="status" class="label">Status</label>
                <div
                    class="select is-multiline is-fullwidth is-rounded  @error('status') is-danger @enderror">
                    <select id="status" name="status">
                        @foreach($financingStatuses as $status)
                            <option value="{{ $status }}" {{ isset($financing) && $status == $financing->status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column">
            <label for="" class="label">Ações</label>
            <button type="submit" class="button is-primary is-large"><ion-icon name="save-outline"></ion-icon> &nbsp;Salvar</button>
            <a href="#" class="button is-danger is-large"><ion-icon name="save-outline"></ion-icon> &nbsp;Gerar resumo</a>
        </div>
    </div>
</form>

<script>
    //contract
    const document_file = document.querySelector('#document_file input[type=file]');
    const proof_of_income = document.querySelector('#proof_of_income input[type=file]');

    document_file.onchange = () => {
        if (document_file.files.length > 0) {
            const fileName = document.querySelector('#document_file .file-name');
            fileName.textContent = document_file.files[0].name;
        }
    }

    proof_of_income.onchange = () => {
        if (proof_of_income.files.length > 0) {
            const fileName = document.querySelector('#proof_of_income .file-name');
            fileName.textContent = proof_of_income.files[0].name;
        }
    }
</script>
