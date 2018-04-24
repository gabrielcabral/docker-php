$("#table_button").click(function() {
	var bolsas = $('input[name=NU_SEQ_BOLSA]:checked').length;
	
	if(bolsas == 0){
	    Dialog.error('Nenhum registro selecionado.', null, null);
	    return false;
	}else{
	    var x = $('input[name=NU_SEQ_BOLSA]:checked');
	    var nu_seq_bolsa = '';
	    var i = 0;
	    for(i = 0; i < bolsas; i++){
	        nu_seq_bolsa += /NU_SEQ_BOLSA/ + x[i].getAttribute('value') ;
	    }
	    
	    
	    if($("#table_main_action option:selected").text() == 'Cancelar bolsa'){
	        var titulo = 'Motivo do Cancelamento da Bolsa';
	        if(i != 1){
	            Dialog.error('Selecione apenas um bolsista por vez.', null, null);
	            return false;
	        }
	        $.ajax({
	            url : $("#table_main_action option:selected").val() + nu_seq_bolsa,
	            success : function(data) {
	                var find = $(data).find('#loginBase #login').html();
	                if(find != null && find != ''){location.href =baseUrl+'/index.php'; return; }
	                
	                Dialog.modal($(data).html().replace(/\n/g, ''), titulo);
	                
	                $("#popup_message form").submit(function(){
	                    formSubmit(this, titulo, valorTopPopUp(), valorWidthPopUp(), valorLeftPopUp());
	                    return false;
	                });
	                
	                Helper.displayLength($('#DS_OBSERVACAO'), $('#DS_OBSERVACAO').attr('maxlength')); 
	                $('.textCount').removeAttr('style');
	                
	                return false;
	            }
	        });
	    }else if($("#table_main_action option:selected").text() == 'Devolver para avaliação'){
	        var titulo = 'Motivo da devolução para avaliação';
	        if(i != 1){
	            Dialog.error('Selecione apenas um bolsista por vez.', null, null);
	            return false;
	        }
	        $.ajax({
	            url : $("#table_main_action option:selected").val() + nu_seq_bolsa,
	            success : function(data) {
	                var find = $(data).find('#loginBase #login').html();
	                if(find != null && find != ''){location.href =baseUrl+'/index.php'; return; }
	                
	                Dialog.modal($(data).html().replace(/\n/g, ''), titulo);
	                
	                $("#popup_message form").submit(function(){
	                    formSubmit(this, titulo, valorTopPopUp(), valorWidthPopUp(), valorLeftPopUp());
	                    return false;
	                });
	                
	                Helper.displayLength($('#DS_OBSERVACAO'), $('#DS_OBSERVACAO').attr('maxlength')); 
	                $('.textCount').removeAttr('style');
	                
	                return false;
	            }
	        });
	    }else if($("#table_main_action option:selected").text() == 'Reenviar para SGB'){
	        //var titulo = "Selecionar o coordenador estadual responsável";
	        if(i == 0){
	            Dialog.error('Selecione apenas um bolsista por vez.', null, null);
	            return false;
	        }
	        
	        window.location.href = $("#table_main_action option:selected").val() + nu_seq_bolsa;
	        
	    }
	    
	    return false;
	}
	
	
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
        		window.location.href = baseUrl + '/index.php/financeiro/verificarpendencias/form';
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
			
			Helper.displayLength($('#DS_OBSERVACAO'), $('#DS_OBSERVACAO').attr('maxlength')); 
			$('.textCount').removeAttr('style');
			
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