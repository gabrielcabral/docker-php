/*
Sistema SICEWEB
Setor responsável: SGETI/FNDE
Analista / Programador: Tiago Augusto Ramos ()
E-Mail: tiago.ramos@cpmbraxis.com
Finalidade: Funções de validação em Javascript
Data de criação: 02/04/2012
*/

$(".icoAceitar").each(function(){
	if(this.href.indexOf('ST_CONFIGURACAO/Inativo')> 0){
		$(this).hide();
	}
	
	
	
});

$(".icoAceitar").click(function(){
	var link = this;
	Dialog.confirm($(this).attr('mensagem'),'Confirmação', function(r){
		if(r == true){
			location.href = link.href;
		}
	});
	return false;
});
