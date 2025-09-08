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

        static getPanelImage(panel_brand) {

            let brand = panel_brand.toLowerCase()
            let path = '/storage/module_brand_logos/';

            if (brand == 'dah Solar') {
                return path + 'dah.png'
            }

            if (brand == 'astronergy chint') {
                return path + this.slugify(brand) + '.png';
            }

            return path + brand + '.png'
        }

        static getInverterImage(inverter_brand) {

            let brand = inverter_brand.toLowerCase();
            let path = '/storage/inverter_brand_logos/';

            if (brand == 'techpowermicro') {
                return path + 'techpower.png'
            }

            if (brand == 'saj microinverter' || brand == 'sajmicroinverter') {
                return path + 'saj_micro.png'
            }

            return path + this.slugify(brand) + '.png';
        }

        static slugify(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Substitui espaços por -
                .replace(/[^\w\-]+/g, '')       // Remove todos os caracteres que não sejam letras, números ou hífens
                .replace(/\-\-+/g, '-')         // Remove hífens duplicados
                .trim();
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
