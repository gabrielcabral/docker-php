//Script para direcionar para o form de n�o autorizar turma
$("#naoautorizar").click(function(){
	var link = baseUrl + "/index.php/secretaria/autorizarnaoautorizar/carregar-nao-autorizar/NU_SEQ_TURMA/" + $("#NU_SEQ_TURMA").val();
	location.href = link;
	return false;
});

//Script para fazer com que seja exibida mensagem de confirma��o ao se acionar o bota�o de confirmar autoriza��o
$("#confirmarautorizar").click(function(){
	var link = baseUrl + "/index.php/secretaria/autorizarnaoautorizar/autorizar-turma/NU_SEQ_TURMA/" + $("#NU_SEQ_TURMA").val();
	
	Dialog.confirm($(this).attr('mensagem'),'Confirma��o', function(r){
		if(r == true){
			location.href = link;
		}
	});
	return false;
});