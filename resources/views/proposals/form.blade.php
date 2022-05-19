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
            <hr>
            <div class="column is-flex is-justify-content-center">
                <button class="button is-medium is-primary">
                    <ion-icon name="sunny-outline"></ion-icon>&nbsp;Buscar Kits</button>
            </div>
            <hr>
        </div>

{{--        KITS--}}

        <div id="kits" class="columns"></div>

    </div>
@endsection
