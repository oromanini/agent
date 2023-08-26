<script>

    $(function () {

        PageUtils.selectize()

        let consumption = null;
        let clientId = PageUtils.setClient()

        $('#average_consumption').on('change', function () {
            consumption = $('#average_consumption').val();
        })

        AddressUtils.setAddresses(clientId)
        AddressUtils.setUcs(clientId)
        PageUtils.disableSubmitIfConsumptionIsNull()

        $('#client').change(function () {
            clientId = $('select[name=client] option').filter(':selected').val();
            AddressUtils.setAddresses(clientId)
            AddressUtils.setUcs(clientId)
        });

        $('#average_consumption').change(function () {
            PageUtils.disableSubmitIfConsumptionIsNull()
        });


        $('#kitSearchSubmit').on('click', function () {

            $('#loader').show()

            let consumption = $('#average_consumption').val();
            let incidence = AddressUtils.setIncidence()
            let kwp = KitsCalc.setKwp(consumption, incidence);
            let roof = $("input[name=roof_structure]:checked").val();
            let tension = $('select[name=tension_pattern] option').filter(':selected').val()
            let addressId = $('select[name=installation_address] option').filter(':selected').val()

            $.ajax({
                url: "/kitSearch/" + kwp + '/' + roof + '/' + tension,
                type: 'get',
                beforeSend: function () {
                    $('#loader').removeClass('disable');
                    $('#loader').addClass('enable');
                },
            })
                .done(function (msg) {
                    msg = msg.reverse()
                    setTimeout(function () {
                        $('#loader').hide()
                    }, 2000)
                    $('#kits').empty();
                    $('#generateProposalButton').empty();
                    $.each(msg, function (i, item) {

                        let panelImage = item.panel_specs.logo ?? ImageUtils.getPanelImage(panelSpecs['panel_brand'])
                        let banks = '/img/banks/banks.png';
                        let inverterImage = item.inverter_specs.logo ?? ImageUtils.getInverterImage(technicalDescription['inverter_brand'])

                        let panelCount = KitsCalc.setPanelCount(item);

                        let isPromotional = false;
                        let isPromotionalText = isPromotional ? 'Promoção' : 'À vista';
                        let isPromotionalColor = isPromotional ? 'is-success' : 'is-success is-light';

                        let finalValue = KitsCalc.calculateFinalValue(
                            item.cost,
                            item.kwp,
                            roof,
                            panelCount,
                            addressId,
                            item.panel_specs.brand,
                            item.panel_specs.power,
                            item.inverter_specs.brand,
                        );

                        let isPromo = finalValue.isPromotional
                            ? '<span class="tag is-success is-flex">Promoção</span>'
                            : '<br>'

                        let averageProduction = KitsCalc.calculateAverageProduction(addressId, item.kwp);

                        PageUtils.kitsAppend(
                            item,
                            panelImage,
                            banks,
                            inverterImage,
                            isPromotionalText,
                            isPromotionalColor,
                            isPromo,
                            finalValue,
                            averageProduction,
                            panelCount,
                        )
                    });

                    PageUtils.generateSubmitButton()

                })

                .fail(function (jqXHR, textStatus, msg) {
                    console.log('FALHA: ' + msg);
                });
        });
    });

    class AddressUtils {
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

                    $('#installation_address_text').attr('data-tooltip', 'Incidência: ' + AddressUtils.setIncidence())
                })
                .fail(function (jqXHR, textStatus, msg) {
                    console.log('FALHA: ' + msg);
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
                    console.log('FALHA: ' + msg);
                });
        }
        static setIncidence() {

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
                    console.log('FALHA: ' + msg);
                });

            return incidence;
        }
    }

    class PageUtils {
        static disableSubmitIfConsumptionIsNull() {

            if (!$('#average_consumption').val() && !$('input:radio[name="roof_structure"]').is(':checked') && !$('#orientation input:checked').length > 0) {
                $('#kitSearchSubmit').attr("disabled", "disabled");
            } else {
                $('#kitSearchSubmit').removeAttr('disabled');
            }
        }
        static kitsAppend(
            item,
            panelImage,
            banks,
            inverterImage,
            isPromotionalText,
            isPromotionalColor,
            isPromo,
            finalValue,
            averageProduction,
            panelCount
        ) {
            $('#kits').append(
                '<div class="column is-3">' +
                '<label>' +
                '<input type="radio" name="kit_id" value="' + item.distributor_code + '">' +
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
                item.kwp + ' kWp' +
                '</div>' +
                '<div style="font-size: 10pt; text-align: center">' +
                'Aprox. ' + averageProduction + ' kWh/mês' +
                '</div>' +
                '<div style="font-size: 10pt; text-align: center">' +
                'Disponibilidade: ' + item.availability +
                '</div>' +
                '<hr>' +
                '<div style="text-align: center">' +
                '<strong>Painel: </strong>' + panelCount + ' ' + item.panel_specs.brand + ' ' + item.panel_specs.power + 'W ' +
                '</div>' +
                '<div style="text-align: center">' +
                '<strong>Eficiência: </strong>' + item.panel_specs.efficiency + '%' +
                '</div>' +
                '<div style="text-align: center">' +
                '<strong>Garantia: </strong>' + item.panel_specs.warranty +
                '</div>' +
                '<hr>' +
                '<div style="text-align: center">' +
                '<div style="text-align: center">' +
                '<strong>Tensão: </strong>' + item.inverter_specs.tension +
                '</div>' +
                '<strong>Inversor: </strong>' + item.inverter_specs.brand +
                    '</div>' +
                '<div style="text-align: center"><br>' +
                item.inverter_specs.model +
                '</div>' +
                '<div style="text-align: center"><br>' +
                '<span style="font-size: 12pt" class="tag is-warning"><strong>' + 'Prazo de entrega: ' + '</strong>' + ' ' + '15 a 30 dias' + '</span>' +
                '</div>'+
                '<hr>' +
                isPromo +
                '<div style="color: #6BC6A7; font-size: 18pt; text-align: center; font-weight: bold">' +
                parseFloat(finalValue.finalPrice).toLocaleString('pt-BR', {
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
        }

        static selectize() {
            $("#client").selectize({});
            $("#agent").selectize({});
        }

        static setClient() {
            return $('select[name=client] option').filter(':selected').val();
        }

        static generateSubmitButton() {
            $('#generateProposalButton').append(
                '<div class="columns"><button type="submit" class="button is-primary is-large">Gerar Proposta</button></div>'
            );
        }
    }

    class ImageUtils {
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
            if (brand == 'Longi') {
                panelImage = '/img/panel_brands/longi.png'
            }
            if (brand == 'Astronergy Chint') {
                panelImage = '/img/panel_brands/astronergy.png'
            }
            if (brand == 'Sunova') {
                panelImage = '/img/panel_brands/sunova.png'
            }
            if (brand == 'Osda') {
                panelImage = '/img/panel_brands/osda.png'
            }

            if (brand == 'Ae_Solar') {
                panelImage = '/img/panel_brands/ae_solar.png'
            }

            return panelImage
        }
        static getInverterImage(brand) {

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
            if (brand == 'Bel') {
                inverterImage = '/img/inverter_brands/bel.png'
            }
            if (brand == 'Sungrow') {
                inverterImage = '/img/inverter_brands/sungrow.png'
            }

            if (brand == 'Saj') {
                inverterImage = '/img/inverter_brands/saj.png'
            }

            return inverterImage
        }
    }

    class KitsCalc {
        static setKwp(consumption, incidence) {
            return (
                parseFloat(consumption)
                / 30
                / incidence
            ) * (
                1 + {{ (float)env('GENERATION_LOST') }});
        }
        static calculateFinalValue(costValue, kwp, roof, panelCount, addressId, panelBrand, panelPower, inverterBrand) {

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
                    address_id: addressId,
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
                    console.log('FALHA: ' + msg);
                });

            return result;
        }
        static calculateAverageProduction(addressId, kwp) {
            return '0';
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
                    console.log('FALHA: ' + msg);
                });


            return result;
        }
        static setPanelCount(item) {
            return parseInt(item.kwp / (item.panel_specs.power / 1000));
        }
    }

</script>
