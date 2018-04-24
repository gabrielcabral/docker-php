function configuraAtividades(){

	
	$("#fieldset-abaOutrasInformacoes").find("input[type=text]").attr("name","DS_ATIVIDADE_ALTERNATIVA[]");
	$("#fieldset-abaOutrasInformacoes").find("select").attr("name","NU_SEQ_ATIVIDADE[]");
	$("#fieldset-abaOutrasInformacoes").find("label").css("display","inline");
	
	var remove = '<a href="#f" class="icoExcluir excluir" style=" word-spacing:20px; position:absolute; display:inline; margin-left:5px; " mensagem="Deseja realmente excluir o registro?" title="Excluir" >X</a>';
	
	$('div.remove').each(function(){
		
		var msg = $(this).find('.msgErro');
		
		if(msg.html() != null && msg.html() != ''){
			
			var msgSelect = $(this).find('select').parent().find('.msgErro'); 
			
			if(msgSelect.html() != null && msgSelect.html() != ''){
				$(this).find('.msgErro').remove();
				$(this).append(msg);
				$(this).find('.msgErro').before(remove);
			}else{
				$(this).find('.msgErro').before(remove);
				$(this).find('.excluir').css('margin-left','0');
				$(this).find('input').parent().css('vertical-align','top');
				$(this).find('input').parent().css('display','inline-block');
			}
			
		}else{				
			$(this).append(remove);
		}
		if(!$("#abaOutrasInformacoes").hasClass('readonly')){
			$(this).find(".icoExcluir").click(function(){
				var registro = this;
				Dialog.confirm($(this).attr('mensagem'),'Confirmação', function(r){
					if(r == true){
						$(registro).closest('div').remove();
					}
				});
				return false;
			});
		}
	});
	
	$("#fieldset-abaOutrasInformacoes").find("select").change(function(){
		var id = $(this).parent().parent().find("input[type=text]").attr("id");
		if(this.value == '10'){
			$("label[for="+id+"]").css('display','inline-block');
		}else{
			$("label[for="+id+"]").css('display','none');
			$("label[for="+id+"]").find('input').val('');
		}
	});
	
	
	$("#fieldset-abaOutrasInformacoes").find("input[type=text]").each(function(){
		var valor = $(this).parent().parent().find("select").val();
		var id = this.id;
		if(valor == '10'){
			$("label[for="+id+"]").css('display','inline-block');
		}else{
			$("label[for="+id+"]").css('display','none');
			$("label[for="+id+"]").find('input').val('');
		}
	});
	
	if($("#abaOutrasInformacoes").hasClass('readonly')){
		$("#btAdcionarAtividade").attr('disabled','1');
		$("#abaOutrasInformacoes").find(".icoExcluir").addClass('disabled');
		$("input").attr('disabled','disabled');
		$("#fieldset-abaOutrasInformacoes").find("select").attr('disabled','disabled');
				
	}else{

		$("#btAdcionarAtividade").click(function(){
                    
			$("#fieldset-abaOutrasInformacoes").append(htmlAuxiliar);
			
			$("#fieldset-abaOutrasInformacoes").find("select").parent().css('display','inline-block');
			$("#fieldset-abaOutrasInformacoes").find("select").parent().css('vertical-align','top');
			
			var idxAtividade = 1;
			$("#fieldset-abaOutrasInformacoes").find("select").each(function(){
				$(this).attr("name","NU_SEQ_ATIVIDADE[]");
				$(this).attr("id","NU_SEQ_ATIVIDADE" + idxAtividade++);
			});
			
			idxAtividade = 1;
			$("#fieldset-abaOutrasInformacoes").find("input[type=text]").each(function(){
				$(this).attr("name","DS_ATIVIDADE_ALTERNATIVA[]");
				$(this).attr("id","DS_ATIVIDADE_ALTERNATIVA" + idxAtividade);
				$(this).parent().attr("for","DS_ATIVIDADE_ALTERNATIVA" + idxAtividade++);
			});
			
			$("#fieldset-abaOutrasInformacoes .icoExcluir").remove();
			
			$('div.remove').each(function(){
				
				
				var msg = $(this).find('.msgErro');
				
				if(msg.html() != null && msg.html() != ''){
					
					var msgSelect = $(this).find('select').parent().find('.msgErro'); 
					
					if(msgSelect.html() != null && msgSelect.html() != ''){
						$(this).find('.msgErro').remove();
						$(this).append(msg);
						$(this).find('.msgErro').before(remove);
					}else{
						$(this).find('.msgErro').before(remove);
						$(this).find('input').parent().css('vertical-align','top');
						$(this).find('input').parent().css('display','inline-block');
					}
					
				}else{				
					$(this).append(remove);
				}
				
				
				$(this).find(".icoExcluir").click(function(){
					var registro = this;
					Dialog.confirm($(this).attr('mensagem'),'Confirmação', function(r){
						if(r == true){
							$(registro).parent().remove();
						}
					});
					return false;
				});
			});
			
			
	
			$("#fieldset-abaOutrasInformacoes").find("select").change(function(){
				var id = $(this).parent().parent().find("input[type=text]").attr("id");
				if(this.value == '10'){
					$("label[for="+id+"]").css('display','inline-block');
				}else{
					$("label[for="+id+"]").css('display','none');
					$("label[for="+id+"]").find('input').val('');
				}
			});
			
			$("#fieldset-abaOutrasInformacoes").find("input[type=text]").each(function(){
				var valor = $(this).parent().parent().find("select").val();
				var id = this.id;
				if(valor == '10'){
					$("label[for="+id+"]").css('display','inline-block');
				}else{
					$("label[for="+id+"]").css('display','none');
					$("label[for="+id+"]").find('input').val('');
				}
			});
			
			
		});
	
	}
	
	
}
