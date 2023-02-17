<div class="box">
    <div class="columns">
        <h6 class="title">
            <ion-icon name="person-outline"></ion-icon> &nbsp;&nbsp; Dados do cliente
        </h6>
    </div>
    <div class="columns">
        <div class="column is-3">
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
        <div class="column is-3">
            <div class="field">
                <label id="nameLabel" for="name" class="label">Nome*</label>
                <div class="control">
                    <input name="name" id="name"
                           class="input is-rounded @error('name') is-danger @enderror" type="text"
                           placeholder="Digite o nome"
                           value="{{ isset($client) ? $client->name : '' }}">
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column is-3">
            <div class="field">
                <label id="documentLabel" for="document" class="label">{{ isset($client) ? $client->type == 'person' ? 'CPF*' : 'CNPJ*' : 'CPF' }}</label>
                <div class="control">
                    <input name="document" id="{{ isset($client) && $client->type == 'company' ? 'cnpj' : 'cpf' }}"
                           class="input is-rounded @error('document') is-danger @enderror" type="text"
                           placeholder="Digite o documento"
                           value="{{ isset($client) ? $client->document : '' }}">
                    @error('document')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column is-3">
            <div class="field">
                <label id="aliasLabel" for="alias" class="label">{{ isset($client) ? $client->type == 'person' ? 'Apelido' : 'Nome Fantasia*' : 'Apelido' }}</label>
                <div class="control">
                    <input name="alias" id="alias" class="input is-rounded" type="text"
                           placeholder="Digite o apelido/nome fantasia"
                           value="{{ isset($client) ? $client->alias : '' }}">
                </div>
            </div>
        </div>
    </div>
    <div class="columns">
        <div class="column is-3">
            <div class="field">
                <label for="email" class="label">E-mail</label>
                <div class="control">
                    <input name="email" id="email"
                           class="input is-rounded @error('email') is-danger @enderror" type="email"
                           placeholder="Digite o email"
                           value="{{ isset($client) ? $client->email : '' }}">
                    @error('email')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column is-3">
            <div class="field">
                <label for="phone_number" class="label">Telefone/Whatsapp*</label>
                <div class="control">
                    <input name="phone_number" id="phone_number"
                           class="input is-rounded @error('phone_number') is-danger @enderror"
                           type="text"
                           placeholder="Digite o telefone/whatsapp"
                           value="{{ isset($client) ? $client->phone_number  : '' }}">
                    @error('phone_number')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>

        @if(isset($client) && !is_null($client->owner_document))
            <div class="column is-3">
                <label for="owner_document" class="label">CNH/RG do {{ isset($client) ? $client->type == 'person' ? 'cliente' : 'proprietário' : '' }}</label>
                <a href="/storage/{{ str_replace('public/', '', $client->owner_document) }}"
                   class="button is-danger" target="_blank">
                    <ion-icon name="eye-outline"></ion-icon>
                    Visualizar Documento</a>
            </div>
        @else
            <div class="column is-3">
                <label for="owner_document" class="label">CNH/RG do {{ isset($client) ? $client->type == 'person' ? 'cliente' : 'proprietário' : '' }}</label>
                <div class="file has-name" id="owner_document">
                    <label class="file-label">
                        <input class="file-input" type="file" name="owner_document">
                        <span class="file-cta">
                                  <span class="file-icon">
                                    <ion-icon name="folder-outline"></ion-icon>
                                  </span>
                                  <span class="file-label">
                                    Escolher foto…
                                  </span>
                                </span>
                        <span class="file-name">
                                    Nenhum arquivo selecionado
                                </span>
                    </label>
                </div>
            </div>
        @endif

        @if(isset($client) && !is_null($client->account_owner_document))
            <div class="column is-3">
                <label for="account_owner_document" class="label">CNH/RG do Titular da conta</label>
                <a href="/storage/{{ str_replace('public/', '', $client->account_owner_document) }}"
                   class="button is-danger" target="_blank">
                    <ion-icon name="eye-outline"></ion-icon>
                    Visualizar Documento</a>
            </div>
        @else
            <div class="column is-3">
                <label for="account_owner_document" class="label">CNH/RG do Titular da conta</label>
                <div class="file has-name" id="account_owner_document">
                    <label class="file-label">
                        <input class="file-input" type="file" name="account_owner_document">
                        <span class="file-cta">
                                  <span class="file-icon">
                                    <ion-icon name="folder-outline"></ion-icon>
                                  </span>
                                  <span class="file-label">
                                    Escolher foto…
                                  </span>
                                </span>
                        <span class="file-name">
                                    Nenhum arquivo selecionado
                                </span>
                    </label>
                </div>
            </div>
        @endif
    </div>
</div>
{{--            end client box--}}
<div class="box">
    <div class="columns">
        <h6 class="title">
            <ion-icon name="location-outline"></ion-icon> &nbsp;&nbsp; Endereço de instalação
        </h6>
    </div>

    <div class="columns">
        <div class="column is-3">
            <div class="field">
                <label for="zipcode" class="label">CEP*</label>
                <div class="control">
                    <input name="zipcode" id="zipcode"
                           class="input is-rounded @error('zipcode') is-danger @enderror" type="text"
                           placeholder="Digite o CEP"
                           value="{{ isset($client) ? $address->zipcode : '' }}">
                    @error('zipcode')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column is-4">
            <div class="field">
                <label for="street" class="label">Rua/Logradouro*</label>
                <div class="control">
                    <input name="street" id="street"
                           class="input is-rounded @error('street') is-danger @enderror" type="text"
                           placeholder="Digite a rua"
                           value="{{ isset($client) ? $address->street : '' }}">
                    @error('street')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column is-2">
            <div class="field">
                <label for="address_number" class="label">Número*</label>
                <div class="control">
                    <input name="address_number" id="address_number"
                           class="input is-rounded @error('address_number') is-danger @enderror"
                           type="text" value="{{ isset($client) ? $address->number : '' }}"
                           placeholder="Digite o número">
                    @error('address_number')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column is-3">
            <div class="field">
                <label for="complement" class="label">Complemento</label>
                <div class="control">
                    <input name="complement" id="complement" class="input is-rounded" type="text"
                           placeholder="Digite o complemento"
                           value="{{ isset($client) ? $address->complement : '' }}">
                </div>
            </div>
        </div>
    </div>
    <div class="columns">
        <div class="column is-3">
            <div class="field">
                <label for="neighborhood" class="label">Bairro*</label>
                <div class="control">
                    <input name="neighborhood" id="neighborhood"
                           class="input is-rounded @error('neighborhood') is-danger @enderror"
                           type="text" value="{{ isset($client) ? $address->neighborhood : '' }}"
                           placeholder="Digite o bairro">
                    @error('neighborhood')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column is-3">
            <div class="field">
                <label for="state" class="label">Estado*</label>
                <div
                    class="select is-multiline is-fullwidth is-rounded @error('state') is-danger @enderror">
                    <select id="state" name="state">
                        @foreach($states as $state)
                            <option
                                @if(isset($client) && $address->city->state->id == $state->id) selected
                                @endif value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                    @error('state')<span class="error-message">{{ $message }}</span>@enderror

                </div>
            </div>
        </div>
        <div class="column is-3">
            <div class="field">
                <label for="city" class="label">Cidade*</label>
                <div
                    class="select is-multiline is-fullwidth is-rounded @error('city') is-danger @enderror">
                    <select id="city" name="city">
                        <option selected>Selecione...</option>
                    </select>
                    {{--                                    @error('city')<span class="error-message">{{ $message }}</span>@enderror--}}
                </div>
            </div>
            @if(isset($client))
                <input type="text" value="{{ $cityId }}" id="city_id" style="display: none">
            @endif
        </div>
    </div>
</div>
{{--        end address box--}}

@if(!isset($client))

    <div class="box">
        <div class="columns">
            <h6 class="title">
                <ion-icon name="newspaper-outline"></ion-icon> &nbsp;&nbsp; UC de instalação
            </h6>
        </div>
        <div class="columns">
            <div class="column is-3">
                <div class="field">
                    <label for="uc_number" class="label">Unidade consumidora (N.º)</label>
                    <div class="control">
                        <input name="uc_number" id="uc_number"
                               value="{{ isset($client) && !is_null($consumerUnit) ? $consumerUnit->number : '' }}"
                               class="input is-rounded  @error('zipcode') is-danger @enderror"
                               type="text"
                               placeholder="Digite o  N.º da UC">
                        @error('uc_number')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="column is-3">
                <div class="field">
                    <label for="uc_type" class="label">Tipo</label>
                    <div
                        class="select is-multiline is-fullwidth is-rounded  @error('uc_type') is-danger @enderror">
                        <select id="uc_type" name="uc_type">
                            <option value="residential">Residencial</option>
                            <option @if(isset($client) && !is_null($consumerUnit)) selected
                                    @endif value="business">Empresarial
                            </option>
                        </select>
                        @error('uc_type')<span class="error-message">{{ $message }}</span>@enderror

                    </div>
                </div>
            </div>
            <div class="column is-6">
                <label for="electricity_bill" class="label">Conta de luz</label>
                <div class="file has-name" id="electricity_bill">
                    <label class="file-label">
                        <input class="file-input" type="file" name="electricity_bill">
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
    </div>
@endif

{{--            end uc box--}}
