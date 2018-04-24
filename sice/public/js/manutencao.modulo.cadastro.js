$("#DS_PREREQUISITO_MODULO").change(function() {
	if ($(this).val() == "S") {
		$("#NU_SEQ_MODULO_PREREQUISITO").show();
		$("label[for=NU_SEQ_MODULO_PREREQUISITO]").show();
	} else {
		$("#NU_SEQ_MODULO_PREREQUISITO").hide();
		$("label[for=NU_SEQ_MODULO_PREREQUISITO]").hide();
	}
});

//Visualizar Certificado
$("#visualizarCertificado").click(function(){
	window.open('','pdfCertificado','');
	var act = this.form.action;
	this.form.action = baseUrl + '/index.php/manutencao/modulo/visualizar-certificado/';
	this.form.target = 'pdfCertificado';
	this.form.submit();
	this.form.target = '_self';
	this.form.action = act;
});
// fim Visualizar Certificado

$("#VL_CARGA_HORARIA").after("<br><span class='msgOrientacao'>Total carga horária.</span>");
$("#VL_CARGA_PRESENCIAL").after("<br><span class='msgOrientacao'>Carga horária presencial mínima.</span>");
$("#VL_CARGA_DISTANCIA").after("<br><span class='msgOrientacao'>Carga horária a distância mínima.</span>");
$("#VL_MIN_CONCLUSAO").after("<br><span class='msgOrientacao'>Em dias.</span>");
$("#VL_MAX_CONCLUSAO").after("<br><span class='msgOrientacao'>Em dias.</span>");

function exibirComboModulosReq(){
	if ($("#DS_PREREQUISITO_MODULO").val() == "S") {
		$("#NU_SEQ_MODULO_PREREQUISITO").show();
		$("label[for=NU_SEQ_MODULO_PREREQUISITO]").show();
	} else if($("#DS_PREREQUISITO_MODULO").val() == "N") {
		$("#NU_SEQ_MODULO_PREREQUISITO").hide();
		$("label[for=NU_SEQ_MODULO_PREREQUISITO]").hide();
	} else {
		$("#NU_SEQ_MODULO_PREREQUISITO").hide();
		$("label[for=NU_SEQ_MODULO_PREREQUISITO]").hide();
	}
}

exibirComboModulosReq();

//Exibir mensagem de confimação ao editar módulo se ele estiver vinculado
$("#confirmar").click(function(){
	if($(this).attr('mensagem') != ""){
		Dialog.confirm($(this).attr('mensagem'),'Confirmação', function(r){
			if(r == true){
			    $("#form").find(':disabled').attr('disabled',false);
				$("#form").submit();
			}
		});
		return false;
	}else{
		$("#form").submit();
	}
});


$(document).ready(function(){$('#visualizarCertificado').text("Pré-visualizar Certificado");});


$('#edit tr').each(
		function() {
			if ($(this).children('td').length > 0) {
				$($(this).children('td')[4]).css('text-align', 'center');
				$($(this).children('td')[5]).css('text-align', 'center');
				$($(this).children('td')[6]).css('text-align', 'center');
			}
		}
);
