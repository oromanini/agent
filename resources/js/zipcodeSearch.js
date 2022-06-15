$(function (){

    setCities()

    function clean_form_zipcode() {
        $("#street").val("");
        $("#neighborhood").val("");
    }

    //Quando o campo cep perde o foco.
    $("#zipcode").blur(function() {

        //Nova variável "cep" somente com dígitos.
        var zipcode = $(this).val().replace(/\D/g, '');

        //Verifica se campo cep possui valor informado.
        if (zipcode != "") {

            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if(validacep.test(zipcode)) {

                //Preenche os campos com "..." enquanto consulta webservice.
                $("#street").val("...");
                $("#neighborhood").val("...");
                $("#city").val("...");
                $("#state").val("...");

                //Consulta o webservice viacep.com.br/
                $.getJSON("https://viacep.com.br/ws/"+ zipcode +"/json/?callback=?", function(dados) {

                    if (!("erro" in dados)) {
                        //Atualiza os campos com os valores da consulta.
                        $("#street").val(dados.logradouro);
                        $("#neighborhood").val(dados.bairro);
                        $("#city").val(dados.localidade);
                        $("#state").val(dados.uf);
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
        setCities()
    });

    $('#state2').change(function () {
        setCities2()
    });

    function setCities() {
        let id = $('#state').find(":selected").val();
        let cityId = parseInt($('#city_id').val());

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
                    }).prop('selected', item.id === cityId));
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
                    }).prop('selected', item.id === cityId));
                });


            })
            .fail(function (jqXHR, textStatus, msg) {
                console.log(msg);
            });
    }

})
