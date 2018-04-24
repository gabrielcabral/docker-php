/*
 Sistema SICEWEB
 Setor responsável: SGETI/FNDE
 Analista / Programador: Tiago Augusto Ramos ()
 E-Mail: tiago.ramos@cpmbraxis.com
 Finalidade: Funções de validação em Javascript
 Data de criação: 02/04/2012
 */

var changeSgRegiao = function () {
    $.ajax({
        url: baseUrl
        + '/index.php/manutencao/quantidadeturma/seleciona-regiao/v/' + $("#v").val() + '/REGIAO/' + $("#SG_REGIAO").val() + '/NU_SEQ_CONFIGURACAO/' + $("#NU_SEQ_CONFIGURACAO").val(),

        data: {
            REGIAO: $("#SG_REGIAO").val(),
            NU_SEQ_CONFIGURACAO: $("#NU_SEQ_CONFIGURACAO").val(),
            v: $("#v").val(),
        },

        success: function (data) {
            var find = $(data).find('#loginBase #login').html();
            if (find != null && find != '') {
                location.href = baseUrl + '/index.php';
                return;
            }

            var label = $("label[for=gridQuantidadeTurmas]");
            var labelHTML = $(data).find("label[for=gridQuantidadeTurmas]");
            label.html(labelHTML.html());

            initialize();

        }
    });
};

$("#SG_REGIAO").change(changeSgRegiao);

$('.result').hide();

//Função para restringir a somente valores numéricos (a class inteiro não estava sendo eficiente neste sentido)
jQuery.fn.ForceNumericOnly = function () {
    return this.each(function () {
        $(this).keydown(function (e) {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
            return (
            key == 8 ||
            key == 9 ||
            key == 46 ||
            (key >= 37 && key <= 40) ||
            (key >= 48 && key <= 57) ||
            (key >= 96 && key <= 105));
        });
    });
};

function initialize() {
    $('a.dialog').click(function () {
        $.ajax({
            url: $(this).attr('href'),
            success: function (data) {
                var find = $(data).find('#loginBase #login').html();
                if (find != null && find != '') {
                    location.href = baseUrl + '/index.php';
                    return;
                }

                $('.result').html(data);

                Dialog.modal($('.result').html().replace(/\n/g, ''), '&nbsp;', null);

                Dialog.modal(data.replace(/\n/g, ''), '&nbsp;', null);

                $('#popup_container.modal #popup_content #popup_message div').css('padding', '0px');
            }
        });
        return false;
    });

    $("input.inteiro").ForceNumericOnly();
    $("#confirmar").click(function () {

        var somaMeso = 0;
        var somaTotal = 0;
        var limiteTurmaValido = true;

        $(".mesorregiao").each(function () {
            if ($(this).attr('value')) {
                if (parseInt($(this).attr('value')) < parseInt($(this).data('min-turmas'))) {
                    return limiteTurmaValido = false;
                }
                somaMeso += parseInt($(this).attr('value'));
            }
        });

        if (limiteTurmaValido == false) {

            addTopMessage('warning', 'O valor atribuído ao Limite de Turmas é inferior ao número de Turmas Cadastradas. Favor redistribuir.');
            return false;
        }

        $(".total").each(function () {
            if ($(this).attr('value')) {
                somaTotal += parseInt($(this).attr('value'));
            }
        });

        if (somaTotal != somaMeso) {
            addTopMessage('warning', 'Soma dos valores distribuídos nas mesorregiões é diferente do total permitido. Favor redistribuir.');
            return false;
        }

    });
}

function addTopMessage(tipo, mensagem) {
    if ($('#mensagens').length == 0) {
        var $divMessages = $('<div id="mensagens"></div>');
        if ($('#menuContexto').length == 0) {
            $('#conteudoCabecalho').after($divMessages);
        } else {
            $('#menuContexto').after($divMessages);
        }
    }

    var config = {
        'error': {'class': 'msgErro', 'label': 'Erro'},
        'warning': {'class': 'msgAlerta', 'label': 'Atenção'},
        'success': {'class': 'msgSucesso', 'label': 'Sucesso'},
        //'info' : {'class' : 'msgErro', 'label' : 'Erro'}
    };

    $('#mensagens').html('<div class="' + config[tipo].class + '"><h3>' + config[tipo].label + '</h3><p>' + mensagem + '</p></div>');

    $('html, body').animate({
        scrollTop: 0
    }, 200, 'linear')
}

