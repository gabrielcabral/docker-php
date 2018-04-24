/**
* Picklists - jQuery plugin for converting a multiple <select> into two, allowing users to easily select multiple items
* Based on code from Multiple Selects Plugin: http://code.google.com/p/jqpickLists/
*
* Version: 0.1
*/

/**
* Adds multiple select behaviour to a <select> element.
* This allows options to be transferred to a different select using mouse double-clicks, or multiple options at a time via a button element.
*
* @usage
* $('#simple').pickList(options);
* $('#simple').pickList('enable');
* $('#simple').pickList('disable');
*/

(function( $ ){

    var methods = {
        init : function( settings ) {
                       
            // set some sensible defaults
            settings = jQuery.extend({
                buttons: true,
                removeText: ' < ',
                addText: ' > ',
                removeAllText : ' << ',
                addAllText : ' >> ',
                cssClass: 'agrupador inLine listaSelecao',
                ieBg: '',
                ieColor: 'graytext',
                fromLabel : 'Dispon&iacute;veis',
                toLabel : 'Selecionados',
                testMode: false
            }, settings);
            return this.each(function() {
        
                if (this.multiple == false) {
                    return;
                }
                var name = this.name;
                if (!this.id) {
                    // we really need an id for this to work properly, so let's create one 
                    // (needs error checking to see if id already exists)
                    this.id = this.name.match(/[a-zA-Z0-9]+/);
                }
                var id = this.id;

                var select = jQuery('#' + id);
		
                // add onsubmit stuff to the form so all the selected elements get passed through correctly
                jQuery(this.form)
                .submit(function(e) {
                    if (settings.testMode) e.preventDefault();
                    for(var item = 0; item < this.pickLists.length; item++)
                    {
                        selectAll(this.pickLists[item]);
                    }
                })
                .each(function() {
                    if (this.pickLists == undefined) this.pickLists = new Array();
                    this.pickLists.push(id);
                });

                var container  = $(this).parent();
                var legend = container.children('span');
                var msgOrientacao = container.children('.msgOrientacao')
                var msgErro = container.children('.msgErro');
                var size = 6;
                var padding = ($(this).height()- 96)/2;
                
                
                if(parseFloat($(this).attr('size')) > size){
                    size = $(this).attr('size');
                }else{
                    $(this).attr('size',size);
                };
                
               
                container.after($('<fieldset class="'+settings.cssClass+' '+legend.attr('class')+'"></fieldset>')
                    .append('<legend>'+legend.html()+'</legend>') // legenda
                        
                    // from
                    .append($('<label for="from_'+id+'"></label>')
                        .append('<span>'+settings.fromLabel+'</span>')
                        .append('<select id="from_'+id+'" multiple="multiple" size="'+size+'" ></select>'))
                         
                    // botoes
                    .append($('<div class="pickListButtons"></div>')
                        .attr('style','padding-top:'+padding+'px')
                        .append('<button id="b_to_all_' + id + '">'+settings.addAllText+'</button>')
                        .append('<button id="b_to_' + id + '">'+settings.addText+'</button>')
                        .append('<button id="b_from_' + id + '">'+settings.removeText+'</button>')
                        .append('<button id="b_from_all_' + id + '">'+settings.removeAllText+'</button>')

                        )
                        
                    // to
                    .append($('<label for="'+id+'"></label>')
                        .append('<span>'+settings.toLabel+'</span>')
                        .append(this))
                        
                    // mensagens
                    .append(msgOrientacao)
                    .append(msgErro)
                    );
        
                container.remove();
                
                moveAllOptions(id, 'from_' + id);
        
                jQuery('#from_' + id).dblclick(function() {
                    addTo('from_' + id, id);
                });
        
                jQuery('#' + id).dblclick(function() {
                    moveFrom(id, 'from_' + id);
                });

                if (settings.buttons)
                {
                    jQuery("#b_to_"+id).click(function(e) {
                
                        e.preventDefault();
                        addTo('from_' + id, id);                    
                    });
                    jQuery("#b_from_"+id).click(function(e) {
                        e.preventDefault();
                        moveFrom(id, 'from_' + id);
                    });
                    jQuery("#b_to_all_"+id).click(function(e) {
                        e.preventDefault();
                        addAllTo('from_' + id, id);
                    });
                    jQuery("#b_from_all_"+id).click(function(e) {
                        e.preventDefault();
                        moveAllFrom(id, 'from_' + id);
                    });                        
                }
                if (jQuery.fn.emulateDisabled) 
                    jQuery('#from_' + id).emulateDisabled();
                if (jQuery.fn.obviouslyDisabled)
                    jQuery('#from_' + id).obviouslyDisabled({
                        textColor: settings.ieColor, 
                        bgColor: settings.ieBg
                    });
                
                if(select.attr('disabled')){
                    select.pickList('disable');
                }else{
                    select.pickList('enable');
                }
            });   
        },
       
        enable : function(){
            $(this).parent().parent().find('select,button').removeAttr('disabled');
        },
       
        disable : function() {
            $(this).parent().parent().find('select,button').attr('disabled','disabled');
        }
    };

    $.fn.pickList = function( method ) {
        // Method calling logic
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            alert('Metodo ou propriedade não suportado!');
        }    
    };
       
    function selectAll(me) {
        $('#' + me + ' option').attr('selected', true);
        $('#from_' + me + ' option').attr('selected', false);
    }
    function addTo(from, to)
    {
        var dest = jQuery("#"+to)[0];

        jQuery("#"+from+" option:selected").clone().each(function() {
            if (this.disabled == true) return
            jQuery(this)
            .appendTo(dest)
            .attr("selected", false);
        });
        jQuery("#"+from+" option:selected")
        .attr("selected", false)
        .attr("disabled", "disabled")
		
        if (jQuery.fn.obviouslyDisabled)
            jQuery("#"+from).obviouslyDisabled({
                textColor: settings.ieColor, 
                bgColor: settings.ieBg
            });
    }
    function addAllTo(from, to){
        var dest = jQuery("#"+to)[0];

        jQuery("#"+from+" option").clone().each(function() {
            if (this.disabled == true) return
            jQuery(this)
            .appendTo(dest)
            .attr("selected", false);
        });
        jQuery("#"+from+" option")
        .attr("selected", false)
        .attr("disabled", "disabled")
		
        if (jQuery.fn.obviouslyDisabled)
            jQuery("#"+from).obviouslyDisabled({
                textColor: settings.ieColor, 
                bgColor: settings.ieBg
            });  
    }
    function moveAllFrom(from, to){
        var dest = jQuery("#"+to)[0];
        jQuery("#"+from+" option").each(function() 
        {
            select = jQuery(this)
            val = select
            .attr("selected", false)
            .val();
            select.remove();
            jQuery('option:disabled', jQuery("#"+to)).each(function() 
            {
                if (this.value == val)
                {
                    jQuery(this).attr("disabled", false);
                }
            });
        });
		
        if (jQuery.fn.obviouslyDisabled)
            jQuery("#"+to).obviouslyDisabled({
                textColor: settings.ieColor, 
                bgColor: settings.ieBg
            });           
    }
    function moveFrom(from, to)
    {
        var dest = jQuery("#"+to)[0];
        jQuery("#"+from+" option:selected").each(function() 
        {
            select = jQuery(this)
            val = select
            .attr("selected", false)
            .val();
            select.remove();
            jQuery('option:disabled', jQuery("#"+to)).each(function() 
            {
                if (this.value == val)
                {
                    jQuery(this).attr("disabled", false);
                }
            });
        });
		
        if (jQuery.fn.obviouslyDisabled)
            jQuery("#"+to).obviouslyDisabled({
                textColor: settings.ieColor, 
                bgColor: settings.ieBg
            });

    }
    function moveAllOptions(from, to) {
        jQuery("#"+to).html(jQuery("#"+from).html())
        .find('option:selected')
        .attr("selected", false)
        .attr("disabled", "disabled");
        
        jQuery("#"+from+" option").each(function(){
            if(!$(this).attr('selected')){
                jQuery(this).remove();
            }else{
                jQuery(this).attr("selected", false);
            }
        });
    }

})( jQuery );