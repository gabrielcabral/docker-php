// Mensagem de confirma��o - solicitar autoriza��o
$("#confirmar").click(function() {
	if($(this).attr('mensagem') != ""){
		Dialog.confirm($(this).attr('mensagem'),'Confirma��o', function(r){
			if(r == true){
			    $("#form").submit();
			}
		});
		return false;
	}
});
