$(function () {

    setCities(null)

    function clean_form_zipcode() {
        $("#street").val("");
        $("#neighborhood").val("");
    }

    function carregarEstadoECidade(data) {

        $.ajax({
            url: "/getCityAndStateByNameAndUf/" + data.localidade + '/' + data.uf,
            type: 'get',
            success: function (response) {
                $('#state option[value="' + response.state_id + '"]').prop("selected", "selected");
                setCities(response.id);
            }
        });
    }


    //Quando o campo cep perde o foco.
    $("#zipcode").blur(function () {

        //Nova variável "cep" somente com dígitos.
        var zipcode = $(this).val().replace(/\D/g, '');

        //Verifica se campo cep possui valor informado.
        if (zipcode != "") {

            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if (validacep.test(zipcode)) {

                //Preenche os campos com "..." enquanto consulta webservice.
                $("#street").val("...");
                $("#neighborhood").val("...");
                $("#city").val("...");
                $("#state").val("...");

                //Consulta o webservice viacep.com.br/
                $.getJSON("https://viacep.com.br/ws/" + zipcode + "/json/?callback=?", function (dados) {

                    if (!("erro" in dados)) {
                        //Atualiza os campos com os valores da consulta.
                        $("#street").val(dados.logradouro);
                        $("#neighborhood").val(dados.bairro);
                        carregarEstadoECidade(dados)

                    } //end if.
                    else {
                        //CEP pesquisado não foi encontrado.
                        clean_form_zipcode();
                        alert("CEP não encontrado.");
                    }
                });
            } //end if.
            else {
                //cep é inválido.
                clean_form_zipcode();
                alert("Formato de CEP inválido.");
            }
        } //end if.
        else {
            //cep sem valor, limpa formulário.
            clean_form_zipcode();
        }
    });

    // CITIES LOAD
    $('#state').change(function () {
        setCities(null)
    });

    $('#state2').change(function () {
        setCities2()
    });

    function setCities(city_id) {

        let id = $('#state').find(":selected").val();
        let cityId = parseInt($('#city_id').val());

        console.log(cityId)

        $.ajax({
            url: "/citiesByState/" + id,
            type: 'get',
            beforeSend: function () {
                console.log("ENVIANDO...");
            }
        })
            .done(function (msg) {

                $('#city').empty();

                $.each(msg, function (i, item) {
                    $('#city').append($('<option>', {
                            value: item.id,
                            text: item.name,
                        })
                    );
                    $('#city option[value="' + cityId + '"]').prop("selected", "selected");
                });


            })
            .fail(function (jqXHR, textStatus, msg) {
                console.log(msg);
            });

    }

    function setCities2() {
        let id = $('#state2').find(":selected").val();
        let cityId = parseInt($('#city_id2').val());

        $.ajax({
            url: "/citiesByState/" + id,
            type: 'get',
            beforeSend: function () {
                console.log("ENVIANDO...");
            }
        })
            .done(function (msg) {

                $('#city2').empty();

                $.each(msg, function (i, item) {
                    $('#city2').append($('<option>', {
                        value: item.id,
                        text: item.name,
                    }).prop('selected', city_id !== null && item.id === city_id));
                });


            })
            .fail(function (jqXHR, textStatus, msg) {
                console.log(msg);
            });
    }

})
