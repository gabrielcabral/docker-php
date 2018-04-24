/*
Sistema SICEWEB
Setor respons�vel: SGETI/FNDE
Analista / Programador: Tiago Augusto Ramos ()
E-Mail: tiago.ramos@cpmbraxis.com
Finalidade: Fun��es de valida��o em Javascript
Data de cria��o: 02/04/2012
*/

$(".icoAceitar").each(function(){
	if(this.href.indexOf('ST_CONFIGURACAO/Inativo')> 0){
		$(this).hide();
	}
	
	
	
});

$(".icoAceitar").click(function(){
	var link = this;
	Dialog.confirm($(this).attr('mensagem'),'Confirma��o', function(r){
		if(r == true){
			location.href = link.href;
		}
	});
	return false;
});
