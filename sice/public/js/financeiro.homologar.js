$(".icoVisualizar").click(function() {
	$.ajax({
		url : this.href,
		success : function(data) {
			
			var find = $(data).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl + '/index.php'; return; }
			
			Dialog.modal($(data).html().replace(/\n/g, ''), 'Detalhes do bolsista');
			
			return false;
		}
	});

	return false;
});

$(".icoReceber").click(function() {
	$.ajax({
		url : this.href,
		success : function(data) {
		
			var find = $(data).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl + '/index.php'; return; }
			
			var titulo = 'Motivo da Devolução';
			
			Dialog.modal($(data).html().replace(/\n/g, ''), titulo);
			
			$("#popup_message form").submit(function(){
				formSubmit(this, titulo, valorTopPopUp(), valorWidthPopUp(), valorLeftPopUp());
				return false;
			});
		
		}
	});

	return false;
});

function formSubmit(obj, titulo, top, width, left){
	
	$.ajax({
        type: "POST",
        url: obj.action,
        data : $("#popup_message form").serializeArray(),
        success: function(html){
        	var find = $(html).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl + '/index.php'; return; }
        	
        	if($(html).find("#mensagens .msgSucesso p").html() == 'FECHAR_POPUP'){
        		$.alerts._hide();
        		window.location.href = baseUrl + '/index.php/financeiro/homologarbolsas/form/NU_SEQ_BOLSA/'+ $(html).find("#NU_SEQ_BOLSA").val();
        		return false;
        	}
        	
        	var texto = $(html).find('#divform').html().replace(/\n/g, '');
        	Dialog.modal('', titulo);
			$("#popup_message").append($(html).find("#mensagens"));
			$("#popup_message").append(texto);

			$("#popup_container").css('top', top);
			$("#popup_container").css('max-width', width);
			$("#popup_container").css('min-width', width);
			$("#popup_container").css('left', left);
			
			$("#popup_message form").submit(function(){
				formSubmit(this, titulo, top, width, left);
				return false;
			});
        }
    });
}

function valorTopPopUp(){
	var texto = $("#popup_container").attr('style');
	var x = (texto.indexOf(' TOP') > -1? texto.indexOf(' TOP') : texto.indexOf(' top')) + 5;
	var y = (texto.indexOf('px', x) - x) + 2;
	var top = texto.substr(x,y);
	return top;
}

function valorWidthPopUp(){
	var texto = $("#popup_container").attr('style');
	var x = (texto.indexOf('WIDTH') > -1? texto.indexOf('WIDTH') : texto.indexOf('width')) + 7;
	var y = (texto.indexOf('px', x) - x) + 2;
	var top = texto.substr(x,y);
	return top;
}

function valorLeftPopUp(){
	var texto = $("#popup_container").attr('style');
	var x = (texto.indexOf(' LEFT') > -1? texto.indexOf(' LEFT') : texto.indexOf(' left')) + 7;
	var y = (texto.indexOf('px', x) - x) + 2;
	var top = texto.substr(x,y);
	return top;
}

window.onload = function(){
	dataTablesObj = $('#tbBolsistas');
	th = $(dataTablesObj).find('thead th:first');
	th.removeAttr('class');
	th.html($('<input />').attr('type','checkbox').bind('click',function(){
	               val = $(this).attr('checked');
	               $(this).parent().unbind();
	               $(dataTablesObj).find('tbody input[type=checkbox]').attr('checked',(val == "checked" || val == true ? "checked" : false));
	}));  
};