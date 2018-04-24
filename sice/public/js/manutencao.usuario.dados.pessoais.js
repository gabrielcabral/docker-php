
// ABA DADOS PESSOAIS
	
var focusoutCPF =
		function() {
			if($(this).val() != ''){

				$.ajax({
	        		url :baseUrl
					+ '/index.php/manutencao/usuario/obter-informacoes-por-cpf/NU_CPF/'
					+ $("#NU_CPF").val(),
					contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
			        data : {
			        	NU_CPF: $("#NU_CPF").val(),
			        	NO_USUARIO: $("#NO_USUARIO").val(),
						NO_USUARIO_CONFIRM: $("#NO_USUARIO_CONFIRM").val(),
						CO_ESTADO_CIVIL: $("#CO_ESTADO_CIVIL").val(),
						DT_NASCIMENTO: $("#DT_NASCIMENTO").val(),
						CO_SEXO_USUARIO: $("#CO_SEXO_USUARIO").val(),
						SG_UF_NASCIMENTO: $("#SG_UF_NASCIMENTO").val(),
						CO_MUNICIPIO_NASCIMENTO: $("#CO_MUNICIPIO_NASCIMENTO").val(),
						NO_MAE: $("#NO_MAE").val()
					},
	        		success : function(data) {
	        			var find = $(data).find('#loginBase #login').html();
	        			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
	        			
	        			$('#abaDadosPessoais').html($(data).find('#abaDadosPessoais').html());
	        			$("#NU_CPF").focusout(focusoutCPF);
	        			$("#NU_CPF").setMask('cpf');
	                	$("#SG_UF_NASCIMENTO").change(ufChange);
	                    $('#NO_USUARIO_CONFIRM').bind('copy paste cut drop', function(event) {
	                        event.preventDefault();
	                        this.value = '';
	                    });
	                    
	                    $("#NO_USUARIO").after("<br><span class='msgOrientacao'>Nome na Receita Federal</span>");
	                    $("#NO_USUARIO_CONFIRM").after("<br><span class='msgOrientacao'>Confirmação de nome de acordo com a Receita Federal</span>");

						//Remove os campos tipo de Escolaridade e Tipo de instituição dos perfils que não é cursista.
						if($("input[type=hidden][name=TIPO_USUARIO]").val() != 7) {
							$('label[for="NU_SEQ_FORMACAO_ACADEMICA"]').hide();
							$('label[for="TP_INSTITUICAO"]').hide();
						}
	        		}
	        	});
			}
		};

var ufChange = function() {
	$.ajax({
        type: "POST",
        url: baseUrl + '/index.php/manutencao/usuario/renderiza-municipio-cad/SG_UF_NASCIMENTO/' + $("#SG_UF_NASCIMENTO").val() ,
        
        data : {
			SG_UF_NASCIMENTO: $("#SG_UF_NASCIMENTO").val(),
		},
        
        success: function(html){
        	var find = $(html).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

			$("#CO_MUNICIPIO_NASCIMENTO option").remove();
			var labelHTML = $(html).find("#CO_MUNICIPIO_NASCIMENTO option");
			$("#CO_MUNICIPIO_NASCIMENTO").append(labelHTML);
			$("#CO_MUNICIPIO_NASCIMENTO").val("");

        }
    });

};    
		
$("#NU_CPF").focusout(focusoutCPF);
$("#SG_UF_NASCIMENTO").change(ufChange);

$(document).ready(function() {
    $('#NO_USUARIO_CONFIRM').bind('copy paste cut drop', function(event) {
        event.preventDefault();
        this.value = '';
    });

	//Remove os campos tipo de Escolaridade e Tipo de instituição dos perfils que não é cursista.
	if($("input[type=hidden][name=TIPO_USUARIO]").val() != 7) {
		$('label[for="NU_SEQ_FORMACAO_ACADEMICA"]').hide();
		$('label[for="TP_INSTITUICAO"]').hide();
	}

});


$("#NO_USUARIO").after("<br><span class='msgOrientacao'>Nome na Receita Federal</span>");
$("#NO_USUARIO_CONFIRM").after("<br><span class='msgOrientacao'>Confirmação de nome de acordo com a Receita Federal</span>");
