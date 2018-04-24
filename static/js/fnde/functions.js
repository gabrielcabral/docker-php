/**
 * Arquivo responsavel pela chamada de todas as funcionalidades dos Sistemas do FNDE
 * Copyright(c) Todos os direitos reservados ao FNDE
 */
/*
if (window.console === null)
    window.console = {
        log: function(p) {
        },
        error: function(p) {
        },
        info: function(p) {
        }
    };
*/
var oFnde = "";

var Fnde = function() {
    oFnde = this;

    autoInit = this.getURLParameter('auto', jQuery('script[src*="fnde.script.min.js"],script[src*="functions.js"]').attr('src')) !== 'null' ? false : true;

    if (autoInit) {
        this.init();
    }
};

Fnde.fn = Fnde.prototype;
Fnde.fn.extend = jQuery.extend;

(function($) {
    Fnde.fn.extend(
            {
                init: function() {

                    this.urlProject = typeof urlProject != 'undefined' ? urlProject : location.protocol + "//" + location.host;
                    this.$header = $('#header');
                    this.$menu = $('body > .menu');
                    this.$content = $('#conteudo');

                    oFnde.tab.create($('div.tab, div.tabVertical'));
                    oFnde.$menu.length && oFnde.menu(oFnde.$menu);

                    oFnde.mask.init();

                    //contador de caracteres
                    $('textarea[maxlength]').not('.editorHtml').each(function() {
                        oFnde.displayLength($(this), $(this).attr('maxlength'));
                    });

                    //Editor Html
                    oFnde.editorHtml();

                    //pickList 
                    $('.pickList').pickList();

                    //Accordion
                    oFnde.accordion.init();

                    //Checkbox Tree
                    $('ul.checkboxTree').checkboxTree({
                        initializeChecked: 'expanded',
                        initializeUnchecked: 'collapsed'
                    });
                },
                getURLParameter: function(name, url) {

                    url = typeof url != 'undefined' ? url.slice(url.indexOf('?')) : location.search;

                    return decodeURI(
                            (RegExp(name + '=' + '(.+?)(&|$)').exec(url) || [, null])[1]
                            );
                },
                editorHtml: function() {
                    if ($('textarea').hasClass('editorHtml')) {
                        var script = document.createElement('script');
                        script.type = 'text/javascript';
                        script.src = oFnde.urlProject + '/static/js/jquery/ckeditor/ckeditor.js';
                        document.getElementsByTagName('head')[0].appendChild(script);
                    }
                    ;
                },
                date: {
                    init: function(input, params) {
                        var defaults = $.extend({
                            changeMonth: true,
                            numberOfMonths: 1,
                            minDate: "01/01/1970",
                            showOn: "both",
                            buttonText: "Selecionar data"
                        }, params),
                                inputDisabled = input.filter("input[disabled=disabled]");

                        inputDisabled.addClass("in-disabled");

                        input.datepicker("destroy");

                        $.datepicker.regional['pt-BR'] = {
                            closeText: 'Fechar',
                            prevText: '&#x3c;Anterior',
                            nextText: 'Pr&oacute;ximo&#x3e;',
                            currentText: 'Hoje',
                            monthNames: ['Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho',
                                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                            monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
                                'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                            dayNames: ['Domingo', 'Segunda-feira', 'Ter&ccedil;a-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado'],
                            dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
                            dayNamesMin: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
                            weekHeader: 'Sm',
                            dateFormat: 'dd/mm/yy',
                            firstDay: 0,
                            isRTL: false,
                            showMonthAfterYear: false,
                            yearSuffix: ''
                        };
                        $.datepicker.setDefaults($.datepicker.regional['pt-BR']);

                        input.each(function() {
                            $(this).attr("disabled") == undefined && $(this).datepicker(defaults);
                        });

                        $("input.date[disabled='disabled']:not('.verify')").each(function() {
                            oFnde.date.buildDisabled($(this));
                        })
                    },
                    disable: function(ele) {
                        var ele = typeof ele == 'object' ? ele : $(ele),
                                bt = ele.parent().find("button.ui-datepicker-trigger").clone();

                        ele.datepicker("destroy");
                        ele.attr("disabled", true);
                        bt.addClass("disabled").insertAfter(ele);
                    },
                    enable: function(ele, params) {
                        ele.removeAttr("disabled");
                        ele.next("button").remove();
                        oFnde.date.range(ele, params);
                    },
                    range: function(startElement, endElement, startDate, endDate) {

                        var startElement = typeof startElement == 'object' ? startElement : $(startElement),
                                endElement = typeof endElement == 'object' ? endElement : $(endElement);


                        var dayNow = new Date();
                        oFnde.date.init(startElement, {
                            minDate: startDate != undefined ? startDate : "",
                            onSelect: function(date, obj) {
                                endElement.datepicker("option", "minDate", date);
                            }
                        });

                        oFnde.date.init(endElement, {
                            maxDate: endDate != undefined ? endDate : '+2y',
                            onSelect: function(date, obj) {
                                //endElement.datepicker("option", "minDate", date);
                            }
                        });
                    },
                    buildDisabled: function(ele) {
                        $('<button type="button" class="ui-datepicker-trigger disabled">Selecionar data</button>').insertAfter(ele);
                        ele.addClass("verify");
                    }
                },
                mask: {
                    init: function() {

                        $('input.fone').length && oFnde.mask.phone($('input.fone'));
                        $('input.fone9').length && oFnde.mask.phone($('input.fone9'));

                        $('input.cpf').setMask('cpf');
                        $('input.cnpj').setMask('cnpj');
                        $('input.cep').setMask('cep');

                        oFnde.date.init($("input.date"));

                        $('input.cep').setMask('cep');
                        $('input.inteiro').setMask({
                            mask: '9',
                            type: 'repeat'
                        });

                        $('input.decimal').setMask('decimal');
                        $('input.signed-decimal').setMask('signed-decimal');
                        $('input.decimal6').setMask({
                            mask: '99,999',
                            type: 'reverse',
                            defaultValue: '000'
                        });
                        $('input.decimal10').setMask({
                            mask: '99,999.999',
                            type: 'reverse',
                            defaultValue: '000'
                        });


                        $('input.decimal4d').setMask({
                            mask: '9999,999.999.999',
                            type: 'reverse',
                            defaultValue: '00000'
                        }).blur(function() {
                            var $obj = $(this)

                            for (i = 0; i < 2; i++) {
                                if ($obj.val()[$obj.val().length - 1] == 0) {
                                    $obj.val($obj.val().substring(0, $obj.val().length - 1))
                                }
                            }
                        });

                        //Valor decimal negativo: altera a class
                        $('.signed-decimal').keyup(function() {
                            ($('.signed-decimal').val().indexOf('-') != -1) ? $('.signed-decimal').addClass('formatValorNegativo') : $('.signed-decimal').removeClass('formatValorNegativo');
                        });
                        if ($('.signed-decimal').val()) {
                            ($('.signed-decimal').val().indexOf('-') != -1) ? $('.signed-decimal').addClass('formatValorNegativo') : $('.signed-decimal').removeClass('formatValorNegativo');
                        }
                        ;

                        $('input.time').setMask({
                            mask: '29:59:59',
                            autoTab: false,
                            setSize: true
                        });

                        //Todo: verificar se deve ficar aqui
                        $('.date').bind('change', function(e) {
                            if (e.keyCode == 13) {
                                return false;
                            }
                            ;
                            if ($(this).val() != '') {
                                if (!Helper.Date.validate($(this).val())) {
                                    $this = $(this);
                                    $(this).val('');
                                    Dialog.alert('Data inválida', 'Atenção', function() {
                                        $this.focus();
                                    });
                                }
                                ;
                            }
                            ;
                            return false;
                        }).bind('click', function() {
                            $(this).trigger('change');
                        }).bind('blur', function() {
                            $(this).trigger('change');
                        }).bind('keydown', function(e) {
                            return !(e.keyCode == 13);
                        });
                    },
                    phone: function($element) {
                        var validation = function() {
                            phone = $element.val().replace(/\D/g, '');
                            $element.unsetMask();
                            if (phone.length > 10) {
                                $element.setMask({
                                    mask: '(99) 99999-9999',
                                    onValid: validation
                                });
                            } else {
                                $element.setMask({
                                    mask: ($element.hasClass('fone9')) ? $element.val().replace(/\D/g, '').length > 10 ? '(99) 99999-9999' : '(99) 9999-99999' : '(99) 9999-9999',
                                    onValid: validation
                                });
                            }
                        }
                        $element.setMask({
                            mask: ($element.hasClass('fone9')) ? $element.val().replace(/\D/g, '').length > 10 ? '(99) 99999-9999' : '(99) 9999-99999' : '(99) 9999-9999',
                            autoTab: false,
                            onValid: validation
                        }).blur(validation)
                    }
                },
                dialog: {
                    init: function(customs) {

                        var defaults = $.extend({
                            modal: true,
                            width: 550,
                            minWidth: 550,
                            closeOnEscape: true
                        }, customs);

                        return defaults
                    },
                    alert: function(message, title, callback, options) {
                        var settings = $.extend(
                                oFnde.dialog.init({
                            title: (title == null) ? 'Alerta' : title,
                            dialogClass: "alert",
                            closeText: "",
                            buttons: {
                                'OK': function() {
                                    $(this).dialog('close');
                                    callback != undefined && callback();
                                }
                            },
                            resizable: false,
                            open: function() {
                                $(this).parent().find('button:contains("OK")').removeClass().addClass('btnConfirmar');
                                $(this).parent().find('.ui-dialog-titlebar-close').hide();
                            }
                        }),
                        options);

                        if (typeof message != 'object')
                            message = $('<div>' + message + '</div>');

                        message.dialog(settings);

                    },
                    confirm: function(message, title, callback, options) {

                        oFnde.dialog.alert(message, title, callback, $.extend(
                                {
                                    title: (title == null) ? 'Confirmação' : title,
                                    dialogClass: "confirm",
                                    buttons: {
                                        Cancelar: function() {
                                            $(this).dialog('close');
                                        },
                                        Confirmar: function() {
                                            callback != undefined && callback(true);
                                            $(this).dialog('close');
                                        }
                                    },
                                    open: function() {
                                        $(this).parent().find('button:contains("Confirmar")').removeClass().addClass('btnConfirmar');
                                        $(this).parent().find('button:contains("Cancelar")').removeClass().addClass('btnCancelar');
                                        $(this).parent().find('.ui-dialog-titlebar-close').hide();
                                    }
                                }, options)
                                );
                    },
                    error: function(message, title, callback, options) {
                        oFnde.dialog.alert(message, title, callback, $.extend(
                                {
                                    title: (title == null) ? 'Erro' : title,
                                    dialogClass: "error"
                                }, options)
                                );
                    },
                    modal: function(content, title, callback, options) {
                        var settings = $.extend(
                                oFnde.dialog.init({
                            title: (title == null) ? 'Modal' : title,
                            dialogClass: "modal",
                            open: function() {
                                callback != undefined && callback();
                            }
                        }),
                        options);

                        if (typeof content != 'object')
                            content = $('<div>' + content + '</div>');

                        content.dialog(settings);
                    },
                    close: function($obj) {
                        $obj.dialog("isOpen") && $obj.dialog("destroy");
                    }
                },
                tab: {
                    create: function(selector) {
                        var elementActive = null;
                        $(selector).children("div").each(function(i, element) {
                            $(element).hide();
                        });
                        $(selector).children("ul").each(function(i, element) {
                            $(element).children("li").each(function(i, element) {
                                $(element).children("a").each(function(i, element) {
                                    oFnde.tab.event.click(element);
                                    if ($(element).parent().hasClass("active")) {
                                        $(element).parent().removeClass('active');
                                        $(element).click();
                                    }
                                }); // a
                            }); //li
                        }); //ul
                    },
                    event: {
                        click: function(element) {
                            $(element).click(function() {
                                if ($(element).parent().hasClass('disabled')) {
                                    return false;
                                }
                                var chk = $(element).attr("href");
                                if (chk.substr(0, 1) == '#' && !$(element).parent().hasClass('active')) {
                                    $(element).parent().parent().parent().children("div").hide();
                                    $(element).parent().parent().children("li.active").removeClass('active');
                                    $(element).parent().addClass('active');
                                    $(chk).show();

                                    return false;
                                }
                            });
                        }
                    }
                },
                menu: function(selector) {

                    if ($(selector)[0].tagName == "DIV") {
                        $(selector).children($(selector)[0].tagName).each(function(i, element) {
                            $(element).hide();
                            oFnde.menu($(element));
                        });
                        $(selector).children("ul").each(function(i, element) {
                            $(element).children("li").each(function(i, element) {
                                if ($(element).hasClass('active')) {
                                    $(element).parent().parent().show();
                                }
                                $(element).children("a").each(function(i, element) {
                                    oFnde.tab.event.click(element);
                                }); // a
                            }); //li
                        }); //ul
                    } else {
                        $(" > li > a, > li > ul > li > a ", selector).click(function() {
                            element = $(this);

                            if (element.attr("href").substr(0, 1) == '#') {
                                
                                $('li', element.closest('ul')).removeClass('active');
                                marginStart = (element.closest('ul').hasClass('menu')) ? 0 : element.closest('ul').height(); 
                                element.parent('li').addClass('active');
                                
                                oFnde.$content.css('margin-top', (marginStart + element.next().height()));
                                
                                return false;
                            }
                        });
                    }

                },
                displayLength: function(objElement, max) {

                    if (max == undefined) {
                        max = -1;
                    }

                    var displayFormat = '<span title="Quantidade de caracteres digitados">#input</span>|<span title="Quantidade máxima de caracteres">#max</span>'

                    var options = {
                        'displayFormat': displayFormat,
                        'maxCharacterSize': max,
                        'originalStyle': 'textCount',
                        'warningStyle': 'textCountError',
                        'warningNumber': 40,
                        'autoStart': true
                    };

                    $(objElement).textareaCount(options);
                },
                validate: {
                    date: function(dateStr) {
                        var dateArr = new String(dateStr).split('/');
                        sDay = dateArr[0];
                        sMonth = dateArr[1];
                        sYear = dateArr[2];

                        dateStr = sMonth + '-' + sDay + '-' + sYear;

                        var datePat = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
                        var matchArray = dateStr.match(datePat); // is the format ok?

                        if (matchArray == null) {
                            return false;
                        }

                        month = matchArray[1]; // p@rse date into variables
                        day = matchArray[3];
                        year = matchArray[5];

                        if (month < 1 || month > 12) { // check month range
                            return false;
                        }

                        if (day < 1 || day > 31) {
                            return false;
                        }

                        if ((month == 4 || month == 6 || month == 9 || month == 11) && day == 31) {
                            return false;
                        }

                        if (month == 2) { // check for february 29th
                            var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
                            if (day > 29 || (day == 29 && !isleap)) {
                                return false;
                            }
                        }
                        return true; // date is valid
                    }
                },
                timerSection: function(timeSection, callback) {

                    callback = (callback != undefined) ? {
                        onExpiry: callback
                    } : {};

                    $('#infoUsuarioSessao').countdown($.extend({
                        until: timeSection,
                        layout: 'Sua sess&atilde;o expira em: {mn} min {sn}',
                        expiryText: '<span>Sua sess&atilde;o expirou!</span>'
                    }, callback));
                },
                dataTable: function(tableId, head, footer, settings) {

                    var titulo = $('title').text();
                    var subtitulo = $(this).find('caption').text();

                    var colSorting = new Array();

                    if($('th.itemSelect').length){
                        colSorting.push({"sType": "dom-checkbox", "bSearchable": false , "aTargets": [$('th.itemSelect').index()]});
                    };

                    if($('th.icons').length){
                        colSorting.push({"bSortable": false, "bSearchable": false , "aTargets": [$('th.icons').index()]});
                    };

                    var mColumns = new Array();
                    $(tableId).find('thead th').each(function() {
                        if (!$(this).hasClass('itemSelect')) {
                            if (!$(this).hasClass('icons')) {
                                mColumns.push($(this).index());
                            }
                        }
                    });

                    sDom = (head) ? '<"listagemAcoesBarra"lfrT>' : '';
                    sDom += (footer) ? 't<"listagemNavegacao"<"listagemPaginacao"ip>>' : '';


                    // set some sensible defaults
                    settings = jQuery.extend({
                        "sDom": sDom,
                        "oLanguage": {
                            "sProcessing": "Processando...",
                            "sLengthMenu": "_MENU_",
                            "sZeroRecords": "N&atilde;o foram encontrados resultados!",
                            "sInfo": "Exibindo de _START_ at&eacute; _END_ de _TOTAL_",
                            "sInfoEmpty": "",
                            "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
                            "sInfoPostFix": "",
                            "sSearch": "",
                            "sUrl": "",
                            "oPaginate": {
                                "sFirst": "&laquo;",
                                "sPrevious": "&lsaquo;",
                                "sNext": "&rsaquo;",
                                "sLast": "&raquo;"
                            }
                        },
                        "sPaginationType": "full_numbers",
                        "aaSorting": [],
                        "aoColumnDefs": colSorting,
                        "oTableTools": {
                            "sSwfPath": oFnde.urlProject + "/static/media/swf/copy_cvs_xls_pdf.swf",
                            "aButtons": [
                                {
                                    "sExtends": "xls",
                                    "sButtonText": "Salvar como Planilha",
                                    "sToolTip": "Salvar como Planilha",
                                    "mColumns": mColumns
                                },
                                {
                                    "sExtends": "pdf",
                                    "sButtonText": "Salvar como PDF",
                                    "sToolTip": "Salvar como PDF",
                                    "sTitle": titulo,
                                    "sPdfMessage": subtitulo,
                                    "mColumns": mColumns
                                }
                            ]
                        }
                    }, settings);

                    $(tableId).dataTable(settings);
                    //$(tableId).dataTable(settings).rowReordering();

                    //Listagem - botão mais ações - controle
                    ($('.listagemAcoes select').val() == "") ? $('.listagemAcoes input').attr('disabled', 'disabled') : $('.listagemAcoes input').removeAttr('disabled');

                    $('.listagemAcoes select').live('change', function() {
                        $(this).val() == "" ? $('.listagemAcoes input').attr('disabled', 'disabled') : $('.listagemAcoes input').removeAttr('disabled');
                    });
                },
                Upload: function(id, urlServ, callback, type)
                {
                    //   var typeUpload = while(while)


                    $('#' + id).uploadify({
                        uploader: oFnde.urlProject + '/static/js/jquery/uploadify/uploadify.swf',
                        script: urlServ,
                        cancelImg: oFnde.urlProject + '/static/js/jquery/uploadify/cancel.png',
                        buttonImg: 'http://www.uploadify.com/uploadify/button.jpg',
                        auto: auto.condition,
                        multi: multi,
                        removeCompleted: false,
                        fileExt: fileExt != undefined ? fileExt : "",
                        fileDesc: fileDesc != undefined ? fileDesc : "",
                        onComplete: callback
                    });

                    $('#' + auto.element).click(function()
                    {
                        $('#' + id).uploadifyUpload();

                        return false;
                    });
                },
                accordion: {
                    init: function() {
                        $('dl.accordion').length &&
                                $('dl.accordion').each(function() {

                            $('dt').each(function() {
                                $ele = $(this);

                                oFnde.accordion.creatIcon($ele);
                                $ele.hasClass('active') && $ele.next().show();

                                $ele.click(function() {
                                    if (!$(this).hasClass('active')) {
                                        $('> dt.active', $(this).parent()).next().slideToggle();
                                        $('> dt.active', $(this).parent()).removeClass('active');

                                        $(this).addClass('active').next().slideToggle();
                                    }
                                    return false;

                                });
                            });


                        });
                    },
                    creatIcon: function($ele) {
                        $ele.append($('<span>').addClass('icon'));
                    }
                },
                // Mostra mensagem de instrucao para o desenvolvedor
                deprecated: function(oldFunction, newFunction) {
                    if (location.host.indexOf('www.') !== 0) {
                        var msg = '******************************************************************************************************************************';
                        msg += '\n** ATENÇÃO';
                        msg += '\n******************************************************************************************************************************';
                        msg += '\n** A função "' + oldFunction + '" foi depreciada!';
                        msg += '\n** Utilizar "' + newFunction + '"';
                        msg += '\n** Para maiores informações consultar: http://intranet.fnde.gov.br/tivirtual/index.php/areasdisciplinas/design-de-interfaces';
                        msg += '\n******************************************************************************************************************************';

                        alert(msg);
                    }
                }
            });

})(jQuery);

jQuery(function()
{
    var gfnde = new Fnde();
});


jQuery.fn.resetDefaultValue = function() {
    function _clearDefaultValue() {
        var _jQuery = jQuery(this);
        if (_jQuery.val() == this.defaultValue) {
            _jQuery.val('');
        }
    }
    ;
    function _resetDefaultValue() {
        var _jQuery = jQuery(this);
        if (_jQuery.val() == '') {
            _jQuery.val(this.defaultValue);
        }
    }
    ;
    return this.click(_clearDefaultValue).focus(_clearDefaultValue).blur(_resetDefaultValue);
};


/**
 * Componente para criação de dialogos
 **/

var Dialog = {
    fixBugIE: function() {
        jQuery('#popup_container,#popup_overlay').bgiframe();
    },
    configs: function() {
        jQuery.alerts.overlayColor = '#000000';
        jQuery.alerts.overlayOpacity = 0.4;
        jQuery.alerts.okButton = '&nbsp;Ok&nbsp';
        jQuery.alerts.cancelButton = '&nbsp;Cancelar&nbsp';
        Dialog.fixBugIE();
    },
    alert: function(message, title, callback) {
        Dialog.configs();
        if (title == null)
            title = 'Alerta';
        jQuery.alerts.dialogClass = 'alert';
        jQuery.alerts._show(title, message, null, 'alert', function(result) {
            if (callback)
                callback(result);
        });
        Dialog.fixBugIE();
        oFnde.deprecated('Dialog.alert(content, title, callback, options)', 'oFnde.dialog.alert(content, title, callback, options)');
    },
    confirm: function(message, title, callback) {
        Dialog.configs();
        if (title == null)
            title = 'Confirmação';
        jQuery.alerts.dialogClass = 'confirm';
        jQuery.alerts.okButton = '&nbsp;Confirmar&nbsp';
        jQuery.alerts._show(title, message, null, 'confirm', function(result) {
            if (callback)
                callback(result);
        });
        Dialog.fixBugIE();
        oFnde.deprecated('Dialog.confirm(content, title, callback, options)', 'oFnde.dialog.confirm(content, title, callback, options)');
    },
    error: function(message, title, callback) {
        Dialog.configs();
        if (title == null)
            title = 'Erro';
        jQuery.alerts.dialogClass = 'error';
        jQuery.alerts._show(title, message, null, 'alert', function(result) {
            if (callback)
                callback(result);
        });
        jQuery("#popup_content").removeClass('alert').addClass('error');
        Dialog.fixBugIE();
        oFnde.deprecated('Dialog.error(content, title, callback, options)', 'oFnde.dialog.error(content, title, callback, options)');
    },
    modal: function(content, title, callback) {
        Dialog.configs();
        if (title == null)
            title = 'Modal';
        jQuery.alerts.dialogClass = 'modal';
        jQuery.alerts._show(title, content, null, 'modal', function(result) {
            if (callback)
                callback(result);
        });
        jQuery('#popup_title').html(jQuery('#popup_title').text() + '<button type="button" id="popup_button_close" >X</button>');
        jQuery('#popup_button_close').click(function() {
            $.alerts._hide();
        });
        Dialog.fixBugIE();
        oFnde.deprecated('Dialog.modal(content, title, callback, options)', 'oFnde.dialog.modal(content, title, callback, options)');
    }
};



var Helper = {
    Datepicker: {
        enable: function(ele) {
            oFnde.date.enable(ele);
            oFnde.deprecated('Helper.Datepicker.enable(ele)', 'oFnde.date.enable(ele)');
        },
        disable: function(ele) {
            oFnde.date.enable(ele);
            oFnde.deprecated('Helper.Datepicker.disable(ele)', 'oFnde.date.disable(ele)');
        },
        range: function(startElement, endElement, startDate, endDate) {
            oFnde.date.range(startElement, endElement, startDate, endDate);
            oFnde.deprecated('Helper.Datepicker.range(startElement, endElement, startDate, endDate)', 'oFnde.date.range(startElement, endElement, startDate, endDate)');
        }
    },
    Tab: {
        create: function(selector) {
            oFnde.tab.create(selector);
            oFnde.deprecated('Helper.Tab.create(selector)', 'oFnde.tab.create(selector)');
        }
    },
    Menu: {
        Tabbed: function(selector) {
            oFnde.menu(selector);
            oFnde.deprecated('Helper.Menu.Tabbed(selector)', 'oFnde.menu(selector)');
        }
    },
    displayLength: function(objElement, max) {
        oFnde.displayLength(objElement, max);
        oFnde.deprecated('Helper.displayLength(objElement,max)', 'oFnde.displayLength(objElement,max)');
    },
    Date: {
        validate: function(dateStr) {
            return oFnde.validate.date(dateStr)
            oFnde.deprecated('Helper.Date.validate(dateStr)', 'oFnde.validate.date(dateStr)');
        }
    },
    TimerSection: function(timeSection, callback) {
        oFnde.timerSection(timeSection, callback);
        oFnde.deprecated('Helper.timerSection(timeSection)', 'oFnde.timerSection(timeSection, callback)');

    },
    DataTable: function(tableId, head, footer, settings) {
        jQuery(function() {
            oFnde.dataTable(tableId, head, footer, settings);
            oFnde.deprecated('Helper.DataTable(tableId,head,footer,settings)', 'oFnde.dataTable(tableId,head,footer,settings)');
        })
    }
};