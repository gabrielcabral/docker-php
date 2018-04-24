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

    $('#CO_MUNICIPIO, #CO_REDE_ENSINO').change(function () {
        if($('#CO_REDE_ENSINO').val() && $('#CO_MUNICIPIO').val()){
            popUp.carregando();

            $.ajax({
                url: baseUrl + '/index.php/relatorios/dadosdoscursistas/nomeescolachange',
                type: 'POST',
                data: {
                    SG_UF_ESCOLA: $("#UF_TURMA").val(),
                    CO_MUNICIPIO_ESCOLA: $("#CO_MUNICIPIO").val(),
                    CO_REDE_ENSINO: $("#CO_REDE_ENSINO").val()
                },
                success: function (data) {
                    var find = $(data).find('#loginBase #login').html();
                    if (find != null && find != '') {
                        location.href = baseUrl + '/index.php';
                        return;
                    }

                    $("#CO_ESCOLA option").remove();
                    var labelHTML = $(data).find("#CO_ESCOLA option");
                    $("#CO_ESCOLA").append(labelHTML);
                    $("#CO_ESCOLA").val("");

                    popUp.carregando();
                }
            });
        }
    });


    // HOOK Botões
    $('#consultar').click(function(){
        $('#dadosdoscursistasform').attr('action', '').submit();
    });

    $('#exportar').click(function(){
        $('#dadosdoscursistasform').attr('action', baseUrl + '/relatorios/dadosdoscursistas/dadosdoscursistas/excel/1').submit();
    });
});