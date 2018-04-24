//Calcula a nota total

var calculaNotaTotal = function() {
    $('.decimal4').blur(function() {
        var notaTutor = $(this).val();
        
        
        var tdParent = $(this).parent().parent();
        var notaCursista = tdParent.next().find('input').val();
        var resultado;
        
        if (notaCursista) {
            if(parseFloat(notaTutor.replace(',','.')) >= 9.0)
                resultado = 10.0;
            else
                resultado = parseFloat(notaTutor.replace(',','.')) + parseFloat(notaCursista.replace(',','.'));
        } else {
                resultado = parseFloat(notaTutor.replace(',','.'));
        }
        
        console.log(resultado);
        
        if(resultado == 'NaN'){
                resultado = "";
        }
        
        tdParent.next().next().find('input').val(number_format(resultado, 1, ",", "."));
        
    });
};


//Executa este método ao entrar na tela para desabilitar a nota dos cursistas desistentes
var verificaDesistente = function() {
	$('#tbCursista tr').each(function() {
		if ($(this).children('td').length > 0) {
			var desistente = $($(this).children('td')[8]).find('input');
			if(desistente.attr('checked')) {
				desistente.parent().parent().parent().find('input[type=text]').attr("disabled", true);
			} else {
				desistente.parent().parent().parent().find('input[type=text]').attr("disabled", false);
			}
		}
	});
};

//Executa esse método ao clicar no ckeckbox de desistente
var desistenteChange = function() {
	if ($(this).attr('checked')) {
		$(this).parent().parent().parent().find('input[type=text]').val('');
		$(this).parent().parent().parent().find('input[type=text]').attr("disabled", true);
	} else {
		$(this).parent().parent().parent().find('input[type=text]').attr("disabled", false);
	}
};

//Executa esse método para verificar se pelo menos uma nota foi lançada pelo tutor
var verificaNotaLancada = function() {
	var flag = false;
	$('#tbCursista tr').each(function() {
		if ($(this).children('td').length > 0) {
			var notaTutor = $($(this).children('td')[4]).find('input');
			if (notaTutor.val() != '' || notaTutor.attr("disabled")) {
				flag = true;
			}
		}
	});
	if (flag) {
		$('#confirmar').attr("disabled", false);
	} else {
		$('#confirmar').attr("disabled", true);
	}
};

//$(calculaNotaTotal);

$(verificaDesistente);
$(verificaNotaLancada);

$('input[type=checkbox]').change(desistenteChange);
$('input').change(verificaNotaLancada);


$(function() {
    
    $('input.decimal4').attr('maxlength', '3');
    $('input.decimal4').addClass('number');
        
    $('body').on('keypress', function(e) {
        if(e.which == 13 || e.which == 3) {
            return false;
        }
    });
    
    $("input.decimal4").keypress(function(event) {
        
        if (event.which == 0 ||event.which == 8 || event.which == 9 || event.which == 27 || event.which == 13 || 
            (event.which == 65 && event.ctrlKey === true) ) {
                 return;
        }
        else {
            if (event.shiftKey || (event.which < 48 || event.which > 57)
            ) {
                event.preventDefault(); 
            }   
        }
    });
    
    
    $("input.decimal4").blur(function() {
        calculaNotaTotal();
        var obj = $(this);
        if(obj.val().indexOf(",") == -1 && obj.val().indexOf(".") == -1) {

            if(parseInt(obj.val()) >= 100) {
                obj.val('');
            }

            if(parseInt(obj.val()) > 10) {
                if(obj.val()%10 == 0) {
                    obj.val((obj.val()/10) + '.0');
                }
                else {
                    obj.val(obj.val()/10);
                }
            }
            else if(parseInt(obj.val()) < 10) {
                obj.val((obj.val() + '.0'));
            }

            if(parseInt(obj.val()) == 10) {
                obj.val('1.0');
            }
        }

        obj.val(obj.val().replace('.', ',')); 
     
        
        var tdParent = obj.parent().parent();
        var notaCursista = tdParent.next().find('input').val();
        var resultado;
        
        if (notaCursista) {
            if(parseFloat(obj.val().replace(',','.')) >= 9.0)
                resultado = 10.0;
            else
                resultado = parseFloat(obj.val().replace(',','.')) + parseFloat(notaCursista.replace(',','.'));
        } else {
                resultado = parseFloat(obj.val().replace(',','.'));
        }
        
        if(resultado == 'NaN'){
                resultado = "";
        }
        
        //tdParent.next().next().find('input').val(number_format(resultado, 1, ",", "."));
        
    });
    
    $('input.decimal4').focus(function() {
        //formatNota($(this));
        $(this).val($(this).val().replace(',', ''));
        $(this).val($(this).val().replace('.', ''));
    });
    
    $('.decimal4').trigger('blur');
})