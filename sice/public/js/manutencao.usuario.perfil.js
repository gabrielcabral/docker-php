//ABA PERFIL

var sucesso =  function(html){
	var find = $(html).find('#loginBase #login').html();
	if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
	
	$("#abaPerfil").html($(html).find("#abaPerfil").html());
   	renderizaCampoRepresentacao();
	renderizaAbaPagamento();
	removerDadosAbaPagamento();
	$("#abaPerfil").find("#SG_UF_ATUACAO_PERFIL_CAD").change(function() {
		removerDadosAbaPagamento();
		$.ajax({
	        type: "POST",
	        url: baseUrl + '/index.php/manutencao/usuario/renderiza-municipio-cad',
	        data : {
	        		NU_SEQ_TIPO_PERFIL :  $("#NU_SEQ_TIPO_PERFIL").val(),
	        		SG_UF_ATUACAO_PERFIL_CAD : $("#SG_UF_ATUACAO_PERFIL_CAD").val(),
	        		CO_REPRESENTACAO_CAD : $("#CO_REPRESENTACAO_CAD").val(),
	        },
	        success: sucesso
	    });

	}    	
	);
	$("#abaPerfil").find("#CO_MUNICIPIO_PERFIL").change(function() {
		
		removerDadosAbaPagamento();
		$.ajax({
	        type: "POST",
	        url: baseUrl
			+ '/index.php/manutencao/usuario/renderiza-mesoregiao-cad',
	        data : {
	        		NU_SEQ_TIPO_PERFIL :  $("#NU_SEQ_TIPO_PERFIL").val(),
	        		SG_UF_ATUACAO_PERFIL_CAD : $("#SG_UF_ATUACAO_PERFIL_CAD").val(),
	        		CO_REPRESENTACAO_CAD : $("#CO_REPRESENTACAO_CAD").val(),
	        		CO_MUNICIPIO_PERFIL: $("#CO_MUNICIPIO_PERFIL").val()
	        },
	        success: sucesso
	    });
	});
	$("#NU_SEQ_TIPO_PERFIL").change(
			function() {
				renderizaAbaPagamento();
				renderizaCampoRepresentacao();
			}
	);
};


var ufChange = function() {
	$.ajax({
        type: "POST",
        url: baseUrl + '/index.php/manutencao/usuario/renderiza-municipio-cad',
        data : {
	    		NU_SEQ_TIPO_PERFIL :  $("#NU_SEQ_TIPO_PERFIL").val(),
	    		SG_UF_ATUACAO_PERFIL_CAD : $("#SG_UF_ATUACAO_PERFIL_CAD").val(),
	    		CO_REPRESENTACAO_CAD : $("#CO_REPRESENTACAO_CAD").val(),
	    },
        success: sucesso
    });

};    	
$("#SG_UF_ATUACAO_PERFIL_CAD").change(ufChange);

var municipioChange = function() {
	$.ajax({
        type: "POST",
        url: baseUrl
		+ '/index.php/manutencao/usuario/renderiza-mesoregiao-cad/NU_SEQ_TIPO_PERFIL/',
        data : {
	    		NU_SEQ_TIPO_PERFIL :  $("#NU_SEQ_TIPO_PERFIL").val(),
	    		SG_UF_ATUACAO_PERFIL_CAD : $("#SG_UF_ATUACAO_PERFIL_CAD").val(),
	    		CO_REPRESENTACAO_CAD : $("#CO_REPRESENTACAO_CAD").val(),
	    		CO_MUNICIPIO_PERFIL: $("#CO_MUNICIPIO_PERFIL").val()
	    },
        success: sucesso
    });
};

$("#CO_MUNICIPIO_PERFIL").change(municipioChange);

function renderizaCampoRepresentacao() {
	if($("#NU_SEQ_TIPO_PERFIL").val() == "4" || $("#NU_SEQ_TIPO_PERFIL").val() == "8"){
		$("#CO_REPRESENTACAO_CAD").show();
		$("label[for=CO_REPRESENTACAO_CAD]").show();
	} else {
		$("#CO_REPRESENTACAO_CAD").hide();
		$("label[for=CO_REPRESENTACAO_CAD]").hide();
	}
}

function renderizaAbaPagamento() {
	if($("#NU_SEQ_TIPO_PERFIL").val() == "4" || $("#NU_SEQ_TIPO_PERFIL").val() == "8"){
		$("#liDadosPagamento").show();
	} else if($("#NU_SEQ_TIPO_PERFIL").val() == "5") {
		$("#liDadosPagamento").show();
	} else if($("#NU_SEQ_TIPO_PERFIL").val() == "6") {
		$("#liDadosPagamento").show();
	} else {
		$("#liDadosPagamento").hide();
		removerDadosAbaPagamento();
	}
}

renderizaCampoRepresentacao();
renderizaAbaPagamento();

$("#NU_SEQ_TIPO_PERFIL").change(
		function() {
			renderizaAbaPagamento();
			renderizaCampoRepresentacao();
		}
	);

function removerDadosAbaPagamento(){
	$("#CO_DISTANCIA_PAGAMENTO").val("");
		
	var labelUfPag = $("#SG_UF_PAGAMENTO");
    $(labelUfPag).find('option').remove();
    labelUfPag.append("<option value='' selected=''> Selecione </option>");
    
    var labelMuPag = $("#CO_MUNICIPIO_PAGAMENTO");
    $(labelMuPag).find('option').remove();
    labelMuPag.append("<option value='' selected=''> Selecione </option>");
    
    var labelAgPag = $("#CO_AGENCIA_PAGAMENTO");
    $(labelAgPag).find('option').remove();
    labelAgPag.append("<option value='' selected=''> Selecione </option>");
    
    $(document).ready(function() {
        $("#CO_DISTANCIA_PAGAMENTO").change(function(){
            if($("#CO_MUNICIPIO_PERFIL").val() == ""){
                Dialog.error('É necessário informar o município de atuação do perfil');
            } else {
                carregaUFs($("#CO_MUNICIPIO_PERFIL").val(), this.value);
            }
        });
        
        $("#SG_UF_PAGAMENTO").change(function(){
            carregaMunicipios($("#CO_MUNICIPIO_PERFIL").val(), $("#CO_DISTANCIA_PAGAMENTO").val(),this.value);
        });
        
        $("#CO_MUNICIPIO_PAGAMENTO").change(function(){
            carregaAgencias($("#CO_MUNICIPIO_PERFIL").val(), this.value,$("#CO_DISTANCIA_PAGAMENTO").val(),$("#SG_UF_PAGAMENTO").val());
        });
    });
    
}