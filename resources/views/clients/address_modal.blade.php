{{--    ADDRESS MODAL--}}

<div id="address_modal" class="modal">
    <div class="modal-background"></div>

    <div class="modal-content">
        <div class="box">
            <h5 class="title is-5">Novo endereço</h5>
            <form action="{{ route('address.store', [$client->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                @if(!is_null($addresses->first()->consumer_unit_id))
                    <div class="columns">
                        <div class="column is-4">
                            <div class="field">
                                <label for="zipcode" class="label">CEP*</label>
                                <div class="control">
                                    <input name="zipcode" id="zipcode"
                                           class="input is-rounded" type="text"
                                           placeholder="Digite o CEP"
                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="column is-8">
                            <div class="field">
                                <label for="street" class="label">Rua/Logradouro*</label>
                                <div class="control">
                                    <input name="street" id="street"
                                           class="input is-rounded" type="text"
                                           placeholder="Digite a rua"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column is-4">
                            <div class="field">
                                <label for="address_number" class="label">Número*</label>
                                <div class="control">
                                    <input name="address_number" id="address_number"
                                           class="input is-rounded"
                                           type="text"
                                           required
                                           placeholder="Digite o número">
                                </div>
                            </div>
                        </div>
                        <div class="column is-8">
                            <div class="field">
                                <label for="complement" class="label">Complemento</label>
                                <div class="control">
                                    <input name="complement" id="complement" class="input is-rounded" type="text"
                                           placeholder="Digite o complemento"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column is-4">
                            <div class="field">
                                <label for="neighborhood" class="label">Bairro*</label>
                                <div class="control">
                                    <input name="neighborhood" id="neighborhood"
                                           class="input is-rounded "
                                           type="text"
                                           placeholder="Digite o bairro" required>
                                </div>
                            </div>
                        </div>
                        <div class="column is-4">
                            <div class="field">
                                <label for="state2" class="label">Estado*</label>
                                <div
                                    class="select is-multiline is-fullwidth is-rounded">
                                    <select id="state2" name="state">
                                        @foreach($states as $state)
                                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="column is-4">
                            <div class="field">
                                <label for="city2" class="label">Cidade*</label>
                                <div
                                    class="select is-multiline is-fullwidth is-rounded">
                                    <select id="city2" name="city">
                                        <option selected>Selecione...</option>
                                    </select>
                                    {{--                                    @error('city')<span class="error-message">{{ $message }}</span>@enderror--}}
                                </div>
                            </div>
                            <input type="text" value="{{ $cityId }}" id="city_id2" style="display: none">
                        </div>
                    </div>
                @endif
                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <label for="uc_number" class="label">Unidade consumidora (N.º)</label>
                            <div class="control">
                                <input name="uc_number" id="uc_number"
                                       class="input is-rounded"
                                       type="text"
                                       placeholder="Digite o  N.º da UC">
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <label for="uc_type" class="label">Tipo</label>
                            <div
                                class="select is-multiline is-fullwidth is-rounded">
                                <select id="uc_type" name="uc_type">
                                    <option value="residential">Residencial</option>
                                    <option value="business">Empresarial</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-12">
                        <label for="electricity_bill" class="label">Conta de luz</label>
                        <div class="file has-name">
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
                <hr>
                <div class="columns is-flex is-justify-content-center">
                    <button type="submit" class="button is-info">
                        <ion-icon name="save-outline"></ion-icon> &nbsp; Salvar
                    </button>
                    &nbsp;
                    <button type="button" id="close-modal" class="button is-info is-light" aria-label="close">Fechar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    $('#close-modal').on('click', function () {
        $('#address_modal').removeClass('is-active');
    })

</script>
