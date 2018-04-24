//Script para fazer com que seja exibida mensagem de confirmação ao se acionar o ícone de excluir
$("#regeitarcancelamento").click(function(){
	var link = baseUrl + "/index.php/secretaria/cancelarturma/rejeitar-cancelar-turma/NU_SEQ_TURMA/" + $("#NU_SEQ_TURMA").val();
	Dialog.confirm($(this).attr('mensagem'),'Confirmação', function(r){
		if(r == true){
			location.href = link;
		}
	});
	return false;
});