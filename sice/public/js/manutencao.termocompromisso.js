$(".icoExcluir").click(function() {
	if($(this).attr('mensagem') != ''){
		var link = this;
		Dialog.confirm($(this).attr('mensagem'), 'Confirma��o', function(r) {
			if (r == true) {
				location.href = link.href;
			}
		});
		return false;
	}
});