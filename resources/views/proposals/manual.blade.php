@extends('base')
@section('content')

    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Nova Proposta</h3>
            </div>
            <div class="columns">
                <div class="title-bottom-line" style="margin-left: 50px"></div>
            </div>
            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        <label for="client" class="label">Cliente*</label>
                        <div
                            class="select is-multiline is-fullwidth is-rounded @error('Client') is-danger @enderror">
                            <select id="client" name="client">
                                @forelse($clients as $client)
                                    <option value="{{ $client->id }}">{{$client->name}}</option>
                                @empty
                                    <option value="">Não há clientes cadastrados</option>
                                @endforelse
                            </select>
                        </div>
                        @error('type')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="column is-1">
                    <br>
                    <a class="button is-info" href="{{ route('client.create') }}"
                       style="padding: 2px 2px 2px 10px; margin-top: 5px">
                        <ion-icon name="person-add-outline"></ion-icon>
                    </a>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label for="average_consumption" class="label">Média de consumo &nbsp;
                            <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                        </label>
                        <div class="control">
                            <input name="average_consumption" id="average_consumption"
                                   class="input is-rounded @error('average_consumption') is-danger @enderror"
                                   type="number"
                                   placeholder="Digite o consumo" required>
                            @error('average_consumption')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label for="kw_price" class="label">Valor do kW &nbsp;
                            <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                        </label>
                        <div class="control">
                            <input name="kw_price" id="kw_price"
                                   class="input is-rounded @error('kw_price') is-danger @enderror" type="text"
                                   placeholder="Digite o valor do kW" required>
                            @error('kw_price')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <label for="tension_pattern" class="label">Padrão de tensão
                            <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                        </label>
                        <div
                            class="select is-multiline is-fullwidth is-rounded @error('tension_pattern') is-danger @enderror">
                            <select id="tension_pattern" name="tension_pattern">
                                @foreach($tensions as $key => $value)
                                    <option value="{{ $value }}">{{ $key }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('tension_pattern')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <div class="columns" style="margin-top: 50px">
                <label for="roof_structure" class="label">Selecione o telhado</label>
            </div>
            <div class="columns">
                @foreach($roofs as $roof)
                    <div class="column">
                        <label>
                            <input type="radio" name="roof_structure" value="{{$roof['id']}}" class="radio-image">
                            <img src="{{ $roof['image'] }}" width="200" class="roof-img">
                        </label>
                    </div>
                @endforeach
            </div>

            {{--            KIT--}}
            <div class="columns">

                <div class="column is-3">
                    <div class="field">
                        <label for="agent" class="label">Agente*</label>
                        <div
                            class="select is-multiline is-fullwidth is-rounded @error('user_id') is-danger @enderror">
                            <select id="agent" name="user_id">
                                @forelse($agents as $agent)
                                    <option value="{{ $agent->id }}">{{$agent->name}}</option>
                                @empty
                                    <option value="">Não há agentes cadastrados</option>
                                @endforelse
                            </select>
                        </div>
                        @error('user_id')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label for="kwp" class="label">Potência(kWp) &nbsp;
                            <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                        </label>
                        <div class="control">
                            <input name="kwp" id="kwp"
                                   class="input is-rounded @error('kwp') is-danger @enderror" type="text"
                                   placeholder="Digite o kWp" required>
                            @error('kwp')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label for="cost" class="label">Custo &nbsp;
                            <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                        </label>
                        <div class="control">
                            <input name="cost" id="cost"
                                   class="input is-rounded @error('cost') is-danger @enderror" type="text"
                                   placeholder="Digite o Custo" required>
                            @error('cost')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label for="final_value" class="label">Valor final &nbsp;
                            <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                        </label>
                        <div class="control">
                            <input name="final_value" id="final_value"
                                   class="input is-rounded @error('final_value') is-danger @enderror" type="text"
                                   placeholder="Digite o Custo" required>
                            @error('final_value')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <label for="panel_quantity" class="label">Total de painéis &nbsp;
                            <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                        </label>
                        <div class="control">
                            <input name="panel_quantity" id="panel_quantity"
                                   class="input is-rounded @error('panel_quantity') is-danger @enderror" type="number"
                                   placeholder="Qtd Paineis" required>
                            @error('panel_quantity')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

            </div>
            <div class="columns">

                <div class="column is-3">
                    <div class="field">
                        <label for="panel" class="label">Painel*</label>
                        <div
                            class="select is-multiline is-fullwidth is-rounded @error('panel') is-danger @enderror">
                            <select id="panel" name="panel">
                                @forelse($panels as $key => $value)
                                    <option value="{{ $key }}">{{$value[0] . ' ' . $value[0] . ' W'}}</option>
                                @empty
                                    <option value="">Não há painéis cadastrados</option>
                                @endforelse
                            </select>
                        </div>
                        @error('panel')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="column is-3">
                    <div class="field">
                        <label for="inverter" class="label">Inversor*</label>
                        <div
                            class="select is-multiline is-fullwidth is-rounded @error('inverter') is-danger @enderror">
                            <select id="inverter" name="inverter">
                                @forelse($inverters as $key => $value)
                                    <option value="{{ $key }}">{{$value}}</option>
                                @empty
                                    <option value="">Não há inversores cadastrados</option>
                                @endforelse
                            </select>
                        </div>
                        @error('inverter')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>

            </div>

            <div class="columns">
                <div class="column is-12">
                    <label for="components" class="label">Componentes</label>
                    <textarea id="components" name="components" class="textarea"
                              placeholder="cole aqui os componentes"></textarea>
                </div>
            </div>

            <div class="column is-flex is-justify-content-center">
                <button class="button is-medium is-primary">
                    <ion-icon name="save-outline"></ion-icon>&nbsp;Salvar
                </button>
            </div>

        </div>
    </div>

@endsection
