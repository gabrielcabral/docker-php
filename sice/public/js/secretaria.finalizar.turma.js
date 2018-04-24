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

        var obj = $(this);
        if(obj.val().indexOf(",") == -1 && obj.val().indexOf(".") == -1) {

            if(parseInt(obj.val()) > 100) {
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
        
        tdParent.next().next().find('input').val(number_format(resultado, 1, ",", "."));
        
    });
    
    $('input.decimal4').focus(function() {
        //formatNota($(this));
        $(this).val($(this).val().replace(',', ''));
        $(this).val($(this).val().replace('.', ''));
    });
    
    $('.decimal4').trigger('blur');
})