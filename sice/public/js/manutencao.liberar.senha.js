//Tela de filtro

var ufChange = function() {
	//Efeito de loading.
	popUp.carregando();
	
	$.ajax({
        
		url: baseUrl + '/index.php/manutencao/liberarsenha/renderiza-mesoregiao/',
		data : {
			SG_UF_ATUACAO_PERFIL: $("#SG_UF_ATUACAO_PERFIL").val(),
		},
        
        success: function(data) {
        	$('#CO_MESORREGIAO option').remove();
        	$('#CO_MESORREGIAO').append('<option value="">Selecione</option>');
        	options = data['MESORREGIAO'];
        	for (var i=0; i < options.length; i++) {
        		$('#CO_MESORREGIAO').append('<option value="' + options[i][0] + '">' + options[i][1] + '</option>');
			}
        	
        	$('#CO_MUNICIPIO_PERFIL option').remove();
        	$('#CO_MUNICIPIO_PERFIL').append('<option value="">Selecione</option>');
        	options = data['MUNICIPIO'];
        	for (var i=0; i < options.length; i++) {
        		$('#CO_MUNICIPIO_PERFIL').append('<option value="' + options[i][0] + '">' + options[i][1] + '</option>');
			}
        	
        	//Limpando o combo Turma.
        	$(limpaTurma);
        	
        	//Termina o efeito de loading.
			popUp.carregando();
        }
    });
};

var mesorregiaoChange = function() {
	//Efeito de loading.
	popUp.carregando();
	
	$.ajax({
        
		url: baseUrl + '/index.php/manutencao/liberarsenha/renderiza-municipio/',
		data : {
        	CO_MESORREGIAO: $("#CO_MESORREGIAO").val(),
        	SG_UF_ATUACAO_PERFIL: $("#SG_UF_ATUACAO_PERFIL").val(),
		},
        
        success: function(data) {
        	$('#CO_MUNICIPIO_PERFIL option').remove();
        	$('#CO_MUNICIPIO_PERFIL').append('<option value="">Selecione</option>');
        	options = data['MUNICIPIO'];
        	for (var i=0; i < options.length; i++) {
        		$('#CO_MUNICIPIO_PERFIL').append('<option value="' + options[i][0] + '">' + options[i][1] + '</option>');
			}
        	
        	//Limpando o combo Turma.
        	$(limpaTurma);
        	
        	//Termina o efeito de loading.
			popUp.carregando();
        }
    });
};

var municipioChange = function() {
	//Efeito de loading.
	popUp.carregando();
	
	$.ajax({

		url: baseUrl + '/index.php/manutencao/liberarsenha/municipio-change/',
		data : {
			CO_MUNICIPIO_PERFIL: $("#CO_MUNICIPIO_PERFIL").val(),
			SG_UF_ATUACAO_PERFIL: $("#SG_UF_ATUACAO_PERFIL").val(),
		},
        			
        success: function(data) {
        	$("#CO_MESORREGIAO").val(data['MESORREGIAO_VAL']);
        	
        	$('#NU_SEQ_TURMA option').remove();
        	$('#NU_SEQ_TURMA').append('<option value="">Selecione</option>');
        	options = data['TURMA'];
        	if (options) {
	        	for (var i=0; i < options.length; i++) {
	        		$('#NU_SEQ_TURMA').append('<option value="' + options[i][0] + '">' + options[i][1] + '</option>');
				}
        	}
        	
        	//Termina o efeito de loading.
			popUp.carregando();
        }
    });
};

var limpaTurma = function() {
	$('#NU_SEQ_TURMA option').remove();
	$('#NU_SEQ_TURMA').append('<option value="">Selecione</option>');
};

$("#SG_UF_ATUACAO_PERFIL").change(ufChange);
$("#CO_MESORREGIAO").change(mesorregiaoChange);
$("#CO_MUNICIPIO_PERFIL").change(municipioChange);

//Combo mais ações
$("#table_main_action").change(function(){
	if($('#table_main_action').val() == null || $('#table_main_action').val() == ''){
		$("#table_button").attr("disabled","disabled");
	}else{
		$("#table_button").removeAttr("disabled");	
	}
});

$("#table_button").click(function(){
	if($('input[name=NU_SEQ_USUARIO]:checked').val() ==  null){
		Dialog.error('Selecione pelo menos 1 usuário para liberar ou renovar acesso.', null, null);
	}else{
		$('input[name=NU_SEQ_USUARIO]').attr('name','NU_SEQ_USUARIO[]');		
		$("#table_button")[0].form.action = $('#table_main_action').val();
		$("#table_button")[0].form.submit();
	}
});
//Fim combo mais ações