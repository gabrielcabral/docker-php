//Script para fazer com que seja exibida mensagem de confirmação ao se acionar o ícone de excluir
$("#cancelar").on('click', function (){
	window.location = $(this).data('url');
});

$(".icoExcluir").click(function(){
	var link = this;
	Dialog.confirm($(this).attr('mensagem'),'Confirmação', function(r){
		if(r == true){
			location.href = link.href;
		}
	});
	return false;
});

$(".icoEditar").click(function(){
	var link = this;
	Dialog.confirm($(this).attr('mensagem'),'Confirmação', function(r){
		if(r == true){
			location.href = link.href;
		}
	});
	return false;
});