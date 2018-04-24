/*
Sistema SICEWEB
Setor responsável: SGETI/FNDE
Analista / Programador: Tiago Augusto Ramos ()
E-Mail: tiago.ramos@cpmbraxis.com
Finalidade: Funções de validação em Javascript
Data de criação: 10/04/2012
 */

function escondeCampos(){
	$("label[for=NO_INSTITUICAO]").hide();
	$("label[for=NO_CURSO]").hide();
	$("label[for=DT_CONCLUSAO]").hide();
}
function mostraCampos(){
	$("label[for=NO_INSTITUICAO]").show();
	$("label[for=NO_CURSO]").show();
	$("label[for=DT_CONCLUSAO]").show();
	$("#DT_CONCLUSAO").setMask({mask:"39/19/2999",autoTab:false});
}

escondeCampos();


$("#TP_ESCOLARIDADE").change(function(){
	if(this.value == '' || parseInt(this.value) < 5){
		escondeCampos();
	}else{
		mostraCampos();
	}
});

$("#confirmarFormacao").click(function(){
	var mensagem = "";
	var contErro = 0;
	
	var TP_ESCOLARIDADE = valorItem("TP_ESCOLARIDADE");
	var DS_TP_ESCOLARIDADE = textoItem("TP_ESCOLARIDADE");

	var TP_INSTITUICAO = null;
	var DS_TP_INSTITUICAO = null;
	
	$('input[name=TP_INSTITUICAO]:checked').each(function() {
		TP_INSTITUICAO = $(this).val();
		pai = $(this).parent();
		$(this).remove();
		DS_TP_INSTITUICAO = pai.html();
		pai.html('');
		pai.append($(this));
		pai.append(DS_TP_INSTITUICAO);
	});
	
	var NO_INSTITUICAO = $('input[name=NO_INSTITUICAO]').val();
	var NO_CURSO = $('input[name=NO_CURSO]').val();
	var DT_CONCLUSAO = $('input[name=DT_CONCLUSAO]').val();
	
	
	var erroCampoObrig = function(){
		$(this).addClass("campoErro");
		$(this).parent().find('.msgErro').remove();
		$(this).after(
				'<ul class="msgErro"><li>O campo &eacute; obrigat&oacute;rio e n&atilde;o pode estar vazio</li></ul></label>'
				);
	};
	
	var sucessoCampoObrig = function(){
		$(this).removeClass("campoErro");
		$(this).parent().find('.msgErro').remove();
	};
	
	var erro = false;
	if(TP_ESCOLARIDADE == ''){
		$("#TP_ESCOLARIDADE").each(erroCampoObrig);
		contErro++;
		mensagem += contErro+". "+$("label[for=TP_ESCOLARIDADE]").find('span').html().replace(":","")+"<br>";
		erro = true;
	}else{
		$("#TP_ESCOLARIDADE").each(sucessoCampoObrig);
	}
	
	if(TP_INSTITUICAO == null){
		$("input[name=TP_INSTITUICAO]").parent().parent().each(function(){
			contErro++;
			mensagem += contErro+". "+$("#TP_INSTITUICAO-1").parent().parent().find('legend').html().replace(":","")+"<br>";
			$(this).addClass("campoErro");
			$(this).parent().find('.msgErro').remove();
			$(this).after(
					'<ul class="msgErro" style="display:inline"><li>O campo &eacute; obrigat&oacute;rio e n&atilde;o pode estar vazio</li></ul></label>'
					);
		});
		erro = true;
	}else{
		$("input[name=TP_INSTITUICAO]").parent().parent().each(sucessoCampoObrig);
	}
	
	
	if(TP_ESCOLARIDADE != '' && parseInt(TP_ESCOLARIDADE) >= 5 ){

		if(NO_INSTITUICAO == ''){
			$("#NO_INSTITUICAO").each(erroCampoObrig);
			contErro++;
			mensagem += contErro+". "+$("label[for=NO_INSTITUICAO]").find('span').html().replace(":","")+"<br>";
			erro = true;
		}else{
			$("#NO_INSTITUICAO").each(sucessoCampoObrig);
		}
		
		if(NO_CURSO == ''){
			$("#NO_CURSO").each(erroCampoObrig);
			contErro++;
			mensagem += contErro+". "+$("label[for=NO_CURSO]").find('span').html().replace(":","")+"<br>";
			erro = true;
		}else{
			$("#NO_CURSO").each(sucessoCampoObrig);
		}
		
		if(DT_CONCLUSAO == ''){
			$("#DT_CONCLUSAO").each(erroCampoObrig);
			contErro++;
			mensagem += contErro+". "+$("label[for=DT_CONCLUSAO]").find('span').html().replace(":","")+"<br>";
			erro = true;
		}else{
			if(!Helper.Date.validate(DT_CONCLUSAO)){
				$("input[name=DT_CONCLUSAO]").each(function(){
					contErro++;
					mensagem += contErro+". "+$("label[for=DT_CONCLUSAO]").find('span').html().replace(":","")+"<br>";
					$(this).addClass("campoErro");
					$(this).parent().find('.msgErro').remove();
					$(this).after(
							'<ul class="msgErro"><li>Data inv&aacute;lida!</li></ul></label>'
							);
				});
				erro = true;		
			}else{
				$("#DT_CONCLUSAO").each(sucessoCampoObrig);
			}
		}
	}
	
	
	if(!erro){
		adicionaFormacao(
				TP_ESCOLARIDADE,
				DS_TP_ESCOLARIDADE,
				TP_INSTITUICAO,
				DS_TP_INSTITUICAO,
				NO_INSTITUICAO,
				NO_CURSO,
				DT_CONCLUSAO);
	}
	
	if(mensagem != ""){
		$('#divErroFA').css('display','');
		$('#camposErro').html(mensagem);
	}
	
});

function textoItem(select) {
	var retorno = null;
	$("#"+select+" option:selected").each(function() {
		retorno = $(this).text();
	});
	return retorno;
}
function valorItem(select) {
	var retorno = null;
	$("#"+select+" option:selected").each(function() {
		retorno = $(this).val();
	});
	return retorno;
}
