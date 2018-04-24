/**
 * Classe que define tudo relacionado a Ajax no Sistema
 * 19/06/2009
 * @autor Jânio Eduardo
 */

function keypresed() {
	return false;
}


var ajax = {
    response : null,
		
    /**
     * Método que executa requisição Get
     * @param String urlDest, String param, String id, int type, int msg
     * @return void
     */
    getAjax : function(urlDest, form, id, msg, boSincono) {
	
		var nu_seq_usuario = verificarLogin();
		
		if( isNaN( nu_seq_usuario ) ){
			document.location.reload();
			return false;
		}
	
		boSincono = boSincono ? false : true;
	
        jQuery.ajax({
            type: "GET",
            url: urlDest,
            async: boSincono,
            data: $('#'+form).serialize(),
            beforeSend: function() {
                popUp.mensagem(msg);
            },
            success: function(resp) {
            	var find = $(resp).find('#loginBase #login').html();
    			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

            	$('#'+id).html(resp);
            	
                //ajax.loadScript(resp);
                popUp.mensagem(msg);
            },
            error: function(resp) {
            }
        });
        return false;
    },

    /**
     * Método que executa requisição Post
     * @param String urlDest, String param, String id, int type, int msg
     * @return void
     */
    postAjax : function(urlDest, form, id, msg, boSincono) {
    	var nu_seq_usuario = verificarLogin();
		
		if( isNaN( nu_seq_usuario ) ){
			document.location.reload();
			return false;
		}
	
		boSincono = boSincono ? false : true;
	
        jQuery.ajax({
            type: "POST",
            url: urlDest,
            async: boSincono,
            data: $('#'+form).serialize(),
            beforeSend: function() {
                popUp.mensagem(msg);
            },
            success: function(resp) {
            	var find = $(resp).find('#loginBase #login').html();
    			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
            	
            	$('#'+id).html(resp);
            	
                //ajax.loadScript(resp);
                popUp.mensagem(msg);
            },
            error: function(resp) {
            }
        });
        return false;
    
    },

    /**
     * Método executa o tipo de retorno
     * @param String txt, String id, int type
     * @return void
     */
    typeReturn : function(txt, id, type) {
        switch (eval(type)) {
            case 1:
                // innerHTML
                jQuery(id).html(txt);
            break;

            case 2:
                // innerHTML concatenando com o valor
                jQuery(id).append(txt);
            break;

            case 3:
                // innerTEXT
                jQuery(id).text(txt);
            break;

            case 4:
                // .value
                jQuery(id).val(txt);
            break;
            
            default:

            break;
        }
    },

    /**
     * Carrega Script's
     * @param string html
     * @return void
     */
    loadScript : function(html) {
        var ini    = 0;
        var code   = '';
        var end    = '';
        var script = '';

        while (ini!=-1) {
            ini = html.indexOf('<script', ini);
            if (ini >= 0) {
                ini         = html.indexOf('>', ini) + 1;
                end         = html.indexOf('</script>', ini);
                code        = html.substring(ini, end);
                script      = document.createElement("script")
                script.text = code;
                window.document.body.appendChild(script);
            }
        }
    },

    /**
     * Método para mensagem de Aguarde
     * @param String id, int msg
     * @return void
     */
    pleaseAjax : function(id, msg) {
        switch (eval(msg)) {
            case 1:
                // innerHTML
                jQuery(id).html('<option value=""> .:Selecione:. </option>');
            break;
        }
    },

    /**
     * Método para envio de dados dos formulários
     * @param String idForm, String urlDest, String id, int type, int msg
     * @return void
     */
    formAjax : function(idForm, idDest, type, msg) {
        var url     = '&' + jQuery(idForm).serialize();
        var urlDest = jQuery(idForm).attr('action');
        ajax.postAjax(urlDest, url, idDest, type, msg);
        return false;
    },

    /**
     * Método para envio de dados dos formulários
     * @param String idForm, String urlDest, String id, int type, int msg
     * @return void
     */
    formPostAjax : function(idForm, urlDest, idDest, type, msg) {
        var url     = '&' + jQuery(idForm).serialize();
        ajax.postAjax(urlDest, url, idDest, type, msg);
        return false;
    },
        
    /**
    * Método para envio de dados dos formulários
    * @param String idForm, String urlDest, String id, int type, int msg
    * @return void
    */
    deleteAjax : function(urlDest, param, id, type, msg, title, text) {
    	if (!text) {
            text = "Confirma Exclusão do item selecionado?";
    	}

    	if (!title) {
            title = "Excluir";
    	}

        $("#dialog").html('<p style="font-size:13px;font-weight:bold;"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>' + text + '</p>').attr('title', title);
        $("#dialog").dialog({
            bgiframe : true,
            resizable : false,
            height : 140,
            width : 350,
            modal : true,
            overlay : {
                backgroundColor: '#fff',
                opacity: 0.5
            },
            buttons : {
                'Confirma' : function() {
                    $(this).dialog('close');
                    ajax.postAjax(urlDest, param, id, type, msg);
                },
                'Cancela': function() {
                    $(this).dialog('close');
                }
            },
            close : function() {
                $("#dialog").html('').removeAttr('title');
                $("#dialog").dialog('destroy');
            }
        });
    },
    
    /**
     * Método para envio dos formulários no evento 'Enter' 
     * Exemplo: ajax.enviaFormulario('#formCadast', '#content', event);
     * @param String form, object objEvent,
     * @return void
     */
    submitForm : function(form, campo, objEvent) {
    	if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
    		var code = eval(objEvent.keyCode); 
    	} else {  
    		var code = eval(objEvent.which);   
     	}	 
    	
    	if (code == 13) {
    		ajax.formAjax(form, campo, 1, 1);
    	}
    },

    /**
     * Método para popular campo richtext
     * @param String Field
     * @return void
     */    
    richText : function(instance) {
    	var e = FCKeditorAPI.GetInstance(instance); 
    		e.UpdateLinkedField();
    },

    /**
     * Método para ajustes para a paginação
     * @param String form
     */
    serializeForm : function (form){
        var formText   = jQuery(form + ' input:text');
        var formRadio  = jQuery(form + ' input:radio');
        var formCheck  = jQuery(form + ' input:checkbox');
        var formSelect = jQuery(form + ' select');

        if (formText) {
            for (var i = 0; i < formText.length; i++) {
                if(formText.eq(i).val()){
                    return jQuery(form).serialize();
                }
            }
        }

        if (formRadio) {
            for (var j = 0; j < formRadio.length; j++) {
                if(formRadio.eq(j).val()){
                    return jQuery(form).serialize();
                }
            }
        }

        if (formCheck) {
            for (var k = 0; k < formCheck.length; k++) {
                if(formCheck.eq(k).val()){
                    return jQuery(form).serialize();
                }
            }
        }

        if (formSelect) {
            for (var l = 0; l < formSelect.length; l++) {
                if(formSelect.eq(l).val() != ''){
                    return jQuery(form).serialize();
                }
            }
        }

        return null;
    },

    validate : function(urlDest, param, msg) {
        jQuery.ajax({
            type: "JSON" ,
            url : urlDest + param,
            beforeSend: function() {
                popUp.mensagem(msg);
            },
            success: function(resp) {
            	var find = $(resp).find('#loginBase #login').html();
    			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
            	
                popUp.mensagem(msg); 
                return resp;
            },
            error: function(resp) {
            }
        });
        return false;
    }
};

/**
 * Classe efeitos para as mensagens ou para as repostas do Ajax
 * 30/06/2009
 * @autor Jânio Eduardo
 */
var popUp = {
    /**
     * Método que faz o efeito de loading...
     * @return object
     */
    carregando : function() {
    	try {
            var preloader = jQuery('#preLoader');
            if (preloader.css('display') == '' || preloader.css('display') == 'block') {
            	//document.onkeydown=true;
                //document.oncontextmenu = new Function("return false;");
                preloader.css('display', 'none');
                
            } else {
              preloader.css('height', $(document).height()).css('display', 'block');
             // document.onkeydown=keypresed;
             // document.oncontextmenu = new Function("return false;");
            }
            
            return ;
        } catch (e) {
            // TODO: handle exception
        }
    },
	
    /**
     * Método que monta o Carregando... no topo da tela
     * @return void
     */
	ajaxLoading : function () {
		var ajaxLoading = jQuery('#ajax_loading');
		if (ajaxLoading.css('display') == '' || ajaxLoading.css('display') == 'block') {
			return ajaxLoading.css('display', 'none');
        }
		
		return ajaxLoading.css({
            display : ''
        });
	},
	
    /**
    * Método que verifica qual o efeito da execução do Ajax
    * @return void
    */
	mensagem : function (msg) {
		switch (eval(msg)) {
			case 2:
				popUp.ajaxLoading();
			break;
	
			default:
				popUp.carregando();
			break;
		}
	}
};

/**
 * Classe que cria url's amigáveis dos formulários 
 * 16/07/2009
 * @autor Jânio Eduardo
 */
var url = {
	formFriends : function() {
		try {
			var urlForm = ''; 
			var inputs = jQuery('#formSearch .simple input');
			for (var i = 0; i < inputs.length; i++) {
				urlForm = urlForm + '/' + inputs[i].name + '/' + inputs[i].value;
			}
			return urlForm;
		} catch (e) {
			// TODO: handle exception
		}
	}
};

/**
 * Classe para Efeitos
 * 31/07/2009
 * @autor Jânio Eduardo
 */
var effects = {
	/**
	 * Método para mostrar ou esconder campos
	 * @param String alvo
	 * @return object
	 */
	showHide : function(alvo) {
		var objAlvo = jQuery(alvo);
		if (objAlvo.css('display') == '' || objAlvo.css('display') == 'block') {
			return objAlvo.css('display', 'none');
        }
		return objAlvo.css('display', '');
	},
	
	/**
	 * Método para slideDown e slideUp com o slideToggle
	 * @param String alvo, String velocidade
	 * @return void
	 */
	slide : function(alvo, velocidade){
		var obj = jQuery(alvo);
		jQuery(alvo).slideToggle(velocidade);
	},
	
	search : function(alvo, effect, velocidade) {
        var options = {};
		$(alvo).toggle(effect, options, velocidade);
	}
	
};

/**
 * Classe para Select Múltiplos
 * 06/08/2009
 * @autor Jânio Eduardo
 */
var multipleSelect = {
	/**
	 * Método para adicionar na lista
	 * @param String objStart que seta quem está mandando os registros
	 * @param String objStart que seta quem está recebe os registros
	 * @param int typeSelect que faz que selecione todos do que está mandando
	 * @param int orderDestiny que para setar ou não a ordem do destino
	 * return void
	 */
	add : function(objStart, objFinal, typeSelect, orderDestiny) {
		var objSelect  = jQuery(objStart + ' option:selected');
		var objRemove  = jQuery(objStart);
		var objDestiny = jQuery(objFinal);

		for (var i = 0; i < objSelect.length; i++) {
			objDestiny.addOption(objSelect[i].value, objSelect[i].text);
			objRemove.removeOption(objSelect[i].value);
		}
		
		objRemove.sortOptions();
		
		if (eval(orderDestiny) == 1) {
			objDestiny.sortOptions(); 
		}
		
		if (eval(typeSelect) == 1){
			var objOldSel  = jQuery(objStart + ' option');
			
			for (var i = 0; i < objOldSel.length; i++) {
				objOldSel[i].selected = true;
			}
		} else {
			var objNewSel  = jQuery(objFinal + ' option');
			
			for (var i = 0; i < objNewSel.length; i++) {
				objNewSel[i].selected = true;
			}	
		}
	}
};

/**
 * Classe para Link's diversos
 * 10/08/2009
 * @autor Jânio Eduardo
 */
var link = {
	page : function(url) {
		window.location.href = url; 
	}
};

/**
 * Classe para efeitos de tabs no sistema
 * 07/12/2009
 * @autor Jânio Eduardo
 */
var effectsTabs = {
    show : function(tab, divShow, divHide) {
        var ul      = jQuery('#menuTabs li');
        var li      = jQuery(tab);
        var showDiv = jQuery(divShow);
        var hideDiv = jQuery(divHide);

        for (var i = 0; i < ul.length; i++) {
            ul.eq(i).addClass('ui-state-default').removeClass('ui-tabs-selected').removeClass('ui-state-active');
        }

        li.removeClass('ui-state-default').addClass('ui-tabs-selected').addClass('ui-state-active');
        hideDiv.addClass('ui-tabs-hide');
        showDiv.removeClass('ui-tabs-hide');

        return false;
    }
};

function verificarLogin(){

	var nu_seq_usuario;
	
	$.ajax({
        url: baseUrl + '/index.php/default/index/is-logged',
        type: "POST",
        async: false,
        success: function(result) {
        	var find = $(result).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
        	
			nu_seq_usuario = result;
		}
    });	
	
	return nu_seq_usuario;
}
function number_format(number, decimals, dec_point, thousands_sep) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival
    // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
    // +   improved by: davook
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Jay Klehr
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Amir Habibi (http://www.residence-mixte.com/)
    // +     bugfix by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'
    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');
    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'
    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
    var n = !isFinite(+number) ? 0 : +number, 
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}


// MODAL DE CONFIRMAÇÃO, BASTA ADICIONAR O ATRIBUTO MENSAGEN NOS COMPONENTES.
$("input").click(function() {
	if($(this).attr('mensagem') != '' && $(this).attr('mensagem') != null){
		var btn = this;
		Dialog.confirm($(this).attr('mensagem'), 'Confirmação', function(r) {
			if (r == true) {
				btn.form.submit();
			}
		});
		return false;
	}
});
