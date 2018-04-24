//Script para direcionar para o form de não autorizar turma
$("#naoautorizar").click(function(){
	var link = baseUrl + "/index.php/secretaria/autorizarnaoautorizar/carregar-nao-autorizar/NU_SEQ_TURMA/" + $("#NU_SEQ_TURMA").val();
	location.href = link;
	return false;
});

//Script para fazer com que seja exibida mensagem de confirmação ao se acionar o botaão de confirmar autorização
$("#confirmarautorizar").click(function(){
	var link = baseUrl + "/index.php/secretaria/autorizarnaoautorizar/autorizar-turma/NU_SEQ_TURMA/" + $("#NU_SEQ_TURMA").val();
	
	Dialog.confirm($(this).attr('mensagem'),'Confirmação', function(r){
		if(r == true){
			location.href = link;
		}
	});
	return false;
});