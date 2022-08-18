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
                        <div class="field">
                            <label for="client" class="label">Cliente*</label>
                            <div
                                class="select is-multiline is-fullwidth is-rounded @error('client') is-danger @enderror">
                                <select id="client" name="client">
                                    @forelse($clients as $client)
                                        <option value="{{ $client->id }}">{{$client->name}}</option>
                                    @empty
                                        <option value="">Não há clientes cadastrados</option>
                                    @endforelse
                                </select>
                            </div>
                            @error('client')<span class="error-message">{{ $message }}</span>@enderror
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
                                       placeholder="Digite o consumo">
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
                                       placeholder="Digite o valor do kW">
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
                                        <option value="{{ $key }}">{{ $value }}</option>
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
                                <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                            </label>
                            <div
                                class="select is-multiline is-fullwidth is-rounded @error('installation_address') is-danger @enderror">
                                <select id="installation_address" name="installation_address"></select>
                            </div>
                            @error('installation_address')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label for="installation_uc" class="label">U.C de instalação
                                <ion-icon class="info-icon" name="information-circle-outline"></ion-icon>
                            </label>
                            <div
                                class="select is-multiline is-fullwidth is-rounded @error('installation_uc') is-danger @enderror">
                                <select id="installation_uc" name="installation_uc">
                                    <option value="">Não há UC's cadastradas</option>
                                </select>
                            </div>
                            @error('installation_uc')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="columns" style="margin-top: 50px;">
                    <label for="roof_structure" class="label">Selecione o telhado</label>
                </div>
                <div class="columns" id="roof_div">
                    @foreach($roofs as $roof)
                        <div class="column">
                            <label>
                                <input type="radio" name="roof_structure" value="{{$roof['id']}}"
                                       class="radio-image roof-structure">
                                <img src="{{ $roof['image'] }}" width="200" class="roof-img">
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="columns is-flex is-justify-content-center" style="margin-top: 15px; margin-bottom: 30px">
                    <div class="column is-6 is-flex is-justify-content-space-around is-align-items-center is-warning"
                         style="border: 2px solid #f2a714; border-radius: 100px;">
                        <label class="checkbox">
                            <input name="orientation[norte]" type="checkbox" value="norte" checked>
                            Norte
                        </label>
                        <label class="checkbox">
                            <input name="orientation[leste]" value="leste" type="checkbox">
                            Leste
                        </label>
                        <label class="checkbox">
                            <input name="orientation[oeste]" value="oeste" type="checkbox">
                            Oeste
                        </label>
                        <label class="checkbox">
                            <input name="orientation[sul]" value="sul" type="checkbox">
                            Sul
                        </label>

                    </div>
                </div>
                <hr style="margin: 10px">
                <div class="column is-flex is-justify-content-center">
                    <span class="button is-medium is-info is-rounded" id="kitSearchSubmit">
                        <ion-icon name="sunny-outline"></ion-icon>&nbsp;Buscar Kits
                    </span>
                </div>
                <hr style="margin: 10px">

                {{--        KITS--}}
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

            let consumption = null;
            let clientId = $('select[name=client] option').filter(':selected').val();


            $('#average_consumption').on('change', function () {
                consumption = $('#average_consumption').val();
            })

            setAddresses(clientId)
            setUcs(clientId)
            disableSubmitIfConsumptionIsNull()

            $('#client').change(function () {
                clientId = $('select[name=client] option').filter(':selected').val();
                setAddresses(clientId)
                setUcs(clientId)
            });

            $('#average_consumption').change(function () {
                disableSubmitIfConsumptionIsNull()
            });

            function setAddresses(clientId) {

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
                    })
                    .fail(function (jqXHR, textStatus, msg) {
                        console.log(msg);
                    });
            }

            function setUcs(clientId) {

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
                        console.log(msg);
                    });
            }

            function setIncidence() {

                let addressId = $('select[name=installation_address] option').filter(':selected').val();
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
                        console.log(msg);
                    });

                return incidence;
            }

            function disableSubmitIfConsumptionIsNull() {

                if (!$('#average_consumption').val() && !$('input:radio[name="roof_structure"]').is(':checked') && !$('#orientation input:checked').length > 0) {
                    $('#kitSearchSubmit').attr("disabled", "disabled");
                } else {
                    $('#kitSearchSubmit').removeAttr('disabled');
                }
            }

            function getPanelImage(brand) {

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

                return panelImage
            }

            function getInverterImage(brand) {

                let inverterImage = null;

                if (brand == 'Growatt') {
                    inverterImage = '/img/inverter_brands/growatt.png'
                }
                if (brand == 'Sofar') {
                    inverterImage = '/img/inverter_brands/sofar.png'
                }
                if (brand == 'Solis') {
                    inverterImage = '/img/inverter_brands/solis.png'
                }

                return inverterImage
            }

            $('#kitSearchSubmit').on('click', function () {
                $('#loader').show()
                let consumption = $('#average_consumption').val();
                let incidence = setIncidence()

                let kwp = parseFloat(consumption) / 30 / (incidence - {{ (float)env('GENERATION_LOST') }});
                let roof = $("input[name=roof_structure]:checked").val();
                let tension = $('select[name=tension_pattern] option').filter(':selected').val()
                let addressId = $('select[name=installation_address] option').filter(':selected').val()

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
                        $.each(msg, function (i, item) {

                            let technicalDescription = item[0]['technical_description']
                            let technicalDescription2 = item[1] ? item[1]['technical_description'] : null;
                            let technicalDescription3 = item[2] ? item[2]['technical_description'] : null;
                            let technicalDescription4 = item[3] ? item[3]['technical_description'] : null;
                            let panelSpecs = technicalDescription['panel_specs'];
                            let panelImage = getPanelImage(panelSpecs['panel_brand'])
                            let banks = technicalDescription['inverter_brand'] === 'Growatt' ? '/img/banks/aldo.png' : '/img/banks/grandsol.png';
                            let inverterImage = getInverterImage(technicalDescription['inverter_brand'])

                            let inverterPower1 = technicalDescription['inverter_power'];
                            let inverterPower2 = technicalDescription2 != null ? '+' + technicalDescription2['inverter_power'] + 'kW ' : '';
                            let inverterPower3 = technicalDescription3 != null ? '+' + technicalDescription3['inverter_power'] + 'kW ' : '';
                            let inverterPower4 = technicalDescription4 != null ? '+' + technicalDescription4['inverter_power'] + 'kW ' : '';

                            let inverterModel1 = technicalDescription['inverter_model'] + 'KW ';
                            let inverterModel2 = technicalDescription2 != null ? ' + ' + technicalDescription2['inverter_model'] : '';
                            let inverterModel3 = technicalDescription3 != null ? ' + ' + technicalDescription3['inverter_model'] : '';
                            let inverterModel4 = technicalDescription4 != null ? ' + ' + technicalDescription4['inverter_model'] : '';

                            let costValue = item[0].price + (item[1] ? item[1].price : 0) + (item[2] ? item[2].price : 0) + (item[3] ? item[3].price : 0);
                            let panelCount = setPanelCount(item);

                            let isPromotional = false;
                            // let isPromotional = technicalDescription['inverter_brand'] === 'Sofar' && (item['sum'].kwp == 2.2 || item['sum'].kwp == 4.4 || item['sum'].kwp == 6.6 || item['sum'].kwp == 7.7);
                            let isPromotionalText = isPromotional ? 'Promoção' : 'À vista';
                            let isPromotionalColor = isPromotional ? 'is-success' : 'is-success is-light';

                            let finalValue = calculateFinalValue(costValue, item['sum'].kwp.toFixed(2), roof, panelCount);
                            let averageProduction = calculateAverageProduction(addressId, item['sum'].kwp.toFixed(2));

                            $('#kits').append(
                                '<div class="column is-3">' +
                                '<label>' +
                                '<input type="radio" name="kit_id" value="' + item[0]['code'] + ';' + (item[1] ? item[1]['code'] + ';' : 'null') + (item[2] ? item[2]['code'] + ';' : '') + (item[3] ? item[3]['code'] : '') + '">' +
                                '<div id="all" class="my-box-shadow">' +
                                '<span class="tag ' + isPromotionalColor + '">' +
                                isPromotionalText +
                                '</span>' +
                                '<div class="is-flex is-justify-content-center">' +
                                '<img src="' + inverterImage + '" alt="" width="150">' +
                                '</div>' +
                                '<div class="is-flex is-justify-content-center">' +
                                '<img src="' + panelImage + '" alt="" width="200">' +
                                '</div>' +
                                '<div style="display:flex; justify-content: center; text-align: center; font-size: 18pt; color: #6b7280; font-weight: 900; margin: 20px 0px">' +
                                item['sum'].kwp.toFixed(2) + ' kWp' +
                                '</div>' +
                                '<div style="font-size: 10pt; text-align: center">' +
                                'Geração aproximada de ' + averageProduction + ' kWh/mês' +
                                '</div>' +
                                '<hr>' +
                                '<div style="text-align: center">' +
                                '<strong>Painel: </strong>' + panelCount + ' ' + panelSpecs['panel_brand'] + ' ' + panelSpecs['panel_power'] + 'W ' + panelSpecs['panel_type'] +
                                '</div>' +
                                '<div style="text-align: center">' +
                                '<strong>Eficiência: </strong>' + panelSpecs['panel_efficiency'] + '%' +
                                '</div>' +
                                '<hr>' +
                                '<div style="text-align: center">' +
                                '<div style="text-align: center">' +
                                '<strong>Tensão: </strong>' + technicalDescription['inverter_tension'] +
                                '</div>' +
                                '<strong>Inversor: </strong>' + technicalDescription['inverter_brand'] + ' ' + inverterPower1 + inverterPower2 + inverterPower3 + inverterPower4 +
                                '</div>' +
                                '<div style="text-align: center"><br>' +
                                inverterModel1 + ' ' + inverterModel2 + ' ' + inverterModel3 + inverterModel4 +
                                '</div>' +
                                '<hr>' +
                                '<div style="color: #6BC6A7; font-size: 18pt; text-align: center; font-weight: bold">' +
                                parseFloat(finalValue).toLocaleString('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL',
                                }) +
                                '</div>' +
                                '<hr/>' +
                                '<div><img src="' + banks + '" alt="..."></div>' +
                                '</div>' +
                                '</label>' +
                                '</div>'
                            );
                        });

                        $('#generateProposalButton').append(
                            '<div class="columns"><button type="submit" class="button is-primary is-large">Gerar Proposta</button></div>'
                        );

                    })

                    .fail(function (jqXHR, textStatus, msg) {
                        console.log(msg);
                    });
            });
        });

        function calculateFinalValue(costValue, kwp, roof, panelCount) {

            let url = '/setFinalValue';
            let result;

            $.ajax({
                url: url,
                async: false,
                data: {
                    kwp: kwp,
                    roof_structure: roof,
                    cost: costValue,
                    panel_count: panelCount,
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
                    console.log(msg);
                });


            return result;
        }


        function calculateAverageProduction(addressId, kwp) {

            let url = '/setAverageProduction';
            let result;

            $.ajax({
                url: url,
                async: false,
                data: {
                    kwp: kwp,
                    addressId: addressId,
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
                    console.log(msg);
                });


            return result;
        }

        function setPanelCount(item) {

            let one = item[0] ? item[0].panel_count : 0;
            let two = item[1] ? item[1].panel_count : 0;
            let three = item[2] ? item[2].panel_count : 0;
            let four = item[3] ? item[3].panel_count : 0;

            return parseInt(one) + parseInt(two) + parseInt(three) + parseInt(four);
        }

    </script>

@endsection



