/*
Sistema Sistema BASE
Setor responsável: SGETI/FNDE
Analista / Programador: Daniel Wilson de Alvarenga ()
E-Mail: dwa8125@gmail.com
Finalidade: Funções de validação em Javascript
Data de criação: 11/06/2010
*/

$().ready(
    function() {
    	
    	$("#NU_CEP").mask('99999-999');
    	$("#NU_TELEFONE").mask('99 9999-9999');
    	$("#NU_CELULAR").mask('99 9999-9999');
    	
    	validaCpfReceitaAction();
    	
		var d = new Date();
		var curr_date = d.getDate();
		 if(curr_date<10) curr_date = '0'+curr_date;
		
		var curr_month = d.getMonth()+1;
		 if(curr_month<10) curr_month = '0'+curr_month;
		
		var curr_year = d.getFullYear();
		var dtAtual = curr_date+'/'+curr_month+'/'+curr_year;

		loadBeneficiario();
		
		$('#SG_UF').change(function(){
			$('#CO_MUNICIPIO_FNDE').load(baseUrl + '/index.php/contrato/tor-produto/get-by-municipio/sg_uf/' + $(this).val());
		});
				
    	$('#DT_NASCIMENTO').blur(function(){
    		var dtNascimento = $('#DT_NASCIMENTO').val();
	    	if( dtNascimento ){
        		if( validaDataMaior(dtNascimento,dtAtual) == true){
        			$('#DT_NASCIMENTO').val('');
        			Dialog.alert( 'A data de nascimento não pode ser maior que a data de hoje.',null,function(r){
        				$('#DT_NASCIMENTO').focus();
        			} );
	    		}
	    	}
	    });  
    	
    	$('#DT_EXPEDICAO_RG').blur(function(){
    		var dtExpedicao = $('#DT_EXPEDICAO_RG').val();
	    	if( dtExpedicao ){
        		if( validaDataMaior(dtExpedicao,dtAtual) == true){
        			Dialog.alert( 'A data de expedição não pode ser maior que a data atual.' );
        			$('#DT_EXPEDICAO_RG').val('');
	    		}
	    	}
	    });  
    	
    	$('#NU_CEP').change(function(){
    		var nuCep = somenteNumeros( $(this).val() );
    		if( nuCep.length != 8 && $(this).val() != '_____-___'){
    			$(this).val('');
    			Dialog.alert('CEP Inválido!',null,function(r){
    				$(this).focus();
    			});
    			return false;
    		}
	    });
    	
    	$('#subformBeneficiario-NU_CEP_BENEFICIARIO').change(function(){
    		var nuCep = somenteNumeros( $(this).val() );
    		if( nuCep.length != 8 && $(this).val() != '_____-___'){
    			$(this).val('');
    			Dialog.alert('CEP Inválido!',null,function(r){
    				$(this).focus();
    			});
    			return false;
    		}
	    });
    	
    	$('#NU_TELEFONE').change(function(){
    		var nuTelefone = somenteNumeros( $(this).val() );
    		if( nuTelefone.length != 10 && $(this).val() != '__ ____-____'){
    			$(this).val('');
    			Dialog.alert('Telefone inválido!',null,function(r){
    				$(this).focus();
    			});
    			return false;
    		}
	    });
    	
    	$('#NU_CELULAR').change(function(){
    		var nuCelular = somenteNumeros( $(this).val() );
    		if( nuCelular.length != 10 && $(this).val() != '__ ____-____'){
    			$(this).val('');
    			Dialog.alert('Celular inválido!',null,function(r){
    				$(this).focus();
    			});
    			return false;
    		}
	    });
    	
    	$("#confirmar").click( function(){
    		var cpf = String( somenteNumeros( $('#NU_CPF_PESSOA').val() ) );
    		var nu_seq_pessoa = $('#NU_SEQ_PESSOA_CADASTRO').val();
    		
    		if( cpf != '0' && cpf != ''){
	    		if( cpf.length != 11 ){
	    			getVerificaCpfAjax(cpf, nu_seq_pessoa);
	    			$(this).val('');
	    			Dialog.alert( 'CPF inválido!' ,null,function(r){
	    				$(this).focus();
	    			});
	    			return false;
	    		}
    		} else{
    			$('#NU_CPF_PESSOA').val('');
    		}
		});
    	
    	$("#NU_CPF_PESSOA").blur(
    		function(){
				var nu_cpf_pessoa = $('#NU_CPF_PESSOA').val();
				var nu_seq_pessoa = $('#NU_SEQ_PESSOA_CADASTRO').val();
				if($("#NU_CPF_PESSOA").val() == $("#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO").val() && $("#NU_CPF_PESSOA").val() != '___.___.___-__' && $("#NU_CPF_PESSOA").val() != '' || !validaCPFAdicionado()){
					Dialog.alert('O CPF do beneficiário não pode ser o mesmo do consultor.',null,function(r){
						$("#NU_CPF_PESSOA").val('');
					});
				} else {
					getVerificaCpfAjax(nu_cpf_pessoa, nu_seq_pessoa);
				}
    	});
    	
    	$("#NO_PESSOA").keyup(function(event){
    		$(this).val($(this).val().toUpperCase());
    		retirarAcentos(this,event);
    	});
    	
    	$("#DS_EMAIL_PESSOA").blur( 
    		function(){
        		if( !validaEmail( $(this).val() ) ){
        			if($(this).val()){
        				$(this).val('');
	        			Dialog.alert('E-mail Inválido!',null ,function (r){
	        				$(this).focus();
	        			});
        			}
        		}
       	});
    	
    	$("#pesquisar").click( 
        	function(){
        	var nu_cep = $('#NU_CEP').val();
        	getVerificaCepAjax(nu_cep);
       	});
    	
		$('#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO').val('');
		$('#subformBeneficiario-NO_PESSOA_BENEFICIARIO').val('');
		$('#subformBeneficiario-NU_CEP_BENEFICIARIO').val('');
		$('#subformBeneficiario-DS_ENDERECO_BENEFICIARIO').val('');
		$('#subformBeneficiario-DS_LOGRADOURO_BENEFICIARIO').val('');
		$('#subformBeneficiario-DS_BAIRRO_BENEFICIARIO').val('');
		$('#subformBeneficiario-SG_UF_BENEFICIARIO').val('');
		$('#subformBeneficiario-CO_MUNICIPIO_FNDE_BENEFICIARIO').val('');
});

function validaCPFAdicionado()
{
	var result = '';
	var id = $(".idCpf");
	var nu_cpf = $('#NU_CPF_PESSOA').val();
	
	for(i=0; i < id.length; i++) {
		if(id.eq(i).val() != 'Sem Resultado'){
			if(id.eq(i).val() === nu_cpf && nu_cpf != '') {
				result = id.eq(i).val();
			}
		}
	}
	
	if(result){
		Dialog.alert('O CPF do beneficiário não pode ser o mesmo do consultor.',null,function(r){
			$("#NU_CPF_PESSOA").val('');
		});
		return false;
	}
	return true;
	
	
}

function validaCpfReceitaAction(){
	if($('#NU_CPF_PESSOA').val()){
		popUp.mensagem(1);
		$.getJSON(baseUrl + '/index.php/contrato/consultor/get-cpf-receita/cpf/'+$('#NU_CPF_PESSOA').val(),'',
		    function(pessoa) {
			if(pessoa){
				$('#NO_PESSOA_RECEITA').val(pessoa.nome);
				if(pessoa.nome != $('#NO_PESSOA').val()){
					$('#NO_PESSOA').val('');
			    }
				if($('#NU_SEQ_PESSOA_CADASTRO').val()){
					if($('#NO_PESSOA').val()!= pessoa.nome){
						Dialog.alert("Nome não confere!");
						$('#NO_PESSOA').focus();
					}
				}
				$('#DT_NASCIMENTO').val(pessoa.dt_nasc);
			} else {
				$('#NU_CPF_PESSOA').val('');
				$('#NO_PESSOA').val('');
				$('#NO_PESSOA_RECEITA').val('');
				$('#DT_NASCIMENTO').val('');
				Dialog.alert("CPF não existe.");
			}
			popUp.mensagem(1);
			});
	} else {
		$('#NU_CPF_PESSOA').val('');
		$('#NO_PESSOA').val('');
		$('#NO_PESSOA_RECEITA').val('');
		$('#DT_NASCIMENTO').val('');
		
	}
}

function validaCpfBenReceitaAction(){
	if($('#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO').val()){
		popUp.mensagem(1);
		$.getJSON(baseUrl + '/index.php/contrato/consultor/get-cpf-receita/cpf/'+$('#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO').val(),'',
		    function(pessoa) {
			if(pessoa){
				$('#subformBeneficiario-NO_PESSOA_BENEFICIARIO_RECEITA').val(pessoa.nome);
				if(pessoa.nome != $('#NO_PESSOA_BENEFICIARIO').val('')){
					$('#subformBeneficiario-NO_PESSOA_BENEFICIARIO').val('');
			    }
			} else {
				$('#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO').val('');
				$('#subformBeneficiario-NO_PESSOA_BENEFICIARIO_RECEITA').val('');
				Dialog.alert("CPF não encontrado.");
			}
			popUp.mensagem(1);
			});
	} else {
		$('#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO').val('');
		$('#subformBeneficiario-NO_PESSOA_BENEFICIARIO_RECEITA').val('');
		$('#DT_NASCIMENTO').val('');
	}
}

function loadBeneficiario()
{
	validaCpfBenReceitaAction();
	$("#subformBeneficiario-NO_PESSOA_BENEFICIARIO").keyup(function(event){
		$(this).val($(this).val().toUpperCase());
		retirarAcentos(this,event);
	});
	
	$("#subformBeneficiario-NO_PESSOA_BENEFICIARIO").blur( 
		function(){
			if($(this).val() != $("#subformBeneficiario-NO_PESSOA_BENEFICIARIO_RECEITA").val() && $(this).val() != ''){
				Dialog.alert("Nome não confere!",null,function(r){
					$("#subformBeneficiario-NO_PESSOA_BENEFICIARIO").val('');
					$("#subformBeneficiario-NO_PESSOA_BENEFICIARIO").focus();
				});
			}
    });
	
	$("#subformBeneficiario-NU_CEP_BENEFICIARIO").mask('99999-999');
	
	$('#subformBeneficiario-SG_UF_BENEFICIARIO').change(function(){
		$('#subformBeneficiario-CO_MUNICIPIO_FNDE_BENEFICIARIO').load(baseUrl + '/index.php/contrato/tor-produto/get-by-municipio/sg_uf/' + $(this).val());
	});
	
	$('#subformBeneficiario-CO_MUNICIPIO_FNDE_BENEFICIARIO').change(function(){
		$('#subformBeneficiario-NU_SEQ_MUNICIPIO_BENEFICIARIO').val($('#subformBeneficiario-CO_MUNICIPIO_FNDE_BENEFICIARIO').val());
	});
		
	if($('#subformBeneficiario-NU_SEQ_MUNICIPIO_BENEFICIARIO').val()){
		var codMunicipio = $('#subformBeneficiario-NU_SEQ_MUNICIPIO_BENEFICIARIO').val();
		$("#subformBeneficiario-CO_MUNICIPIO_FNDE_BENEFICIARIO").load(baseUrl + '/index.php/contrato/tor-produto/get-by-municipio/sg_uf/' + $('#subformBeneficiario-SG_UF_BENEFICIARIO').val(), function(){
			$("#subformBeneficiario-CO_MUNICIPIO_FNDE_BENEFICIARIO option[value='"+ codMunicipio +"']").attr('selected', 'selected');
		});
	}
	
	$("#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO").blur(function(){
		if($("#NU_CPF_PESSOA").val() == $("#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO").val() && $("#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO").val() != '___.___.___-__' && $("#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO").val() != ''){
			Dialog.alert('O CPF do beneficiário não pode ser o mesmo do consultor.',null,function(r){
				$("#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO").val('');
			});
		}else if ($("#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO").val() != '___.___.___-__' && $("#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO").val() != '') {
			validaCpfBenReceitaAction();
		} else if ($("#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO").val() == '___.___.___-__' || $("#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO").val() == ''){
			$("#subformBeneficiario-NO_PESSOA_BENEFICIARIO_RECEITA").val('');
		}
	});
	
	$("#pesquisarBene").click(
        	function(){
        	var nu_cep = $('#subformBeneficiario-NU_CEP_BENEFICIARIO').val();
        	getVerificaCepBeneficiarioAjax(nu_cep);
   });
	
	$('#subformBeneficiario-Adicionar').click(
		function(){
			//Validação de Regra de negócio
			if(validaCpfBeneficiario() == false){return false;}
			ajax.postAjax( baseUrl + '/index.php/contrato/consultor/add-item-list/','form','subformBeneficiario',1);
	});
	
	$('#subformBeneficiario-ST_END_DIFERENTE-1').click(function(){
		if($('#subformBeneficiario-ST_END_DIFERENTE-1').is(':checked')){
			$('#subformBeneficiario-NU_CEP_BENEFICIARIO').val($('#NU_CEP').val());
			$('#subformBeneficiario-DS_ENDERECO_BENEFICIARIO').val($('#DS_ENDERECO').val());
			$('#subformBeneficiario-DS_LOGRADOURO_BENEFICIARIO').val($('#DS_LOGRADOURO').val());
			$('#subformBeneficiario-DS_BAIRRO_BENEFICIARIO').val($('#DS_BAIRRO').val());
			$('#subformBeneficiario-SG_UF_BENEFICIARIO').val($('#SG_UF').val());
//			$('#subformBeneficiario-CO_MUNICIPIO_FNDE_BENEFICIARIO').load(baseUrl + '/index.php/contrato/consultor/get-popula-municipio/uf/'+$('#SG_UF').val()+'/municipioCodigoFNDE/'+$('#CO_MUNICIPIO_FNDE').val());
			$('#subformBeneficiario-NU_SEQ_MUNICIPIO_BENEFICIARIO').val($('#CO_MUNICIPIO_FNDE').val());
			
			if($('#subformBeneficiario-NU_SEQ_MUNICIPIO_BENEFICIARIO').val()){
				var codMunicipio = $('#subformBeneficiario-NU_SEQ_MUNICIPIO_BENEFICIARIO').val();
				$("#subformBeneficiario-CO_MUNICIPIO_FNDE_BENEFICIARIO").load(baseUrl + '/index.php/contrato/tor-produto/get-by-municipio/sg_uf/' + $('#subformBeneficiario-SG_UF_BENEFICIARIO').val(), function(){
					$("#subformBeneficiario-CO_MUNICIPIO_FNDE_BENEFICIARIO option[value='"+ codMunicipio +"']").attr('selected', 'selected');
				});
			}
		} else {
			$('#subformBeneficiario-NU_CEP_BENEFICIARIO').val('');
			$('#subformBeneficiario-DS_ENDERECO_BENEFICIARIO').val('');
			$('#subformBeneficiario-DS_LOGRADOURO_BENEFICIARIO').val('');
			$('#subformBeneficiario-DS_BAIRRO_BENEFICIARIO').val('');
			$('#subformBeneficiario-SG_UF_BENEFICIARIO').val('');
			$('#subformBeneficiario-CO_MUNICIPIO_FNDE_BENEFICIARIO').val('');
		}
	});
}
function validaCpfBeneficiario(){

	var result = '';
	var id = $(".idCpf");
	var nu_cpf = $('#subformBeneficiario-NU_CPF_PESSOA_BENEFICIARIO').val();
	
		for(i=0; i < id.length; i++) {
			if(id.eq(i).val() != 'Sem Resultado'){
				if(id.eq(i).val() === nu_cpf && nu_cpf != '') {
					result = id.eq(i).val();
				}
			}
		}
	
	if(result){
		Dialog.alert('O CPF '+nu_cpf+' já está cadastrado para este consultor.');
		return false;
	}
}

function getVerificaCpfAjax(nu_cpf_pessoa, nu_seq_pessoa){
	$.ajax({
		async: false,
        url: baseUrl + '/index.php/contrato/consultor/get-cpf-existe/',
        type: "GET",
        data: "nu_cpf_pessoa = " + nu_cpf_pessoa,
     success: function(result) {
    	var find = $(result).find('#loginBase #login').html();
		if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
		if(result){
			result = result.split("|");
			if(nu_seq_pessoa != result[1]){
				$('#NU_CPF_PESSOA').val('');
				$("#NO_PESSOA_RECEITA").val('');
				$("#NO_PESSOA").val('');
				$('#DT_NASCIMENTO').val('');
				Dialog.alert('CPF: ' + result[0] + ' já está cadastrado no sistema!',null,function(r){
					$('#NU_CPF_PESSOA').focus();
				});
			} else {
				validaCpfReceitaAction();
			}
			
			return false;
		} else {
			validaCpfReceitaAction();
		}
	}
 });
}

function getVerificaCepAjax(nu_cep){
	
	$.getJSON(baseUrl + '/index.php/contrato/consultor/get-consulta-cep/cep/'+nu_cep,'',
        function(endereco) {
		if(endereco == null){
			Dialog.alert('CEP não encontrado!');
			return false;
		} else{
			$('#DS_ENDERECO').val(endereco.endereco);
			$('#DS_LOGRADOURO').val(endereco.complemento);
			$('#DS_BAIRRO').val(endereco.bairro);
			$('#SG_UF').load(baseUrl + '/index.php/contrato/consultor/get-popula-estado/uf/'+endereco.uf);
			$('#CO_MUNICIPIO_FNDE').load(baseUrl + '/index.php/contrato/consultor/get-popula-municipio/uf/'+endereco.uf+'/municipioCodigoFNDE/'+endereco.municipioCodigoFNDE);
		}
		
	});
}
function getVerificaCepBeneficiarioAjax(nu_cep){
	$.getJSON(baseUrl + '/index.php/contrato/consultor/get-consulta-cep/cep/'+nu_cep,'',
        function(endereco) {
		if(endereco == null){
			Dialog.alert('CEP não encontrado!');
			return false;
		} else {
			$('#subformBeneficiario-DS_ENDERECO_BENEFICIARIO').val(endereco.endereco);
			$('#subformBeneficiario-DS_LOGRADOURO_BENEFICIARIO').val(endereco.complemento);
			$('#subformBeneficiario-DS_BAIRRO_BENEFICIARIO').val(endereco.bairro);
			$('#subformBeneficiario-SG_UF_BENEFICIARIO').load(baseUrl + '/index.php/contrato/consultor/get-popula-estado/uf/'+endereco.uf);
			$('#subformBeneficiario-CO_MUNICIPIO_FNDE_BENEFICIARIO').load(baseUrl + '/index.php/contrato/consultor/get-popula-municipio/uf/'+endereco.uf+'/municipioCodigoFNDE/'+endereco.municipioCodigoFNDE);
		}
	});
}

function removeItemList( id ){
	if( id ){
		Dialog.confirm( "Confirma a exclusão das informações do beneficiário?", null, function(r){
			if(r){
				ajax.postAjax( baseUrl + '/index.php/contrato/consultor/remove-item-list/id/'+id,'form','subformBeneficiario',1);
			}
		});
	}
}