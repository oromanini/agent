@extends('base')
@section('content')

    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <form action="{{ route('proposal.manual.store') }}" method="post">
                @csrf

                <div class="columns mt-2 ml-1">
                    <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Nova Proposta</h3>
                </div>
                <div class="columns">
                    <div class="title-bottom-line" style="margin-left: 50px"></div>
                </div>
                <div class="columns">
                    <div class="column is-3">
                        @if(!$clients->isEmpty())
                            <div class="field">
                                <label for="client" class="label">Cliente*</label>
                                <div
                                    class="select is-multiline is-fullwidth  @error('Client') is-danger @enderror">
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
                        @else
                            <label for="client" class="label">Cliente*</label>
                            <a href="{{ route('client.create') }}" class="button is-primary">Cadastrar cliente</a>
                        @endif
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="average_consumption" class="label">Média de consumo &nbsp;</label>
                            <div class="control">
                                <input name="average_consumption" id="average_consumption"
                                       class="input  @error('average_consumption') is-danger @enderror"
                                       type="number"
                                       placeholder="Digite o consumo" required>
                                @error('average_consumption')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="kw_price" class="label">Valor do kW &nbsp;</label>
                            <div class="control">
                                <input name="kw_price" id="kw_price"
                                       class="input  @error('kw_price') is-danger @enderror" type="text"
                                       placeholder="Digite o valor do kW" required>
                                @error('kw_price')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="tension_pattern" class="label">Padrão de tensão</label>
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
                    <div class="column is-2">
                        <div class="field">
                            <label for="incidence" class="label">Incidência solar</label>
                            <p id="incidence-field" style="font-size: 20pt; color: orangered"></p>
                            <button id="incidence_loader" style="border: none" class="button is-loading">Loading</button>
                        </div>
                    </div>
                </div>

                <div class="columns" style="margin-top: 50px">
                    <label for="roof_structure" class="label">Selecione o telhado</label>
                </div>
                <div class="columns" style="margin-bottom: 10px">
                    @foreach($roofs as $roof)
                        <div class="column">
                            <label>
                                <input type="radio" name="roof_structure" value="{{$roof['id']->value}}" class="radio-image">
                                <img src="{{ $roof['image'] }}" width="200" class="roof-img">
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="columns is-flex is-justify-content-center"
                     style="margin-top: 15px; margin-bottom: 30px">
                    <div class="column is-6 is-flex is-justify-content-space-around is-align-items-center is-warning"
                         style="border: 2px solid #f2a714; border-radius: 100px;">
                        <label class="checkbox">
                            <input name="orientation" type="radio" value="norte"
                            > Norte
                        </label>
                        <label class="checkbox">
                            <input name="orientation" value="leste/oeste" type="radio"
                            > Leste/Oeste
                        </label>
                        <label class="checkbox">
                            <input name="orientation" value="sul" type="radio"
                            > Sul
                        </label>
                    </div>
                </div>

                {{--            KIT--}}
                <div class="columns">

                    <div class="column is-3">
                        <div class="field">
                            <label for="agent" class="label">Agente*</label>
                            <div
                                class="select is-multiline is-fullwidth  @error('user_id') is-danger @enderror">
                                <select id="agent" name="agent">
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
                            <label for="kwp" class="label">Potência(kWp) &nbsp;</label>
                            <div class="control">
                                <input name="kwp" id="kwp"
                                       class="input  @error('kwp') is-danger @enderror" type="text"
                                       placeholder="Digite o kWp" required>
                                @error('kwp')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="cost" class="label">Custo &nbsp;</label>
                            <div class="control">
                                <input name="cost" id="cost"
                                       class="input  @error('cost') is-danger @enderror" type="text"
                                       placeholder="Digite o Custo" required>
                                @error('cost')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="final_value" class="label">Valor final &nbsp;</label>
                            <div class="control">
                                <input name="final_value" id="final_value"
                                       class="input  @error('final_value') is-danger @enderror" type="text"
                                       placeholder="Digite o Custo" required>
                                @error('final_value')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="panel_quantity" class="label">Total de painéis &nbsp;</label>
                            <div class="control">
                                <input name="panel_quantity" id="panel_quantity"
                                       class="input  @error('panel_quantity') is-danger @enderror"
                                       type="number"
                                       placeholder="Qtd Paineis" required>
                                @error('panel_quantity')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                </div>
                <div class="columns">
                    <div class="column is-2">
                        <div class="field">
                            <label for="panel_brand" class="label">Marca do Painel*</label>
                            <div
                                class="select is-multiline is-fullwidth  @error('panel_brand') is-danger @enderror">
                                <select id="panel_brand" name="panel_brand">
                                    @forelse($panels as $panel)

                                        <option {{}} value="{{ $panel->brand_enum }}">{{$panel->name}}</option>
                                    @empty
                                        <option value="">Não há painéis cadastrados</option>
                                    @endforelse
                                </select>
                            </div>
                            @error('panel_brand')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="panel_model" class="label">Modelo do painel</label>
                            <div class="control">
                                <input name="panel_model" id="panel_model"
                                       class="input  @error('panel_model') is-danger @enderror" type="text"
                                       placeholder="AKJH-28SIJ" required>
                                @error('panel_model')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="panel_power" class="label">Potência do painel</label>
                            <div class="control">
                                <input name="panel_power" id="panel_power"
                                       class="input  @error('panel_power') is-danger @enderror" type="number"
                                       placeholder="550" required>
                                @error('panel_power')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="panel_warranty" class="label">Garantia do painel</label>
                            <div class="control">
                                <input name="panel_warranty" id="panel_warranty"
                                       class="input  @error('panel_warranty') is-danger @enderror"
                                       type="number"
                                       placeholder="12" required>
                                @error('panel_warranty')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-2">
                        <div class="field">
                            <label for="inverter_brand" class="label">Marca do Inversor*</label>
                            <div
                                class="select is-multiline is-fullwidth  @error('inverter_brand') is-danger @enderror">
                                <select id="inverter_brand" name="inverter_brand">
                                    @forelse($inverters as $inverter)
                                        <option value="{{ $inverter->brand_enum }}">{{$inverter->name}}</option>
                                    @empty
                                        <option value="">Não há inversores cadastrados</option>
                                    @endforelse
                                </select>
                            </div>
                            @error('inverter_brand')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="inverter_model" class="label">Modelo do inversor</label>
                            <div class="control">
                                <input name="inverter_model" id="inverter_model"
                                       class="input  @error('inverter_model') is-danger @enderror" type="text"
                                       placeholder="MIC-3000" required>
                                @error('inverter_model')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="inverter_power" class="label">Potência do inversor</label>
                            <div class="control">
                                <input name="inverter_power" id="inverter_power"
                                       class="input  @error('inverter_power') is-danger @enderror"
                                       type="text"
                                       placeholder="3" required>
                                @error('inverter_power')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="inverter_warranty" class="label">Garantia do Inversor</label>
                            <div class="control">
                                <input name="inverter_warranty" id="inverter_warranty"
                                       class="input  @error('inverter_warranty') is-danger @enderror"
                                       type="number"
                                       placeholder="10" required>
                                @error('inverter_warranty')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="inverter_quantity" class="label">Qtd. de Inversores</label>
                            <div class="control">
                                <input name="inverter_quantity" id="inverter_quantity"
                                       class="input  @error('inverter_quantity') is-danger @enderror"
                                       type="number"
                                       placeholder="1" value="1" required>
                                @error('inverter_quantity')<span class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <label for="inverter_overload" class="label">Overload</label>
                            <div class="control has-icons-left">
                                <input name="inverter_overload" id="inverter_overload"
                                       class="input  @error('inverter_overload') is-danger @enderror"
                                       type="number" value="50"
                                       placeholder="50" required>
                                @error('inverter_overload')<span class="error-message">{{ $message }}</span>@enderror
                                <span class="icon is-small is-left">
                                    <i>%</i>
                                </span>
                            </div>
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
                    <button class="button is-large is-primary" type="submit">
                        <ion-icon name="save-outline"></ion-icon>&nbsp;Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(function () {

            let url = '/incidenceByClientId/';

            setIncidence(url);

            $("#client").selectize({});
            $("#agent").selectize({});

            $('#client').on('change', function () {
                setIncidence(url);
            })


            function setIncidence(url) {

                $.ajax({

                    url : url + $('#client').find(":selected").val(),
                    type : 'GET',
                    dataType:'json',
                    beforeSend: function () {
                        $('#incidence-field').hide()
                        $('#incidence_loader').show()
                    },
                    success : function(data) {
                        $('#incidence_loader').hide()
                        $('#incidence-field').show()
                        $('#incidence-field').html(data)
                    },
                    error : function(request,error)
                    {
                        // alert("Request: "+JSON.stringify(request));
                    }
                });
            }
        })
    </script>

@endsection
