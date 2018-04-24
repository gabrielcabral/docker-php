//Tela de Termo de aceite - Verificação de checkbox
$("#CK_ACEITACAO-1").click(function(){
    if($("#CK_ACEITACAO-1").attr('checked')){
        $("#confirmar").removeAttr('disabled');
    }else{
        $("#confirmar").attr('disabled','disabled');
    }
});

//Tela de Pesquisa de Bolsas - Renderização do campo de Período de Vinculação
var anoExercicioChange = function() {
    
    popUp.carregando();
    
    $.ajax({
        type: "POST",
        url: baseUrl + '/index.php/financeiro/bolsa/carrega-periodo-vinc-por-ano/VL_EXERCICIO/' + $("#VL_EXERCICIO").val() ,
        data : {
            UF_TURMA: $("#VL_EXERCICIO").val(),
        },
        success: function(html){
        	var find = $(html).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
			
			$("#NU_SEQ_PERIODO_VINCULACAO option").remove();
			var labelHTML = $(html).find("#NU_SEQ_PERIODO_VINCULACAO option");
			$("#NU_SEQ_PERIODO_VINCULACAO").append(labelHTML);
			$("#NU_SEQ_PERIODO_VINCULACAO").val($(html).find("#NU_SEQ_PERIODO_VINCULACAO").val());

			popUp.carregando();
            
        }
    });
};

//Renderização do campo de Mesorregião
var regiaoChange = function() {
    
    popUp.carregando();
    
    $.ajax({
        type: "POST",
        url: baseUrl + '/index.php/financeiro/bolsa/renderiza-uf/SG_REGIAO/' ,
        data : {
            SG_REGIAO: $("#SG_REGIAO").val(),
        },
        success: function(html){
            var find = $(html).find('#loginBase #login').html();
            if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
            
            $("#SG_UF option").remove();
            var labelHTML = $(html).find("#SG_UF option");
            $("#SG_UF").append(labelHTML);
            $("#SG_UF").val($(html).find("#SG_UF").val());
            
            $("#SG_UF").change(ufChange);
            
            $("#CO_MESORREGIAO option").remove();
			var labelHTMLmeso = $(html).find("#CO_MESORREGIAO option");
			$("#CO_MESORREGIAO").append(labelHTMLmeso);
			$("#CO_MESORREGIAO").val($(html).find("#CO_MESORREGIAO").val());
			
            popUp.carregando();
            
        }
    });
};

//Renderização do campo de Mesorregião
var ufChange = function() {
    
    popUp.carregando();
    
    $.ajax({
        type: "POST",
        url: baseUrl + '/index.php/financeiro/bolsa/renderiza-mesorregiao/SG_UF/' ,
        data : {
            SG_UF: $("#SG_UF").val(),
        },
        success: function(html){
        	var find = $(html).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
			
            var label = $("label[for=CO_MESORREGIAO]");
            $(label).find('select').remove();
            var labelHTML = $(html).find("label[for=CO_MESORREGIAO]");
            label.append($(labelHTML).find('select'));
            
            popUp.carregando();
            
        }
    });
};

$("#VL_EXERCICIO").change(anoExercicioChange);
$("#SG_UF").change(ufChange);
$("#SG_REGIAO").change(regiaoChange);

//Combo mais ações
$("#table_button").click(function(){
	if($('input[name=IDENTIFICADOR_LINHA]:checked').val() ==  null){
			Dialog.error('Nenhum registro selecionado.', null, null);
	}else{
		$('input[name=IDENTIFICADOR_LINHA]').attr('name','IDENTIFICADOR_LINHA[]');
		$("#table_button")[0].form.action = $('#table_main_action').val();
		$("#table_button")[0].form.submit();
	}
});
//Fim combo mais ações

//Função enviada pelo Janio para adicionar um checkbox que selecione todos nesta tela
//dataTablesObj = $('#edit');
//Helper.DataTable(dataTablesObj,true,true,{
//    "fnInitComplete": function(oSettings, json) {
//        th = $(dataTablesObj).find('thead th:first');
//        th.removeAttr('class');
//        th.html($('<input />').attr('type','checkbox').bind('click',function(){
//            val = $(this).attr('checked');
//            $(this).parent().unbind();
//            $('#id_da_tabela tbody input[type=checkbox]').attr('checked',val);
//        }));
//    }
//});
