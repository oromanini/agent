@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto proposal-single-shell">

            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Nova Proposta</h3>
            </div>
            <div class="columns">
                <div class="title-bottom-line" style="margin-left: 50px"></div>
            </div>
            <form action="{{ route('proposal.store') }}" method="post">
                @csrf
                <div class="columns">
                    <div class="column is-4">
                        <label for="client" class="label">Cliente</label>
                        <div class="field has-addons">
                            <div class="control is-expanded">
                                <select id="client" name="client">
                                    @foreach($clients as $client)
                                        <option value="{{$client->id}}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="control">
                                <a class="button is-info" href="{{ route('client.create') }}" title="Cadastrar novo cliente">
                                    <ion-icon name="person-add-outline"></ion-icon>
                                    <span class="ml-1">Novo</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label for="average_consumption" class="label">Média de consumo &nbsp;
                                <span data-tooltip="Para calcular a média de consumo, some o consumo dos 12 meses presentes
                                na conta de luz do seu cliente. Após somar, divida o valor total pela quantidade de meses. CUIDADO, algumas
                                contas têm 13 meses. Nesses casos, divida por 13.">
                                    <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                                </span>
                            </label>
                            <div class="control">
                                <input name="average_consumption" id="average_consumption"
                                       class="input  @error('average_consumption') is-danger @enderror"
                                       type="number"
                                       placeholder="Digite o consumo">
                                @error('average_consumption')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="kw_price" class="label">Valor do kW &nbsp;
                                <span data-tooltip="Para calcular o valor do kWh do seu cliente, divida o valor total da conta
                                pelo consumo do cliente daquele mês. Por exemplo, se o cliente gastou R$ 300 naquele mês, e consumiu
                                350 kWh, o valor do kWh será de R$ 0,85">
                                <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                                </span>
                            </label>
                            <div class="control">
                                <input name="kw_price" id="kw_price"
                                       class="input  @error('kw_price') is-danger @enderror" type="text"
                                       placeholder="Digite o valor do kW">
                                @error('kw_price')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label for="tension_pattern" class="label">Padrão de tensão
                                <span
                                    data-tooltip="Você pode encontrar a FASE e a TENSÃO do seu cliente na conta de luz">
                                    <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                                </span>
                            </label>
                            <div
                                class="select is-multiline is-fullwidth  @error('tension_pattern') is-danger @enderror">
                                <select id="tension_pattern" name="tension_pattern">
                                    @foreach($tensions as $tension)
                                        <option value="{{ $tension->value }}">{{ $tension->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('tension_pattern')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-3">
                        <div class="field">
                            <label for="installation_address" class="label">Endereço de instalação
                                <span id="installation_address_text">
                                    <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                                </span>
                            </label>
                            <div
                                class="select is-multiline is-fullwidth  @error('installation_address') is-danger @enderror">
                                <select id="installation_address" name="installation_address"></select>
                            </div>
                            @error('installation_address')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label for="installation_uc" class="label">U.C de instalação</label>
                            <div
                                class="select is-multiline is-fullwidth  @error('installation_uc') is-danger @enderror">
                                <select id="installation_uc" name="installation_uc">
                                    <option value="">Não há UC's cadastradas</option>
                                </select>
                            </div>
                            @error('installation_uc')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    @if(auth()->user()->isAdmin)
                        <div class="column is-3">
                            <div class="field">
                                <label for="agent" class="label">Agente</label>
                                <div
                                    class="select is-multiline is-fullwidth ">
                                    <select id="agent" name="agent">
                                        <option value="{{ auth()->user()->id }}">{{ auth()->user()->name }}</option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="columns" style="margin-top: 50px;">
                    <label for="roof_structure" class="label">Selecione o telhado</label>
                </div>
                <div class="columns" id="roof_div">
                    @foreach($roofs as $roof)
                        <div class="column">
                            <label>
                                <input type="radio" name="roof_structure" value="{{$roof['id']->value}}"
                                       class="radio-image roof-structure">
                                <img src="{{ $roof['image'] }}" width="170" class="roof-img">
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="columns is-flex is-justify-content-center" style="margin-top: 15px; margin-bottom: 30px">
                    <div class="column is-8">
                        <div id="orientation" class="orientation-segmented" role="radiogroup" aria-label="Orientação">
                            <label class="orientation-option">
                                <input name="orientation" type="radio" value="norte" checked>
                                <span>Norte</span>
                            </label>
                            <label class="orientation-option">
                                <input name="orientation" value="leste/oeste" type="radio" >
                                <span>Leste/Oeste</span>
                            </label>
                            <label class="orientation-option">
                                <input name="orientation" value="sul" type="radio" >
                                <span>Sul</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="column is-flex is-justify-content-center">
                    <span class="button is-medium is-info " id="kitSearchSubmit">
                        <ion-icon name="sunny-outline"></ion-icon>&nbsp;Buscar Kits
                    </span>
                </div>
                <hr style="margin: 10px">


                <div id="kits" class="columns is-flex is-justify-content-center is-flex-wrap-wrap"
                     style="padding: 25px 10px"></div>
                <hr>
                <div id="generateProposalButton" class="columns is-flex is-justify-content-center"
                     style="padding: 25px 0"></div>
            </form>
    </div>

    @include('proposals.form_script')
@endsection


