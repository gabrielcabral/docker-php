// Mensagem de confirmação - solicitar autorização
$("#confirmar").click(function() {
	if($(this).attr('mensagem') != ""){
		Dialog.confirm($(this).attr('mensagem'),'Confirmação', function(r){
			if(r == true){
			    $("#form").submit();
			}
		});
		return false;
	}
});
