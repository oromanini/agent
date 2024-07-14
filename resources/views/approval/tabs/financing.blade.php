<div class="columns" style="padding: 10px 10px">
    <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt="..">Financiamento</h3>
</div>
<br>
<form action="{{ route('approval.update.financing', [$proposal->id]) }}" method="post" enctype="multipart/form-data">
    @method('PUT')
    @csrf

    <div class="columns">
        <div id="owner_select" class="column is-3 mr-3">
            <div class="field">
                <label for="status" class="label">
                    <ion-icon name="person-outline"></ion-icon>
                    Responsável</label>
                <div class="select is-multiline is-rounded  @error('owner') is-danger @enderror">
                    <select @if(\Illuminate\Support\Facades\Auth::user()->permission != 'admin') disabled @endif id="owner" name="owner_id">
                        @foreach($financingOwners as $owner)
                            <option
                                value="{{ $owner->id }}" {{ !is_null($financing) && $financing->owner->id == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                        @endforeach
                    </select>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div id="status_select" class="column is-3 mr-3">
            <div class="field">
                <label for="status" class="label">
                    <ion-icon name="information-circle-outline"></ion-icon>
                    Status</label>
                <div class="select is-multiline is-rounded  @error('status') is-danger @enderror">
                    <select id="status" name="status_id">
                        @foreach($financingStatuses as $status)
                            <option
                                value="{{ $status->id }}" {{ isset($financing) && $status->id == $financing->status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>
    <hr>
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
                           value="{{ is_null($financing) ? $client->name : $financing->full_name }}">
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
        <div class="column is-6">
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
        <div class="column is-3">
            <label for="" class="label">&nbsp;Documento do cliente</label>
            <a href="/storage/{{ str_replace('public/', '', $proposal->client->owner_document) }}"
               {{ !isset($proposal->client->owner_document) ? 'disabled' : '' }}
               class="button is-danger" target="_blank">
                <ion-icon name="eye-outline"></ion-icon>
                Visualizar Documento</a>
        </div>
        <div class="column is-3">
            <label for="proof_of_income" class="label">Comprovante de renda</label>
            @if(isset($proposal->financing->proof_of_income))
                <a href="/storage/{{ str_replace('public/', '', $proposal->financing->proof_of_income) }}"
                   class="button is-danger" target="_blank">
                    <ion-icon name="eye-outline"></ion-icon>
                    Visualizar comprovante</a>
            @else
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
            @endif
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
                    <select id="property_situation" name="property_situation">
                        <option
                            value="own" {{ isset($financing) && $financing->property_situation == 'own' ? 'selected' : '' }}>
                            Próprio
                        </option>
                        <option
                            value="rented" {{ isset($financing) && $financing->property_situation == 'rented' ? 'selected' : '' }}>
                            Alugado
                        </option>
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
                           value="{{ isset($financing) ? $financing->income : '' }}">
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
                           value="{{ isset($financing) ? $financing->patrimony : '' }}">
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
                           value="{{ isset($financing) ? $financing->profession : '' }}">
                    @error('profession')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
    </div>
    <hr>
    <hr>
    <div class="columns">
        <div class="column">
            <div class="field">
                <label for="bank" class="label">Banco</label>
                <div class="control">
                    <input name="bank" id="bank"
                           class="input is-rounded @error('bank') is-danger @enderror" type="text"
                           placeholder="Digite o banco"
                           value="{{ isset($financing) ? $financing->bank : '' }}">
                    @error('bank')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label for="installments" class="label">Parcelas*</label>
                <div
                    class="select is-multiline is-fullwidth is-rounded @error('installments') is-danger @enderror">
                    <select id="installments" name="installments">
                        <option {{ isset($financing) && $financing->installments == 12 ? 'selected' : '' }} value="12">
                            12x
                        </option>
                        <option {{ isset($financing) && $financing->installments == 24 ? 'selected' : '' }} value="24">
                            24x
                        </option>
                        <option {{ isset($financing) && $financing->installments == 36 ? 'selected' : '' }} value="36">
                            36x
                        </option>
                        <option {{ isset($financing) && $financing->installments == 48 ? 'selected' : '' }} value="48">
                            48x
                        </option>
                        <option {{ isset($financing) && $financing->installments == 60 ? 'selected' : '' }} value="60">
                            60x
                        </option>
                        <option {{ isset($financing) && $financing->installments == 72 ? 'selected' : '' }} value="72">
                            72x
                        </option>
                        <option {{ isset($financing) && $financing->installments == 84 ? 'selected' : '' }} value="84">
                            84x
                        </option>
                        <option {{ isset($financing) && $financing->installments == 96 ? 'selected' : '' }} value="96">
                            96x
                        </option>
                        <option
                            {{ isset($financing) && $financing->installments == 108 ? 'selected' : '' }} value="108">
                            108x
                        </option>
                        <option
                            {{ isset($financing) && $financing->installments == 120 ? 'selected' : '' }} value="120">
                            120x
                        </option>
                        <option
                            {{ isset($financing) && $financing->installments == 132 ? 'selected' : '' }} value="132">
                            132x
                        </option>
                        <option
                            {{ isset($financing) && $financing->installments == 144 ? 'selected' : '' }} value="144">
                            144x
                        </option>
                    </select>
                </div>
                @error('installments')<span class="error-message">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label for="payment_grace" class="label">Carência*</label>
                <div class="select is-multiline is-fullwidth is-rounded @error('payment_grace') is-danger @enderror">
                    <select id="payment_grace" name="payment_grace">
                        @for($i = 1; $i <= 12; $i++)
                            <option
                                {{ isset($financing) && $financing->payment_grace == $i ? 'selected' : '' }} value="{{$i}}">{{$i}}
                                mês/meses
                            </option>
                        @endfor
                    </select>
                </div>
                @error('payment_grace')<span class="error-message">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>
    <div class="columns">
        <div class="column is-12">
            <label class="label" for="observations">Observações</label>
            <textarea id="note" name="note" class="textarea">{{ isset($financing) ? $financing->note : ''  }}</textarea>
        </div>
    </div>


        @php
            $canSave = auth()->user()->permission == 'admin'
            || auth()->user()->permission == 'financial';
        @endphp

        <div class="column" @if(!$canSave) style="display: none" @endif>
            <label for="" class="label">Ações</label>
            <button type="submit" class="button is-primary is-large">
                <ion-icon name="save-outline"></ion-icon> &nbsp;Salvar
            </button>
            {{--            <a href="#" class="button is-danger is-large">--}}
            {{--                <ion-icon name="save-outline"></ion-icon> &nbsp;Gerar resumo</a>--}}
        </div>
    </div>
</form>

<script>

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
