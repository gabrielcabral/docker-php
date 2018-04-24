//Pesquisa de informações na receita federal com o CPF

var funcaoCheck = function(){
	$('.checkall').click(function(){
		for(var i = 0; i < $('.check_NU_MATRICULA').size(); i++){
			
			if($('.checkall').attr("checked") == true){
				$('.check_NU_MATRICULA')[i].checked = true;
				if($('.check_NU_MATRICULA')[i].disabled){
					$('.check_NU_MATRICULA')[i].checked = false;
				}
			}else{
				$('.check_NU_MATRICULA')[i].checked = false;
				if($('.check_NU_MATRICULA')[i].disabled){
					$('.check_NU_MATRICULA')[i].checked = false;
				}
			}
		}
	});
};

$(document).ready(function() {
	funcaoCheck();
	$.ajax({
		type : "POST",
		url : baseUrl
				+ '/index.php/secretaria/notificarcursista/get-cursista-avaliou-curso-turma/',

		data : {
			NU_SEQ_TURMA : $("#NU_SEQ_TURMA").val(),
		},
		success : function(data) {
			
			 for(var i = 0; i < data.length; i++){
				 $('input[type=checkbox][value='+data[i]['NU_MATRICULA']+']').attr('disabled', true);				
			 }
		}
	});
});

$("#confirmarNotificacao").click(function() {
	//Variavel de todos os cursistas do grid
	var todos = [];
	//Variavel dos cursistas selecionados
	var selecionados = [];
	
	//Conta os cursistas selecionados
	$('.itemSelect :checked').each(function() {
		selecionados.push($(this).val());
	});
	
	//Verifica se pelo menos um cursista foi selecionado.
	if (selecionados.length > 0) {
		//Conta todos os cursistas
		$('#tbCursista tr').each(function() {
			if ($(this).children('td').length > 0) {
				todos.push($($(this).children('td')[1]).text());
			}
		});
		
		var txt = 'Existem ' + (todos.length - selecionados.length) + ' cursistas que não foram notificados. Deseja continuar?';
		
		Dialog.confirm(txt,'Confirmação', function(r){
			if (r == true) {
				window.location.href = baseUrl + '/index.php/secretaria/notificarcursista/notificar/NU_SEQ_TURMA/' + $("#NU_SEQ_TURMA").val() +
				'/NU_MATRICULA/' + selecionados;
			}
		});
	} else {
		Dialog.alert('Selecione pelo menos um cursista para notificar.');
	}
});