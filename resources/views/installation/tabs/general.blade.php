<form action="{{ route('installation.update', [$installation->id]) }}" method="post"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="columns">
        <div class="column is-3">
            <div class="field">
                <label id="installation_forecast_label" for="installation_forecast" class="label">
                    Agendamento da instalação</label>
                <div class="control">
                    <input name="installation_forecast" id="installation_forecast"
                           class="input @error('installation_forecast') is-danger @enderror" type="date"
                           value="{{ isset($installation->installation_forecast) ? $installation->installation_forecast->toDateString() : '' }}">
                    @error('protocol_approval_date')<span
                        class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div class="column is-3">
            <div class="field">
                <label for="ca_cost" class="label">Custo do C.A</label>
                <div class="control">
                    <input name="ca_cost" id="ca_cost"
                           class="input @error('ca_cost') is-danger @enderror" type="text"
                           placeholder="Digite o Custo do CA"
                           value="{{ $installation->ca_cost }}">
                    @error('ca_cost')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div class="column is-3">
            <label for="ca_proof_of_payment" class="label">Comprovante Pagamento C.A</label>
            @if(isset($installation->ca_proof_of_payment))
                <a href="/storage/{{ str_replace('public/', '', $installation->ca_proof_of_payment) }}"
                   class="button is-danger" target="_blank">
                    <ion-icon name="eye-outline"></ion-icon>
                    Visualizar NF</a>
            @else
                <div class="file has-name">
                    <label class="file-label">
                        <input class="file-input" type="file" name="ca_proof_of_payment"
                               id="ca_proof_of_payment">
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
        <div class="column is-3">
            <label for="ca_invoice" class="label">NF C.A</label>
            @if(isset($installation->ca_invoice))
                <a href="/storage/{{ str_replace('public/', '', $installation->ca_invoice) }}"
                   class="button is-danger" target="_blank">
                    <ion-icon name="eye-outline"></ion-icon>
                    Visualizar NF</a>
            @else
                <div class="file has-name">
                    <label class="file-label">
                        <input class="file-input" type="file" name="ca_invoice" id="ca_invoice">
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
        <div class="column is-3">
            <div class="field">
                <label for="installation_cost" class="label">Custo da instalação</label>
                <div class="control">
                    <input name="installation_cost" id="installation_cost"
                           class="input @error('installation_cost') is-danger @enderror" type="text"
                           placeholder="Digite o Custo da instalação"
                           value="{{ $installation->installation_cost }}">
                    @error('installation_cost')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div class="column is-3">
            <label for="installation_proof_of_payment" class="label">Comprovante Pagamento
                Instalação</label>
            @if(isset($installation->installation_proof_of_payment))
                <a href="/storage/{{ str_replace('public/', '', $installation->installation_proof_of_payment) }}"
                   class="button is-danger" target="_blank">
                    <ion-icon name="eye-outline"></ion-icon>
                    Visualizar NF</a>
            @else
                <div class="file has-name">
                    <label class="file-label">
                        <input class="file-input" type="file" name="installation_proof_of_payment"
                               id="installation_proof_of_payment">
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
        <div class="column is-3">
            <label for="installation_invoice" class="label">NF Instalação</label>
            @if(isset($installation->installation_invoice))
                <a href="/storage/{{ str_replace('public/', '', $installation->installation_invoice) }}"
                   class="button is-danger" target="_blank">
                    <ion-icon name="eye-outline"></ion-icon>
                    Visualizar NF</a>
            @else
                <div class="file has-name">
                    <label class="file-label">
                                 <input class="file-input" type="file" name="installation_invoice"
                                          id="installation_invoice">
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
        <div class="column is-2">
            <div class="field">
                <label id="installation_date_label" for="installation_date" class="label">
                    Data de instalação</label>
                <div class="control">
                    <input name="installation_date" id="installation_date"
                           class="input @error('installation_date') is-danger @enderror" type="date"
                           value="{{ isset($installation->installation_date) ? $installation->installation_date->toDateString() : '' }}">
                    @error('installation_date')<span
                        class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="columns">
        <div class="column is-3">
            <div class="field">
                <label for="monitoring_login" class="label">Usuário Monitoramento</label>
                <div class="control">
                    <input name="monitoring_login" id="monitoring_login"
                           class="input @error('monitoring_login') is-danger @enderror" type="text"
                           placeholder="Digite o login"
                           value="{{ $installation->monitoring_login }}">
                    @error('monitoring_login')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div class="column is-3">
            <div class="field">
                <label for="monitoring_password" class="label">Monitoramento Senha</label>
                <div class="control">
                    <input name="monitoring_password" id="monitoring_password"
                           class="input @error('monitoring_password') is-danger @enderror" type="text"
                           placeholder="Digite a senha"
                           value="{{ $installation->monitoring_password }}">
                    @error('monitoring_password')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div class="column is-3">
            <div class="field">
                <label for="monitoring_app" class="label">Aplicativo utilizado</label>
                <div
                    class="select is-multiline @error('monitoring_app') is-danger @enderror">
                    <select id="monitoring_app" name="monitoring_app">
                        @foreach($appList as $app)
                            <option {{ $app->value === $installation->monitoring_app ? 'selected' : '' }} value="{{ $app->value }}">{{ $app->value }}</option>
                        @endforeach
                    </select>
                </div>
                @error('monitoring_app')<span class="error-message">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>
    <hr>
    <div class="columns">
        <div class="column is-3">
            <label for="" class="label">Diagrama Unifilar</label>
            <a href="/storage/{{ str_replace('public/', '', $installation->proposal->homologation->single_line_project) }}"
               class="button is-danger" target="_blank">
                <ion-icon name="eye-outline"></ion-icon>
                Ver Unifilar</a>
        </div>
        <div class="column is-3">
            <label for="" class="label">Vistoria </label>
            <a class="button is-danger"
               href="{{ route('approval.show', [$installation->proposal->id]) . '#technical' }}">
                <ion-icon name="eye-outline"></ion-icon>
                Ver vistoria
            </a>
        </div>
        <div class="column is-3">
            <label for="" class="label">Tipo de telhado</label>
            <p>{{ \App\Enums\RoofStructure::translateExternalRoof($installation->proposal->roof_structure)->value }}</p>
        </div>
        <div class="column is-3">
            <label for="" class="label">Tensão do cliente</label>
            <p>{{ $installation->proposal->tension_pattern }}</p>
        </div>
    </div>
    <div class="columns p-2 pt-4">
        <div class="column">
            @include('proposals.show.kit_data')
        </div>
    </div>
    <div class="columns is-center is-justify-content-center p-6">
        <button class="button is-primary is-large has-icon" type="submit">
            <span class="icon"><ion-icon name="save-outline"></ion-icon></span>
            &nbsp;&nbsp;Salvar
        </button>
    </div>
</form>
