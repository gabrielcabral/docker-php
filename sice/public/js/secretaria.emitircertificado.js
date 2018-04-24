if ($('#ZIP_FILE').val()) {
    window.open(baseUrl + '/index.php/secretaria/emitircertificado/download-zip/file/' + $('#ZIP_FILE').val());
}

////TELA DE FILTRO
var ufChange = function () {

    popUp.carregando();

    $.ajax({
        type: "GET",
        url: baseUrl + '/index.php/secretaria/emitircertificado/uf-change',
        data: {
            SG_UF: $("#SG_UF").val(),
        },
        complete: function () {
            popUp.carregando();
        },

        success: function (data) {

            var options;

            options = $("#CO_MESORREGIAO");
            $(options).empty();
            $.each(data.mesorregiao, function (index, value) {
                options.append(new Option(value, index));
            });

            options = $("#CO_MUNICIPIO");
            $(options).empty();
            $.each(data.municipio, function (index, value) {
                options.append(new Option(value, index));
            });

            options = $("#NU_SEQ_CURSO");
            $(options).empty();
            $.each(data.curso, function (index, value) {
                options.append(new Option(value, index));
            });
        }
    });
};

var mesorregiaoChange = function () {

    popUp.carregando();

    $.ajax({
        type: "GET",
        url: baseUrl + '/index.php/secretaria/emitircertificado/mesorregiao-change/',
        data: {
            CO_MESORREGIAO: $("#CO_MESORREGIAO").val(),
            SG_UF: $("#SG_UF").val(),
        },
        complete: function () {
            popUp.carregando();
        },

        success: function (data) {

            var options;

            options = $("#CO_MUNICIPIO");
            $(options).empty();
            $.each(data, function (index, value) {
                options.append(new Option(value, index));
            });
        }
    });

};

$("#SG_UF").change(ufChange);
$("#CO_MESORREGIAO").change(mesorregiaoChange);

$("#cancelar").on('click', function () {
    window.location = $(this).data('url');
});

$('body').on('click', 'input:checkbox', function () {
    var val = $(this).val().split('/');
    $.post(baseUrl + '/index.php/secretaria/emitircertificado/gerar-sessao-cursistas', {
        cursista: val[0],
        turma: val[1]
    });
});

//Combo de mais acoes do grid
$("#table_button").click(
    function () {

        var dados = {usuario: [], turma: []};
        var params;

        if ($('input[name=NU_SEQ_USUARIO_NU_SEQ_TURMA]:checked').val() == null) {
            Dialog.alert('Selecione pelo menos um cursista para emitir certificado.', null, null);
        } else {

            $('input[name=NU_SEQ_USUARIO_NU_SEQ_TURMA]:checked').each(function (index) {
                var item = $(this).val().split('/');
                dados.usuario.push(item[0]);
                dados.turma.push(item[1]);
            });

            params = jQuery.param(dados);

            location.href = $("#table_main_action").val() + '?' + params;
        }
    }
);


$.each($('.icoAvaliar,.icoEncaminhar'), function () {

    // Favor colocar um código mais decente.

    var url = $(this).attr('href');

    var temAvaliacao = url.split('ST_CURSO_AVAL/')[1].split('/')[0];
    var situacaoMatricula = url.split('DS_SITUACAO/')[1].split('/')[0];
    var situacaoTurma = url.split('ST_TURMA/')[1].split('/')[0];

    // situacaoTurma igual a 11 é igual a finalizada
    if((situacaoMatricula != 'Aprovado com destaque' && situacaoMatricula != 'Aprovado') || situacaoTurma != 11 || temAvaliacao == 1){
        $(this).addClass('disabled');
        //$(this).attr('href', '#');
        $(this).attr('onclick', 'return false');
    }

});