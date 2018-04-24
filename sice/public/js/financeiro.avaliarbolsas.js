$(".icoVisualizar").click(function() {
	var titulo = "Visualizar turmas";
	$.ajax({
		url : $(this).attr('href'),
		success : function(data) {
			var find = $(data).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl + '/index.php'; return; }
			
			Dialog.modal($(data).html().replace(/\n/g, ''), titulo);
		}
	});

	return false;
});

$(".icoAvaliar").click(function() {
	avaliarTurmas($(this).attr('href'));
	return false;
});

function avaliarTurmas(url){
	var titulo = "Avaliar Turmas";
	
	$.ajax({
		url : url,
		success : function(data) {
			var find = $(data).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

			Dialog.modal($(data).html().replace(/\n/g, ''), titulo);
						
			$("#popup_message form").submit(function(){
				formSubmitAvaliarTurmas(this, titulo, valorTopPopUp(), valorWidthPopUp(), valorLeftPopUp());
				return false;
			});
		}
	});

	return false;
}

function motivoInaptidao(url) {
	$.ajax({
		url : baseUrl + url,
		success : function(data) {
			var find = $(data).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
			
			var titulo = 'Motivo da Inaptidão';
			
			Dialog.modal($(data).html().replace(/\n/g, ''), titulo);
			
			$("#popup_message form").submit(function(){
				formSubmit(this, titulo, valorTopPopUp(), valorWidthPopUp(), valorLeftPopUp());
				return false;
			});
			
			Helper.displayLength($('#DS_OBSERVACAO'), $('#DS_OBSERVACAO').attr('maxlength'));
			$('.textCount').removeAttr('style');
		}
	});

	return false;
}

$("#cancelar").click(function() {
	$.alerts._hide();
});

function motivoNaoAprovacao(url) {
	$.ajax({
		url : baseUrl + url,
		success : function(data) {
			var find = $(data).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
			
			var titulo = 'Motivo da não aprovação';
			
			Dialog.modal($(data).html().replace(/\n/g, ''), titulo);
						
			$("#popup_message form").submit(function(){
			    formSubmitNaoAprovacao(this, titulo, valorTopPopUp(), valorWidthPopUp(), valorLeftPopUp(), $('#NU_SEQ_BOLSA').val());
				return false;
			});
			
			Helper.displayLength($('#DS_OBSERVACAO'), $('#DS_OBSERVACAO').attr('maxlength')); 
			$('.textCount').removeAttr('style');
		}
	});

	return false;
}

function formSubmit(obj, label, top, width, left){
	$.ajax({
        type: "POST",
        url: obj.action,
        data : $("#popup_message form").serializeArray(),
        success: function(html){
        	var find = $(html).find('#loginBase #login').html();
			if(find != null && find != ''){location.href =baseUrl+'/index.php'; return; }
			
        	if($(html).find("#mensagens .msgSucesso p").html() == 'FECHAR_POPUP'){
        		$.alerts._hide();
        		window.location.href = baseUrl + '/index.php/financeiro/avaliarbolsas/form';
        		return false;
        	}
        	
        	var texto = $(html).find('#divform').html().replace(/\n/g, '');
			Dialog.modal('', label);
			$("#popup_message").append($(html).find("#mensagens"));
			$("#popup_message").append(texto);
			
			if($("label[for=htmlOrientacao]")){
				$("label[for=htmlOrientacao]").remove();
			}
			
			$("#popup_container").css('top', top);
			$("#popup_container").css('max-width', width);
			$("#popup_container").css('min-width', width);
			$("#popup_container").css('left', left);
			
			Helper.displayLength($('#DS_OBSERVACAO'), $('#DS_OBSERVACAO').attr('maxlength')); 
			$('.textCount').removeAttr('style');
			
			$("#popup_message form").submit(function(){
				formSubmit(obj, label, top, width, left);
				return false;
				
			});
        }
    });
}

function valorTopPopUp(){
	var texto = $("#popup_container").attr('style');
	var x = (texto.indexOf(' TOP') > -1? texto.indexOf(' TOP') : texto.indexOf(' top')) + 5;
	var y = texto.indexOf(';', x) - x;
	var top = texto.substr(x,y);
	return top;
}

function valorWidthPopUp(){
	var texto = $("#popup_container").attr('style');
	var x = (texto.indexOf('WIDTH') > -1? texto.indexOf('WIDTH') : texto.indexOf('width')) + 7;
	var y = texto.indexOf(';', x) - x;
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

function limparAvaliacao(indice){
    $('#I'+indice).attr('checked', false); 
}

function formSubmitNaoAprovacao(obj, label, top, width, left, bolsa){
    
    $.ajax({
        type: "POST",
        url: obj.action,
        data : $("#popup_message form").serializeArray(),
        success: function(html){
            var find = $(html).find('#loginBase #login').html();
            if(find != null && find != ''){location.href =baseUrl+'/index.php'; return; }
            
            if($(html).find("#mensagens .msgSucesso p").html() == 'FECHAR_POPUP'){
                $.alerts._hide();
                avaliarTurmas(baseUrl + "/index.php/financeiro/avaliarturmas/form/NU_SEQ_BOLSA/" + bolsa);
                return false;
            }
            
            var texto = $(html).find('#divform').html().replace(/\n/g, '');
            Dialog.modal('', label);
            $("#popup_message").append($(html).find("#mensagens"));
            $("#popup_message").append(texto);
           
            $("#popup_container").css('top', top);
            $("#popup_container").css('max-width', width);
            $("#popup_container").css('min-width', width);
            $("#popup_container").css('left', left);
            
            Helper.displayLength($('#DS_OBSERVACAO'), $('#DS_OBSERVACAO').attr('maxlength')); 
            $('.textCount').removeAttr('style');
            
            $("#popup_message form").submit(function(){
                formSubmitNaoAprovacao(obj, label, top, width, left, bolsa);
                return false;
                
            });
        }
    });
}

function formSubmitAvaliarTurmas(obj, label, top, width, left){
	
	$.ajax({
        type: "POST",
        url: obj.action,
        data : $("#popup_message form").serializeArray(),
        success: function(html){
        	var find = $(html).find('#loginBase #login').html();
			if(find != null && find != ''){location.href =baseUrl+'/index.php'; return; }
			
        	if($(html).find("#mensagens .msgSucesso p").html() == 'FECHAR_POPUP'){
        		$.alerts._hide();
        		window.location.href = baseUrl + '/index.php/financeiro/avaliarbolsas/form';
        		return false;
        	}
        	
        	var texto = $(html).find('#divform').html().replace(/\n/g, '');
			Dialog.modal('', label);
			$("#popup_message").append(texto);
			
			$("#popup_container").css('top', top);
			$("#popup_container").css('max-width', width);
			$("#popup_container").css('min-width', width);
			$("#popup_container").css('left', left);
			
			Helper.displayLength($('#DS_OBSERVACAO'), $('#DS_OBSERVACAO').attr('maxlength')); 
			$('.textCount').removeAttr('style');
			
			$("#popup_message form").submit(function(){
				formSubmitAvaliarTurma(obj, label, top, width, left);
				return false;
				
			});
        }
    });
}
