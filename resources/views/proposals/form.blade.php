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
            <form action="{{ route('proposal.store') }}" method="post">
                @csrf
                <div class="columns">
                    <div class="column is-3">

                        <label for="client" class="label">Cliente</label>
                        <select id="client" name="client">
                            @foreach($clients as $client)
                                <option value="{{$client->id}}">{{ $client->name }}</option>
                            @endforeach
                        </select>


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
                <div class="columns is-flex is-justify-content-center"
                     style="margin-top: 15px; margin-bottom: 30px">
                    <div class="column is-6 is-flex is-justify-content-space-around is-align-items-center is-warning"
                         style="border: 2px solid #f2a714; border-radius: 100px;">
                        <label class="checkbox">
                            <input name="orientation" type="radio" value="norte" checked>
                            Norte
                        </label>
                        <label class="checkbox">
                            <input name="orientation" value="leste/oeste" type="radio">
                            Leste/Oeste
                        </label>
                        <label class="checkbox">
                            <input name="orientation" value="sul" type="radio">
                            Sul
                        </label>
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

    </div>

    {{--    KIT SEARCH--}}

    <script>

        $(function () {

            $("#client").selectize({});
            $("#agent").selectize({});

            let consumption = null;
            let clientId = $('select[name=client] option').filter(':selected').val();

            $('#average_consumption').on('change', function () {
                consumption = $('#average_consumption').val();
            })

            Address.setAddresses(clientId)
            Address.setUcs(clientId)
            ScreenHelper.disableSubmitIfConsumptionIsNull()

            $('#client').change(function () {
                clientId = $('select[name=client] option').filter(':selected').val();
                Address.setAddresses(clientId)
                Address.setUcs(clientId)
            });

            $('#average_consumption').change(function () {
                ScreenHelper.disableSubmitIfConsumptionIsNull()
            });

            $('#kitSearchSubmit').on('click', function () {
                $('#loader').show()
                let addressId = $('select[name=installation_address] option').filter(':selected').val()
                let consumption = $('#average_consumption').val();
                let incidence = Address.setIncidence(addressId);
                let orientation = $("input[name=orientation]:checked").val();
                let kwp = Kit.setKwp(consumption, incidence, orientation);
                let roof = $("input[name=roof_structure]:checked").val();
                let tension = $('select[name=tension_pattern] option').filter(':selected').val()

                $.ajax({
                    url: "/kitSearch/" + kwp.toFixed(2) + '/' + roof + '/' + tension,
                    type: 'get',
                    beforeSend: function () {
                        $('#loader').removeClass('disable');
                        $('#loader').addClass('enable');
                    },
                })
                    .done(function (msg) {
                        setTimeout(function () {
                            $('#loader').hide()
                        }, 2000)
                        $('#kits').empty();
                        $('#generateProposalButton').empty();

                        $.each(msg, function (i, distributor_kits) {

                            distributor_kits.sort((a, b) => a.cost - b.cost);

                            $.each(distributor_kits, function (j, item) {

                                let panelSpecs = JSON.parse(item.panel_specs);
                                let inverterSpecs = JSON.parse(item.inverter_specs);
                                let banks = '/img/banks/banks.png';

                                let panelCount = Kit.setPanelCount(item);

                                let finalValue = Kit.calculateFinalValue(
                                    item.cost,
                                    item.kwp,
                                    roof,
                                    panelCount,
                                    addressId,
                                    panelSpecs['brand'],
                                    panelSpecs['power'],
                                    inverterSpecs['brand']
                                );

                                let isPromotional = finalValue.isPromotional;
                                let isPromotionalText = isPromotional ? '* PROMOÇÃO *' : 'Preço padrão';
                                let isPromotionalColor = isPromotional ? 'is-success' : 'is-warning';

                                let isPromo = '';
                                let inverterBrand = inverterSpecs.brand === 'SAJ Microinverter' ? 'SAJ MICRO' : inverterSpecs.brand;
                                let averageProduction = Kit.calculateAverageProduction(addressId, item.kwp);

                                $('#kits').append(
                                    '<div class="column is-3">' +
                                    '<label>' +
                                    '<input type="radio" name="kit_id" value="' + item.distributor_code + '">' +
                                    '<div id="all" class="my-box-shadow">' +
                                    '<span class="tag ' + isPromotionalColor + '">' +
                                    isPromotionalText +
                                    '</span>' +
                                    '<div class="is-flex is-justify-content-center">' +
                                    '<img src="' + ScreenHelper.getPanelImage(panelSpecs.brand) + '" alt="" width="100">' +
                                    '<img src="' + ScreenHelper.getInverterImage(inverterSpecs.brand) + '" alt="" width="100">' +
                                    '</div>' +
                                    '<div style="display:flex; justify-content: center; text-align: center; font-size: 14pt; color: #6b7280; font-weight: 900; margin: 20px 0px">' +
                                    item.kwp + ' kWp' +
                                    '</div>' +
                                    '<div style="font-size: 10pt; text-align: center">' +
                                    '<strong>Geração aproximada de ' + averageProduction + ' kWh/mês</strong>' +
                                    '</div>' +
                                    '<hr>' +
                                    '<div style="text-align: center">' +
                                    '<strong>Painel: </strong>' + panelCount + ' x ' + panelSpecs.brand + ' ' + panelSpecs.power + 'W ' +
                                    '</div>' +
                                    '<div style="text-align: center">' +
                                    '<strong>Garantia: </strong>' + Kit.setWarranty(panelSpecs['panel_brand']) +
                                    '</div>' +
                                    '<hr>' +
                                    '<div style="text-align: center">' +
                                    '<strong>Inversor: </strong>' + inverterBrand + ' ' + inverterSpecs.power + ' KW' +
                                    '<div style="text-align: center">' +
                                    '<strong>Tensão: </strong>' + Kit.setStringTensionPattern(item.tension_pattern) +
                                    '</div>' +
                                    '</div>' +
                                    '<hr>' +
                                    isPromo +
                                    '<div style="color: #6BC6A7; font-size: 18pt; text-align: center; font-weight: bold">' +
                                    parseFloat(finalValue.finalPrice).toLocaleString('pt-BR', {
                                        style: 'currency',
                                        currency: 'BRL',
                                    }) +
                                    '</div>' +
                                    '<div style="text-align: center">' +
                                    '<br>' +
                                    '<strong style="color: darkred">Chegada em estoque: </strong>' + DateHelper.databaseDateToString(item.availability) +
                                    '</div>' +
                                    '<hr/>' +
                                    '<div style="display: flex; justify-content: center"><img src="' + banks + '" alt="..." width="230" style="a"></div>' +
                                    '</div>' +
                                    '</label>' +
                                    '</div>'
                                );
                            });
                        });
                        $('#generateProposalButton').append(
                            '<div class="columns"><button type="submit" class="button is-primary is-large">Gerar Proposta</button></div>'
                        );

                    })

                    .fail(function (jqXHR, textStatus, msg) {
                        console.log('FALHA AO TRAZER KITS: ' + msg);
                    });
            });
        });

        class Address {

            static setAddresses(clientId) {

                let url = '/addressesFromClientId/';

                $.ajax({
                    url: url + clientId,
                    type: 'get',
                    beforeSend: function () {
                        console.log("ENVIANDO...");
                    }
                })
                    .done(function (msg) {
                        $('#installation_address').empty();

                        $.each(msg, function (i, item) {
                            $('#installation_address').append($('<option>', {
                                value: item.id,
                                text: item.street + ' ' + item.number
                            }));
                        });
                        $('#installation_address_text').attr('data-tooltip', 'Incidência: ' + Address.setIncidence(msg[0].id))
                    })
                    .fail(function (jqXHR, textStatus, msg) {
                        console.log('FALHA AO CALCULAR INCIDENCIA: ' + msg);
                    });
            }

            static setUcs(clientId) {

                let url = '/ucsFromClientId/';

                $.ajax({
                    url: url + clientId,
                    type: 'get',
                    beforeSend: function () {
                        console.log("ENVIANDO...");
                    }
                })
                    .done(function (msg) {
                        if (msg != '') {
                            $('#installation_uc').empty();


                            $.each(msg, function (i, item) {
                                $('#installation_uc').append($('<option>', {
                                    value: item.id,
                                    text: 'U.C' + item.number
                                }));
                            });
                        }
                    })
                    .fail(function (jqXHR, textStatus, msg) {
                        console.log('FALHA AO SETAR UC: ' + msg);
                    });
            }

            static setIncidence(addressId) {
                let url = '/incidenceFromAddressId/';
                let incidence = 1;

                $.ajax({
                    url: url + addressId,
                    type: 'get',
                    async: false,
                    beforeSend: function () {
                        console.log("ENVIANDO...");
                    }
                })
                    .done(function (msg) {
                        incidence = msg;
                    })
                    .fail(function (jqXHR, textStatus, msg) {
                        console.log('FALHA AO SETAR INCIDENCIA: ' + msg);
                    });

                return incidence;
            }
        }

        class ScreenHelper {

            static disableSubmitIfConsumptionIsNull() {

                if (!$('#average_consumption').val() && !$('input:radio[name="roof_structure"]').is(':checked') && !$('#orientation input:checked').length > 0) {
                    $('#kitSearchSubmit').attr("disabled", "disabled");
                } else {
                    $('#kitSearchSubmit').removeAttr('disabled');
                }
            }

            static getPanelImage(brand) {

                let panelImage = null;

                if (brand == 'Jinko') {
                    panelImage = '/img/panel_brands/jinko.png'
                }
                if (brand == 'DAH Solar') {
                    panelImage = '/img/panel_brands/dah.png'
                }
                if (brand == 'Ja') {
                    panelImage = '/img/panel_brands/ja.png'
                }
                if (brand == 'Phono') {
                    panelImage = '/img/panel_brands/phono.png'
                }

                if (brand == 'Longi' || brand == 'LONGI') {
                    panelImage = '/img/panel_brands/longi.png'
                }
                if (brand == 'Astronergy Chint') {
                    panelImage = '/img/panel_brands/astronergy.png'
                }
                if (brand == 'SUNOVA') {
                    panelImage = '/img/panel_brands/sunova.png'
                }
                if (brand == 'OSDA') {
                    panelImage = '/img/panel_brands/osda.png'
                }

                if (brand == 'Ae_Solar') {
                    panelImage = '/img/panel_brands/ae_solar.png'
                }

                if (brand == 'Pulling') {
                    panelImage = '/img/panel_brands/pulling.png'
                }

                if (brand == 'Hanersun' || brand == 'HANERSUN') {
                    panelImage = '/img/panel_brands/hanersun.png'
                }

                if (brand == 'RESUN') {
                    panelImage = '/img/panel_brands/resun.png'
                }

                if (brand == 'SINE') {
                    panelImage = '/img/panel_brands/sine.png'
                }

                if (brand == 'Era' || brand == 'ERA') {
                    panelImage = '/img/panel_brands/era.png'
                }

                if (brand == 'HONOR') {
                    panelImage = '/img/panel_brands/honor.png'
                }

                if (brand == 'Runergy') {
                    panelImage = '/img/panel_brands/runergy.png'
                }
                if (brand == 'RENEPV' || brand == 'renepv') {
                    panelImage = '/img/panel_brands/renepv.png'
                }

                return panelImage
            }

            static getInverterImage(brand) {

                let inverterImage = null;

                if (brand == 'Growatt') {
                    inverterImage = '/img/inverter_brands/growatt.png'
                }
                if (brand == 'SOFAR') {
                    inverterImage = '/img/inverter_brands/sofar.png'
                }
                if (brand == 'SOLIS') {
                    inverterImage = '/img/inverter_brands/solis.png'
                }
                if (brand == 'Bel') {
                    inverterImage = '/img/inverter_brands/bel.png'
                }
                if (brand == 'Sungrow') {
                    inverterImage = '/img/inverter_brands/sungrow.png'
                }

                if (brand == 'SAJ') {
                    inverterImage = '/img/inverter_brands/saj.png'
                }

                if (brand == 'TechPowerMicro') {
                    inverterImage = '/img/inverter_brands/techpower.png'
                }

                if (brand == 'SAJ Microinverter' || brand == 'SajMicroinverter') {
                    inverterImage = '/img/inverter_brands/saj_micro.png'
                }

                if (brand == 'SOLPLANET') {
                    inverterImage = '/img/inverter_brands/solplanet.png'
                }

                return inverterImage
            }
        }

        class Kit {

            static setKwp(consumption, incidence, orientation) {

                let GENERATION_LOST;

                if (orientation === 'norte') {
                    GENERATION_LOST = 0.2;
                } else if (orientation === 'sul') {
                    GENERATION_LOST = 0.5;
                } else {
                    GENERATION_LOST = 0.3;
                }

                return (
                    parseFloat(consumption)
                    / 30
                    / incidence
                ) * (
                    1 + GENERATION_LOST);
            }

            static setWarranty(brand) {
                if (brand === 'Sunova') {
                    return '15 anos';
                }
                return '12 anos'
            }

            static setEstimatedDelivery(brand) {
                if (brand === 'Sunova' || brand === 'Astronergy') {
                    return ': 15 a 30 dias';
                }

                if (brand === 'Jinko' || brand === 'Ja') {
                    return ': 3 a 7 dias';
                }

                if (brand === 'Dah' || brand === 'Osda' || brand === 'Ae_Solar') {
                    return ': 20 a 30 dias';
                }

                return ': até 20 dias'
            }

            static setPanelCount(item) {
                let panel_specs = JSON.parse(item.panel_specs);
                let panel_count = item.kwp / (panel_specs.power / 1000);

                return panel_count.toFixed(0);
            }

            static calculateAverageProduction(addressId, kwp) {

                let url = '/setAverageProduction';
                let result;

                $.ajax({
                    url: url,
                    async: false,
                    data: {
                        kwp: kwp,
                        addressId: addressId,
                        orientation: $("input[name=orientation]:checked").val(),
                        _token: '{{csrf_token()}}'
                    },
                    type: 'post',
                    beforeSend: function () {
                        console.log("ENVIANDO...");
                    }
                })
                    .done(function (msg, m) {
                        result = msg
                    })
                    .fail(function (jqXHR, textStatus, msg) {
                        console.log('FALHA AO CALCULAR GERACAO: ' + msg);
                    });

                return result;
            }

            static calculateFinalValue(costValue, kwp, roof, panelCount, addressId, panelBrand, panelPower, inverterBrand) {

                let url = '/setFinalValue';
                let result;

                $.ajax({
                    url: url,
                    async: false,
                    data: {
                        kwp: kwp,
                        roofStructure: roof,
                        cost: costValue,
                        panelCount: panelCount,
                        addressId: addressId,
                        panelBrand: panelBrand,
                        panelPower: panelPower,
                        inverterBrand: inverterBrand,
                        _token: '{{csrf_token()}}'
                    },
                    type: 'post',
                    beforeSend: function () {
                    }
                })
                    .done(function (msg, m) {
                        result = msg
                    })
                    .fail(function (jqXHR, textStatus, msg) {
                        console.log('FALHA AO CALCULAR VALOR FINAL: ' + msg);
                    });

                return result;
            }

            static setStringTensionPattern(tension_pattern) {

                let tension = null;

                $.ajax({
                    type: 'POST',
                    url: '/get-tension-by-value',
                    async: false,
                    data: {
                        tension: tension_pattern,
                        _token: '{{csrf_token()}}'
                    }
                })
                    .done(function (data) {
                        tension = data;
                    });
                return tension;
            }
        }

        class DateHelper {

            static databaseDateToString(date) {
                let dataMoment = moment(date);
                return dataMoment.format('DD/MM/YYYY');
            }
        }

    </script>

@endsection



