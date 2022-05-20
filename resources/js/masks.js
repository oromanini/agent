$(function () {

    $('#document').mask('000.000.000-00', {reverse: true});
    $('#phone_number').mask('(00) 0 0000-0000');
    $('#zipcode').mask('00000-000');
    $('#kw_price').mask('0,00');
    $('#kwp').mask('##0.00', {reverse: true});
    $('#cost').mask('000.000.000.000.000,00', {reverse: true});
    $('#final_value').mask('000.000.000.000.000,00', {reverse: true});


    $('#type').change(function () {
        if ($('#type').val() === 'company') {
            $('#document').mask('00.000.000/0000-00', {reverse: true});
            $('#nameLabel').text('Razão Social*')
            $('#documentLabel').text('CNPJ*')
            $('#aliasLabel').text('Nome Fantasia*')
        } else {
            $('#document').mask('000.000.000-00', {reverse: true});
            $('#nameLabel').text('Nome*')
            $('#documentLabel').text('CPF*')
            $('#aliasLabel').text('Apelido*')
        }
    })


})
