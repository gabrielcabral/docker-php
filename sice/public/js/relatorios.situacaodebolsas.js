$(document).ready(function() {
    $('#CO_MUNICIPIO').change(function () {
        $.ajax({
            type: "POST",
            url: baseUrl + '/index.php/manutencao/usuario/municipio-change/',
            data: {
                CO_MUNICIPIO_PERFIL: $("#CO_MUNICIPIO").val()
            },
            success: function (html) {
                if (html != '') {
                    $("#CO_MESORREGIAO").val(html);
                }
            }
        });
    });

    $('#CO_MESORREGIAO').change(function () {
        $.ajax({
            url: baseUrl + '/index.php/manutencao/usuario/renderiza-municipio/CO_MESORREGIAO/' + $("#CO_MESORREGIAO").val() + '/SG_UF_ATUACAO_PERFIL/' + $("#UF_TURMA").val(),
            data: {
                CO_MESORREGIAO: $("#CO_MESORREGIAO").val(),
                SG_UF_ATUACAO_PERFIL: $("#UF_TURMA").val()
            },
            success: function (data) {
                var find = $(data).find('#loginBase #login').html();
                if (find != null && find != '') {
                    location.href = baseUrl + '/index.php';
                    return;
                }

                $("#CO_MUNICIPIO option").remove();
                var labelHTML = $(data).find("#CO_MUNICIPIO_PERFIL option");
                $("#CO_MUNICIPIO").append(labelHTML);

                $("#CO_MUNICIPIO").val($(data).find("#CO_MUNICIPIO_PERFIL").val());
            }
        });
    });

    $('#UF_TURMA').change(function () {
        //Método que faz o efeito de loading.
        popUp.carregando();

        $.ajax({
            url: baseUrl + '/index.php/manutencao/usuario/renderiza-mesoregiao/SG_UF_ATUACAO_PERFIL/' + $("#UF_TURMA").val(),
            data: {
                SG_UF_ATUACAO_PERFIL: $("#UF_TURMA").val()
            },
            success: function (data) {
                var find = $(data).find('#loginBase #login').html();
                if (find != null && find != '') {
                    location.href = baseUrl + '/index.php';
                    return;
                }

                $("#CO_MESORREGIAO option").remove();
                var labelHTML = $(data).find("#CO_MESORREGIAO option");
                $("#CO_MESORREGIAO").append(labelHTML);

                $("#CO_MESORREGIAO").val($(data).find("#CO_MESORREGIAO").val());

                $("#CO_MUNICIPIO option").remove();
                var labelHTMLMunicipio = $(data).find("#CO_MUNICIPIO_PERFIL option");
                $("#CO_MUNICIPIO").append(labelHTMLMunicipio);

                $("#CO_MUNICIPIO").val($(data).find("#CO_MUNICIPIO_PERFIL").val());

                //Termina o efeito de loading.
                popUp.carregando();
            }
        });
    });

    $('#SG_REGIAO').change(function () {
        popUp.carregando();

        $.ajax({
            type: "POST",
            url: baseUrl + '/index.php/financeiro/bolsa/renderiza-uf/SG_REGIAO/',
            data: {
                SG_REGIAO: $("#SG_REGIAO").val(),
            },
            success: function (html) {
                var find = $(html).find('#loginBase #login').html();
                if (find != null && find != '') {
                    location.href = baseUrl + '/index.php';
                    return;
                }

                $("#UF_TURMA option").remove();
                var labelHTML = $(html).find("#SG_UF option");
                $("#UF_TURMA").append(labelHTML);
                $("#UF_TURMA").val($(html).find("#SG_UF").val());

                $("#CO_MESORREGIAO option").remove();
                var labelHTMLmeso = $(html).find("#CO_MESORREGIAO option");
                $("#CO_MESORREGIAO").append(labelHTMLmeso);
                $("#CO_MESORREGIAO").val($(html).find("#CO_MESORREGIAO").val());

                popUp.carregando();
            }
        });
    });

    // HOOK Botões
    $('#consultar').click(function(){
        $('#situacaodebolsasform').attr('action', '').submit();
    });

    $('#exportar').click(function(){
        $('#situacaodebolsasform').attr('action', baseUrl + '/relatorios/situacaodebolsas/situacaodebolsas/excel/1').submit();
    });
});