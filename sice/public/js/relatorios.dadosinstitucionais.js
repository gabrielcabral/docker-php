$(document).ready(function() {

    // Verificar se a turma Existe e desabilita os demais campos caso exista.
    $("#NU_SEQ_TURMA").focusout(function() {
        $.ajax({
            type: "POST",
            url: baseUrl + '/index.php/secretaria/turma/buscarturma/id/' + $("#NU_SEQ_TURMA").val(),
            data: {
                NU_SEQ_TURMA: $("#NU_SEQ_TURMA").val()
            },
            success: function (data) {
                //alert(data);
                if(data){
                    $("#CO_MUNICIPIO").attr('disabled', 'disabled');
                    $("#CO_MESORREGIAO").attr('disabled', 'disabled');
                    $("#CO_REDE_ENSINO").attr('disabled', 'disabled');
                    $("input[name='SG_UF[]").attr('disabled', 'disabled');
                    $("#filter_todos").attr('disabled', 'disabled');
                    $("#CO_MUNICIPIO").val("");
                    $("#CO_MESORREGIAO").val("");
                    $("#CO_REDE_ENSINO").val("");
                }else{
                    $("#CO_MUNICIPIO").removeAttr('disabled');
                    $("#CO_MESORREGIAO").removeAttr('disabled');
                    $("#CO_REDE_ENSINO").removeAttr('disabled');
                    $("input[name='SG_UF[]").removeAttr('disabled');
                    $("#filter_todos").removeAttr('disabled');
                }
            }
        });
    });

    // Desabilita os campos de Data caso seja selecionado o ano
    $("#NU_ANO").change(function(){
        if($("#NU_ANO").val() != ''){
            $("#DT_INICIO").attr('disabled', 'disabled');
            $("#DT_FIM").attr('disabled', 'disabled');
            $("#DT_INICIO").val("");
            $("#DT_FIM").val("");
        }else{
            $("#DT_INICIO").removeAttr('disabled');
            $("#DT_FIM").removeAttr('disabled');
        }
    });
    // Desabilita os campos de Ano caso seja selecionado uma data inicio
    $("#DT_FIM").change(function(){
        if($("#DT_FIM").val() != ''){
            $("#NU_ANO").attr('disabled', 'disabled');
            $("#NU_ANO").val("");
        }else{
            $("#NU_ANO").removeAttr('disabled');
        }
    });

    //Busca somente as mesorregioes dos estados selecionados.
    $("#CO_MESORREGIAO").focusin(function(){
        var checkeds = new Array();
        $("input[name='SG_UF[]']:checked").each(function ()
        {
            checkeds.push($(this).val());
        });
        $.ajax({
            type: "GET",
            url: baseUrl + '/index.php/secretaria/turma/ajaxmesorregiao',
            data: {'checkeds':checkeds},
            dataType: "json",
            success: function(json){
                var options = "";
                options += '<option value="">Selecione</option>';
                $.each(json, function(key, value){
                    options += '<option value="' + key + '">' + value + '</option>';
                });
                $("#CO_MESORREGIAO").html(options);
            }
        });
    });

    //Retorna os Municipios de acordo com a mesoregiao selecionada.
    $('#CO_MUNICIPIO').focusin(function () {
        //var checkeds = new Array();
        //$("input[name='SG_UF[]']:checked").each(function ()
        //{
        //    checkeds.push($(this).val());
        //});
        $.ajax({
            url: baseUrl + '/index.php/manutencao/usuario/renderiza-municipio/CO_MESORREGIAO/' + $("#CO_MESORREGIAO").val(),
            data: {
                CO_MESORREGIAO: $("#CO_MESORREGIAO").val()
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

    //Busca somente as mesorregioes dos estados selecionados.
    $("#CO_REDE_ENSINO").focusin(function(){

        $.ajax({
            type: "GET",
            url: baseUrl + '/index.php/secretaria/turma/ajaxmesorregiao',
            data: {'checkeds':checkeds},
            dataType: "json",
            success: function(json){
                var options = "";
                options += '<option value="">Selecione</option>';
                $.each(json, function(key, value){
                    options += '<option value="' + key + '">' + value + '</option>';
                });
                $("#CO_MESORREGIAO").html(options);
            }
        });
    });

});