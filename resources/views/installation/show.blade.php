@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Instalação</h3>
            </div>
            <br>
            <div class="columns">
                <div class="column is-4">
                    <span class="tag is-info is-light" style="font-size: 16pt">
                        {{ 'Proposta #' . $installation->proposal->id . ' - ' .$installation->proposal->client->name }}
                    </span>
                </div>
                <div class="column is-4">
                    <span class="tag is-info" style="font-size: 16pt">
                        {{ 'Status: ' . $installation->status->name }}
                    </span>
                </div>
            </div>
            <br>
            <form action="{{ route('installation.update', [$installation->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="columns">
                    <div class="column is-3">
                        <div class="field">
                            <label id="installation_forecast_label" for="installation_forecast" class="label">
                                Previsão de instalação</label>
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
                </div>
                <div class="columns">
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
                                    <input class="file-input" type="file" name="ca_proof_of_payment" id="ca_proof_of_payment">
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
                        <label for="installation_proof_of_payment" class="label">Comprovante Pagamento Instalação</label>
                        @if(isset($installation->installation_proof_of_payment))
                            <a href="/storage/{{ str_replace('public/', '', $installation->installation_proof_of_payment) }}"
                               class="button is-danger" target="_blank">
                                <ion-icon name="eye-outline"></ion-icon>
                                Visualizar NF</a>
                        @else
                            <div class="file has-name">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="installation_proof_of_payment" id="installation_proof_of_payment">
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
                                    <input class="file-input" type="file" name="installation_invoice" id="installation_invoice">
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
                        <p>{{ \App\Enums\RoofStructure::getDescription($installation->proposal->roof_structure) }}</p>
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
                <hr>
                <div class="columns">
                   <div class="column is-4">
                       <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Custos Adicionais</h3>
                   </div>
                </div>
                <div class="columns">
                    <div class="column is-3">
                        <div class="field">
                            <label for="plus_cost_description" class="label">Descrição do custo</label>
                            <div class="control">
                                <input name="plus_cost_description" id="plus_cost_description"
                                       class="input @error('plus_cost_description') is-danger @enderror" type="text"
                                       placeholder="Digite a descrição do custo"
                                       value="{{ $installation->plus_cost_description }}">
                                @error('plus_cost_description')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label for="plus_cost_value" class="label">Valor do custo</label>
                            <div class="control">
                                <input name="plus_cost_value" id="plus_cost_value"
                                       class="input @error('plus_cost_value') is-danger @enderror" type="text"
                                       placeholder="Digite o valor do custo"
                                       value="{{ $installation->plus_cost_value }}">
                                @error('plus_cost_value')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-3">
                        <label class="label" for="add_cost"> &nbsp;</label>
                        <button type="submit" id="add_cost" class="button is-danger">
                            <ion-icon name="add-circle"></ion-icon> &nbsp;Acrescentar
                        </button>
                    </div>
                </div>
                @include('installation.plusCostTable')
                <hr>
                @include('installation.images')
            </form>
        </div>
    </div>
@endsection
