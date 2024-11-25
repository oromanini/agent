<script>

    $(function () {

        let consumption = null;
        let city = $('#city').val();

        $('#average_consumption').on('change', function () {
            consumption = $('#average_consumption').val();
        })

        ScreenHelper.disableSubmitIfConsumptionIsNull()

        $('#average_consumption').change(function () {
            ScreenHelper.disableSubmitIfConsumptionIsNull()
        });

        $('#city').on('change', function () {
            city = $('#city').val();
            console.log(city)
        })

        $('#kitSearchSubmit').on('click', function () {
            $('#loader').show()

            let consumption = $('#average_consumption').val();
            let incidence = Address.setIncidence(city)
            let kwp = Kit.setKwp(consumption, incidence);
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
                                city,
                                panelSpecs['brand'],
                                panelSpecs['power'],
                                inverterSpecs['brand']
                            );

                            let isPromotional = finalValue.isPromotional;
                            let isPromotionalText = isPromotional ? '* PROMOÇÃO *' : 'Preço padrão';
                            let isPromotionalColor = isPromotional ? 'is-success' : 'is-warning';

                            let isPromo = '';
                            let inverterBrand = inverterSpecs.brand === 'SAJ Microinverter' ? 'SAJ MICRO' : inverterSpecs.brand;
                            let averageProduction = Kit.calculateAverageProduction(city, item.kwp);

                            $('#kits').append(
                                '<div class="column is-3">' +
                                '<label>' +
                                '<input type="radio" name="kit_id" value="' + item.distributor_code + '">' +
                                '<div id="all" class="my-box-shadow">' +
                                '<span class="tag ' + isPromotionalColor + '">' +
                                isPromotionalText +
                                '</span>' +
                                '<div class="is-flex is-justify-content-center">' +
                                '<img src="' + ScreenHelper.getPanelImage(panelSpecs.brand) + '" alt="" width="135">' +
                                '<img src="' + ScreenHelper.getInverterImage(inverterSpecs.brand) + '" alt="" width="135">' +
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
                    console.log('FALHA: ' + msg);
                });
        });
    });

    class Address {

        static setIncidence(city) {

            let url = '/incidenceFromCity/';
            let incidence = 1;

            $.ajax({
                url: url + city,
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
            if (brand == 'Longi') {
                panelImage = '/img/panel_brands/longi.png'
            }
            if (brand == 'Astronergy Chint') {
                panelImage = '/img/panel_brands/astronergy.png'
            }
            if (brand == 'SUNOVA') {
                panelImage = '/img/panel_brands/sunova.png'
            }
            if (brand == 'Osda') {
                panelImage = '/img/panel_brands/osda.png'
            }

            if (brand == 'Ae_Solar') {
                panelImage = '/img/panel_brands/ae_solar.png'
            }

            if (brand == 'Pulling') {
                panelImage = '/img/panel_brands/pulling.png'
            }

            if (brand == 'Hanersun') {
                panelImage = '/img/panel_brands/hanersun.png'
            }

            if (brand == 'RESUN') {
                panelImage = '/img/panel_brands/resun.png'
            }

            if (brand == 'Sine') {
                panelImage = '/img/panel_brands/sine.png'
            }

            if (brand == 'Era' || brand == 'ERA') {
                panelImage = '/img/panel_brands/era.png'
            }

            if (brand == 'Honor') {
                panelImage = '/img/panel_brands/honor.png'
            }

            if (brand == 'Runergy') {
                panelImage = '/img/panel_brands/runergy.png'
            }
            console.log(brand)
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

            if (brand == 'SOLPLANET') {
                inverterImage = '/img/inverter_brands/solplanet.png'
            }

            if (brand == 'TechPowerMicro') {
                inverterImage = '/img/inverter_brands/techpower.png'
            }

            if (brand == 'SAJ Microinverter' || brand == 'SajMicroinverter') {
                inverterImage = '/img/inverter_brands/saj_micro.png'
            }

            return inverterImage
        }
    }

    class Kit {

        static setKwp(consumption, incidence) {
            return (
                parseFloat(consumption)
                / 30
                / incidence
            ) * (
                1 + {{ (float)env('GENERATION_LOST') }});
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

        static calculateAverageProduction(city, kwp) {

            let url = '/setAverageProductionByCity';
            let result;

            $.ajax({
                url: url,
                async: false,
                data: {
                    kwp: kwp,
                    cityId: city,
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

        static calculateFinalValue(costValue, kwp, roof, panelCount, cityId, panelBrand, panelPower, inverterBrand) {

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
                    cityId: cityId,
                    panelBrand: panelBrand,
                    panelPower: panelPower,
                    inverterBrand: inverterBrand,
                    isLead: true,
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
