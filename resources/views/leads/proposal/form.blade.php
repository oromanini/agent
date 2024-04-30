@extends('base')

@section('content')

    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Nova Proposta para LEAD</h3>
            </div>
            <div class="columns">
                <div class="title-bottom-line" style="margin-left: 50px"></div>
            </div>
            <form action="{{ route('leads.store') }}" method="post">
                @csrf
                <div class="columns">
                    <div class="column is-3">
                        <label for="lead" class="label">Nome do lead</label>
                        <input required class="input" type="text" id="lead_name" name="lead_name">
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
                                       required
                                       type="number"
                                       placeholder="Digite o consumo">
                                @error('average_consumption')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label for="kwh_price" class="label">Valor do kW &nbsp;
                                <span data-tooltip="Para calcular o valor do kWh do seu cliente, divida o valor total da conta
                                pelo consumo do cliente daquele mês. Por exemplo, se o cliente gastou R$ 300 naquele mês, e consumiu
                                350 kWh, o valor do kWh será de R$ 0,85">
                                <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                                </span>
                            </label>
                            <div class="control">
                                <input name="kwh_price" id="kwh_price"
                                       value="0.82"
                                       required
                                       class="input  @error('kwh_price') is-danger @enderror" type="text"
                                       placeholder="Digite o valor do kW">
                                @error('kwh_price')<span class="error-message">{{ $message }}</span>@enderror
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
                        <label for="telefone" class="label">Telefone/whatsapp</label>
                        <input required class="input" type="text" id="phone_number" name="phone_number">
                    </div>
                    <div class="column is-3">
                        <label for="state" class="label">Estado*</label>
                        <div
                            class="select is-multiline is-fullwidth @error('state') is-danger @enderror">
                            <select id="state" name="state">
                                @foreach($states as $state)
                                    <option @if($state->name == 'PARANÁ') selected @endif
                                    value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                            @error('state')<span class="error-message">{{ $message }}</span>@enderror
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
                                                                    @error('city')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        @if(isset($client))
                            <input type="text" value="{{ $cityId }}" id="city_id" style="display: none">
                        @endif
                    </div>

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

    </div>

    @include('leads.proposal.form_script')
@endsection
