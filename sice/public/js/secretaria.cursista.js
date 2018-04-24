//Pesquisa de informações na receita federal com o CPF
var focusoutCPF = function () {
    if ($(this).val() != '') {
        var cpf = $("#NU_CPF").val();
        cpf = cpf.replace(/[0-9]^/g, "");
        $.ajax({
            url: baseUrl
            + '/index.php/secretaria/cadastrarcursista/obter-informacoes-por-cpf/NU_CPF/'
            + $("#NU_CPF").val(),

            data: $("form").serializeArray(),

            success: function (html) {
                var find = $(html).find('#loginBase #login').html();
                if (find != null && find != '') {
                    location.href = baseUrl + '/index.php';
                    return;
                }

                $('#fieldset-dadosPessoais').html($(html).find('#fieldset-dadosPessoais').html());

                $("#SG_UF_NASCIMENTO").change(ufChange);
                $("#CO_MUNICIPIO_ESCOLA").change(municipioChange);
                $("#CO_REDE_ENSINO").change(redeEnsinoChange);

                initilizeMasks();
            }
        });
    }
};

//Pesquisa de municípios por UF
var ufChange = function () {
    $.ajax({
        type: "POST",
        url: baseUrl
        + '/index.php/secretaria/cadastrarcursista/renderiza-municipio/SG_UF_NASCIMENTO/'
        + $("#SG_UF_NASCIMENTO").val(),

        data: {
            SG_UF_NASCIMENTO: $("#SG_UF_NASCIMENTO").val(),
        },

        success: function (html) {
            var find = $(html).find('#loginBase #login').html();
            if (find != null && find != '') {
                location.href = baseUrl + '/index.php';
                return;
            }

            $("#CO_MUNICIPIO_NASCIMENTO option").remove();
            var labelHTML = $(html).find("#CO_MUNICIPIO_NASCIMENTO option");
            $("#CO_MUNICIPIO_NASCIMENTO").append(labelHTML);

            $("#CO_MUNICIPIO_NASCIMENTO").val($(html).find("#CO_MUNICIPIO_NASCIMENTO").val());


        }
    });


};

$('#SG_UF_ESCOLA').change(function () {
    $.ajax({
        type: "POST",
        url: baseUrl + '/index.php/secretaria/cadastrarcursista/renderiza-municipio/',
        data: {
            SG_UF_ESCOLA: $("#SG_UF_ESCOLA").val(),
        },
        success: function (html) {
            var find = $(html).find('#loginBase #login').html();
            if (find != null && find != '') {
                location.href = baseUrl + '/index.php';
                return;
            }
            $("#CO_MUNICIPIO_ESCOLA option").remove();
            var labelHTML = $(html).find("#CO_MUNICIPIO_ESCOLA option");
            $("#CO_MUNICIPIO_ESCOLA").append(labelHTML);
            $("#CO_MUNICIPIO_ESCOLA").val($(html).find("#CO_MUNICIPIO_ESCOLA").val());
        }
    });
});

//Pesquisa de mesorregião e rede de ensino por município
var municipioChange = function () {
    $.ajax({
        type: "POST",
        url: baseUrl
        + '/index.php/secretaria/cadastrarcursista/renderiza-mesorregiao/CO_MUNICIPIO_ESCOLA/'
        + $("#CO_MUNICIPIO_ESCOLA").val(),

        data: {
            CO_MUNICIPIO_ESCOLA: $("#CO_MUNICIPIO_ESCOLA").val(),
        },

        success: function (html) {
            var find = $(html).find('#loginBase #login').html();
            if (find != null && find != '') {
                location.href = baseUrl + '/index.php';
                return;
            }

            $("#NO_MESORREGIAO_ESCOLA option").remove();
            var labelHTML = $(html).find("#NO_MESORREGIAO_ESCOLA option");
            $("#NO_MESORREGIAO_ESCOLA").append(labelHTML);

            $("#NO_MESORREGIAO_ESCOLA").val($(html).find("#NO_MESORREGIAO_ESCOLA").val());

            $("#CO_REDE_ENSINO option").remove();
            var labelHTML = $(html).find("#CO_REDE_ENSINO option");
            $("#CO_REDE_ENSINO").append(labelHTML);

            $("#CO_REDE_ENSINO").val($(html).find("#CO_REDE_ENSINO").val());

        }
    });
};

var redeEnsinoChange = function () {

    $.ajax({
        type: "POST",
        url: baseUrl
        + '/index.php/secretaria/cadastrarcursista/renderiza-nome-escola/CO_REDE_ENSINO/'
        + $("#CO_REDE_ENSINO").val() + '/CO_MUNICIPIO_ESCOLA/' + $("#CO_MUNICIPIO_ESCOLA").val(),

        data: {
            CO_REDE_ENSINO: $("#CO_REDE_ENSINO").val(),
        },

        success: function (html) {
            var find = $(html).find('#loginBase #login').html();
            if (find != null && find != '') {
                location.href = baseUrl + '/index.php';
                return;
            }

            $("#CO_ESCOLA option").remove();
            var labelHTML = $(html).find("#CO_ESCOLA option");
            $("#CO_ESCOLA").append(labelHTML);

            $("#CO_ESCOLA").val($(html).find("#CO_ESCOLA").val());

            $("#CO_REDE_ENSINO").val($(html).find("#CO_REDE_ENSINO").val());

        }
    });


};


$("#btnAdicionar").click(function () {

    Dialog.modal(("<iframe width=\"100%\" height=\"480\" src=\"" + $(this).attr('href') + "\" frameBorder=\"0\" ></iframe>").replace(/\n/g, ''), 'Adicionar Cursista');
    $("#popup_content").attr("id", "popup_content2");
    $("#popup_content2").css("width", "500px;");

    return false;
});


//Adiciona máscaras aos campos
function initilizeMasks() {

    $("#NU_CPF").focusout(focusoutCPF);

    $("input.cpf").setMask("cpf");
    $("input.cnpj").setMask("cnpj");

    if ($('input.celular').length) {
        $("input.fonegeral").setMask("phone");

        if ($('input.celular').val().length < 11) {
            $('input.celular').setMask('(99) 9999-99999');
        } else {
            $('input.celular').setMask('(99) 99999-9999');
        }
        $('input.celular').bind('focusout', function () {
            if ($(this).val().length > 14) {
                $('input.celular').setMask('(99) 99999-9999');
            } else {
                $('input.celular').setMask('(99) 9999-99999');
            }
        });
    } else {
        $("input.fone").setMask("phone");
    }


    $("input.cep").setMask("cep");
    if ($("input.date").length) {
        $("input.date").datePicker({
            startDate: "01/01/1970"
        }).setMask({
            mask: "39/19/2999",
            autoTab: false
        });
    }
    $("input.decimal").setMask("decimal");
    $("input.signed-decimal").setMask("signed-decimal");
    $("input.decimal6").setMask({
        mask: "99,999",
        type: "reverse",
        defaultValue: "000"
    });
    $("input.decimal10").setMask({
        mask: "99,999.999",
        type: "reverse",
        defaultValue: "000"
    });
    $(".signed-decimal").keyup(
        function () {
            ($(".signed-decimal").val().indexOf("-") != -1) ? $(
                ".signed-decimal").addClass("formatValorNegativo") : $(
                ".signed-decimal").removeClass("formatValorNegativo")
        });
    if ($(".signed-decimal").val()) {
        ($(".signed-decimal").val().indexOf("-") != -1) ? $(".signed-decimal")
            .addClass("formatValorNegativo") : $(".signed-decimal")
            .removeClass("formatValorNegativo")
    }
    $("input.time").setMask({
        mask: "29:59:59",
        autoTab: false,
        setSize: true
    });
    $(".date").bind("change", function (d) {
        if (d.keyCode == 13) {
            return false
        }
        if ($(this).val() != "") {
            if (!Helper.Date.validate($(this).val())) {
                $this = $(this);
                $(this).val("");
                Dialog.alert("Data inválida", "Atenção", function () {
                    $this.focus()
                })
            }
        }
        return false
    }).bind("click", function () {
        $(this).trigger("change")
    }).bind("blur", function () {
        $(this).trigger("change")
    }).bind("keydown", function (d) {
        return !(d.keyCode == 13)
    });
    $(".pickList").pickList();
    $("textarea[maxlength]").not(".editorHtml").each(function () {
        Helper.displayLength($(this), $(this).attr("maxlength"))
    });

    $('#NO_USUARIO_CONFIRM').bind('copy paste cut drop', function (event) {
        event.preventDefault();
        this.value = '';
    });

    $('#DS_EMAIL_USUARIO_CONFIRM').bind('copy paste cut drop', function (event) {
        event.preventDefault();
        this.value = '';
    });
}

//Bloquear cópia nos campos de confirmação do nome
$(document).ready(function () {
    $('#NO_USUARIO_CONFIRM').bind('copy paste cut drop', function (event) {
        event.preventDefault();
        this.value = '';
    });
    initilizeMasks();
});

//Bloquear cópia nos campos de confirmação do e-mail
$(document).ready(function () {
    $('#DS_EMAIL_USUARIO_CONFIRM').bind('copy paste cut drop', function (event) {
        event.preventDefault();
        this.value = '';
    });
});


//MODAL DE CONFIRMAÇÃO DE EXCLUSÃO.
$(".icoExcluir").click(function () {
    if ($(this).attr('mensagem') != '') {
        var link = this;
        Dialog.confirm($(this).attr('mensagem'), 'Confirma\xE7\xE3o', function (r) {
            if (r == true) {
                location.href = link.href;
            }
        });
        return false;
    }
});


$("#SG_UF_NASCIMENTO").change(ufChange);
$("#NU_CPF").focusout(focusoutCPF);
$("#CO_MUNICIPIO_ESCOLA").change(municipioChange);
$("#CO_REDE_ENSINO").change(redeEnsinoChange);

if ($(".msgSucesso p").html() == 'FECHAR_POPUP') {
    $("#preLoader").html('');
    $.alerts._hide();
    window.top.location.href = baseUrl + '/index.php/secretaria/vinccursistaturma/carregar-turma/NU_SEQ_TURMA/' + $(html).find("#NU_SEQ_TURMA").val();
}
