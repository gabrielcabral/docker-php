$(document).ready(function() {
	

	var obj = $("#CO_DISTANCIA_PAGAMENTO");
	var pai = obj.parent();
	obj.remove();
	pai.find('.campoRequerido').after(obj);
	
	var objSgUfPagamento = $("#SG_UF_PAGAMENTO");
	var paiSgUfPagamento = objSgUfPagamento.parent();
	objSgUfPagamento.remove();
	paiSgUfPagamento.find('.campoRequerido').after(objSgUfPagamento);
	
    $("#CO_DISTANCIA_PAGAMENTO").change(function(){
        if($("#CO_MUNICIPIO_PERFIL").val() == ""){
            Dialog.error('É necessário informar o município de atuação do perfil');
            this.value = '';
        } else {
            carregaUFs($("#CO_MUNICIPIO_PERFIL").val(), this.value);
        }
    });
    $("#SG_UF_PAGAMENTO").change(function(){
        carregaMunicipios($("#CO_MUNICIPIO_PERFIL").val(), $("#CO_DISTANCIA_PAGAMENTO").val(),this.value);
    });
    $("#CO_MUNICIPIO_PAGAMENTO").change(function(){
        carregaAgencias($("#CO_MUNICIPIO_PERFIL").val(), this.value,$("#CO_DISTANCIA_PAGAMENTO").val(),$("#SG_UF_PAGAMENTO").val());
    });
});

function carregaUFs(cod_municipio, distancia){
    jQuery.ajax({
        type: "GET",
        url: baseUrl+"/index.php//manutencao/usuario/carrega-uf/CO_MUNICIPIO_PERFIL/" + cod_municipio+ "/CO_DISTANCIA_PAGAMENTO/"+ distancia,
        async: false,
        beforeSend: function() {
        },
        success: function(resp) {
        	var find = $(resp).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
        	
            $('#CO_MUNICIPIO_PAGAMENTO').html('<option value="" selected=""> Selecione </option>');
            $('#CO_AGENCIA_PAGAMENTO').html('<option value="" selected=""> Selecione </option>');

            var label = $("label[for=SG_UF_PAGAMENTO]");
            if(resp.indexOf('h1') == -1 ){
            $(label).find('select').html(resp);
            }else{
            $(label).find('select').html('<option value="" selected=""> Selecione </option>'); 
            }

        },
        error: function(resp) {
        }
    });
}
function carregaMunicipios(cod_municipio, distancia, sg_uf){
    jQuery.ajax({
        type: "GET",
        url: baseUrl+"/index.php/manutencao/usuario/carrega-municipio/CO_MUNICIPIO_PERFIL/" + cod_municipio+ "/CO_DISTANCIA_PAGAMENTO/"+ distancia +"/SG_UF_PAGAMENTO/"+sg_uf,
        async: false,
        beforeSend: function() {
        },
        success: function(resp) {
        	var find = $(resp).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
        	
            $('#CO_AGENCIA_PAGAMENTO').html('<option value="" selected=""> Selecione </option>');
            
            var label = $("label[for=CO_MUNICIPIO_PAGAMENTO]");
            $(label).find('select').html(resp);
        },
        error: function(resp) {
        }
    });
}
function carregaAgencias(cod_municipio, co_municipio_ibge_encontrado, distancia, sg_uf){
    jQuery.ajax({
        type: "GET",
        url: baseUrl+"/index.php/manutencao/usuario/carrega-agencias/CO_MUNICIPIO_PERFIL/" + cod_municipio+ "/CO_DISTANCIA_PAGAMENTO/"+ distancia+"/SG_UF_PAGAMENTO/"+ sg_uf+"/CO_MUNICIPIO_PAGAMENTO/"+ co_municipio_ibge_encontrado,
        async: false,
        beforeSend: function() {
        },
        success: function(resp) {
        	var find = $(resp).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
        	
            var label = $("label[for=CO_AGENCIA_PAGAMENTO]");
            $(label).find('select').html(resp);
        },
        error: function(resp) {
        }
    });
}
