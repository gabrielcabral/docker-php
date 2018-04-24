/*
Sistema SICEWEB
Setor responsável: SGETI/FNDE
Analista / Programador: Tiago Augusto Ramos ()
E-Mail: tiago.ramos@cpmbraxis.com
Finalidade: Funções de validação em Javascript
Data de criação: 10/04/2012
 */

/*$(".icoAceitar").click(function() {
 var link = this;
 Dialog.confirm($(this).attr('mensagem'), 'Confirmação', function(r) {
 if (r == true) {
 location.href = link.href;
 }
 });
 return false;
 });*/

if ($('div.tab') != null) {
	var html = $('#form').html();
	$('div.tab').append(html);
	$('#form').remove();
}

$("#Adicionar").click(function() {
	$.ajax({
		url : $(this).attr('href'),
		success : function(data) {
			var find = $(data).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

			Dialog.modal(data.replace(/\n/g, ''), 'Adicionar Formação', null);
		}
	});

	return false;
});

// Exibe mensagens de confirmação ao tentar excluir ou alterar a situação de um usuário
$(".icoAceitar").click(function() {
	if($(this).attr('mensagem') != ''){
		var link = this;
		Dialog.confirm($(this).attr('mensagem'), 'Confirmação', function(r) {
			if (r == true) {
				location.href = link.href;
			}
		});
		return false;
	}
});
$(".icoExcluir").click(function() {
	if($(this).attr('mensagem') != ''){
		var link = this;
		Dialog.confirm($(this).attr('mensagem'), 'Confirmação', function(r) {
			if (r == true) {
				location.href = link.href;
			}
		});
		return false;
	}
});
// fim mensagem de confirmação.

if ($('div.tab') != null) {
    var html = $('#form').html();
	$('div.tab').append(html);
	$('#form').remove();
}


//TELA DE FILTRO
//Combo mais ações
$("#table_main_action").change(
		function() {
			if ($('#table_main_action').val() == null
					|| $('#table_main_action').val() == '') {
				$("#table_button").attr("disabled", "disabled");
			} else {
				$("#table_button").removeAttr("disabled");
			}
		});

$("#table_button").click(
		function() {
			if ($('input[name=NU_SEQ_USUARIO]:checked').val() == null) {
				Dialog.error('Nenhum usuário bolsista selecionado', null, null);
			} else {
				window.location.href = $('#table_main_action').val()
						+ "/NU_SEQ_USUARIO/"
						+ $('input[name=NU_SEQ_USUARIO]:checked').val();
			}
		});
//Fim combo mais ações


// Tela de filtro

var changeUfAtuacaoPerfil = function() {

	//Método que faz o efeito de loading.
	popUp.carregando();

	$.ajax({
		url : baseUrl
		+ '/index.php/manutencao/usuario/renderiza-mesoregiao/SG_UF_ATUACAO_PERFIL/'
		+ $("#SG_UF_ATUACAO_PERFIL").val(),

		data : {
			SG_UF_ATUACAO_PERFIL: $("#SG_UF_ATUACAO_PERFIL").val(),
		},

		success : function(data) {
			var find = $(data).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

			$("#CO_MESORREGIAO option").remove();
			var labelHTML = $(data).find("#CO_MESORREGIAO option");
			$("#CO_MESORREGIAO").append(labelHTML);

			$("#CO_MESORREGIAO").val($(data).find("#CO_MESORREGIAO").val());

			$("#CO_MUNICIPIO_PERFIL option").remove();
			var labelHTMLMunicipio = $(data).find("#CO_MUNICIPIO_PERFIL option");
			$("#CO_MUNICIPIO_PERFIL").append(labelHTMLMunicipio);

			$("#CO_MUNICIPIO_PERFIL").val($(data).find("#CO_MUNICIPIO_PERFIL").val());

			//Termina o efeito de loading.
			popUp.carregando();
		}
	});
};

var changeMesorregiao = function() {

	$.ajax({
		url : baseUrl
		+ '/index.php/manutencao/usuario/renderiza-municipio/CO_MESORREGIAO/'
		+ $("#CO_MESORREGIAO").val()
		+ '/SG_UF_ATUACAO_PERFIL/'
		+ $("#SG_UF_ATUACAO_PERFIL").val(),

		data : {
			CO_MESORREGIAO: $("#CO_MESORREGIAO").val(),
			SG_UF_ATUACAO_PERFIL: $("#SG_UF_ATUACAO_PERFIL").val(),
		},

		success : function(data) {
			var find = $(data).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

			$("#CO_MUNICIPIO_PERFIL option").remove();
			var labelHTML = $(data).find("#CO_MUNICIPIO_PERFIL option");
			$("#CO_MUNICIPIO_PERFIL").append(labelHTML);

			$("#CO_MUNICIPIO_PERFIL").val($(data).find("#CO_MUNICIPIO_PERFIL").val());
		}
	});

};

var changeMunicipioPerfil = function() {
	$.ajax({
		type : "POST",
		url : baseUrl + '/index.php/manutencao/usuario/municipio-change/',
		data : {
			CO_MUNICIPIO_PERFIL : $("#CO_MUNICIPIO_PERFIL").val(),
		},

		success : function(html) {
			if (html != '') {
				$("#CO_MESORREGIAO").val(html);
			}
		}
	});
};

$("#SG_UF_ATUACAO_PERFIL").change(changeUfAtuacaoPerfil);
$("#CO_MESORREGIAO").change(changeMesorregiao);
$("#CO_MUNICIPIO_PERFIL").change(changeMunicipioPerfil);
// ABA FORMAÇÃO

var READONLY = '';

// se conter a class readonly
if($("#abaFormacaoAcademica").hasClass('readonly')){
	READONLY = 'READONLY/1';
}

$("#htmlDivFormacaoAcademica").html("Carregando...");


function atualizaRemover() {
	$("#htmlDivFormacaoAcademica .icoExcluir").click(function() {

		if($(this).hasClass('disabled'))
			return false;

		var varUrl = this.href;

		varUrl += "?" + new Date().getTime();

		Dialog.confirm($(this).attr('mensagem'),'Confirmação', function(r){
			if(r == true){
				$.ajax({
					url : varUrl,
					success : function(data) {
						var find = $(data).find('#loginBase #login').html();
						if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

						$("#htmlDivFormacaoAcademica").html(data);
						atualizaRemover();
						$.alerts._hide();
					}
				});
			}
		});
		return false;
	});
}

function adicionaFormacao(TP_ESCOLARIDADE, DS_TP_ESCOLARIDADE, TP_INSTITUICAO,
		DS_TP_INSTITUICAO, NO_INSTITUICAO, NO_CURSO, DT_CONCLUSAO) {

	var URL = baseUrl+"/index.php/manutencao/formacaoacademica/add-formacao"
			+ "/TP_ESCOLARIDADE/"
			+ escape(TP_ESCOLARIDADE)
			+ "/DS_TP_ESCOLARIDADE/"
			+ escape(DS_TP_ESCOLARIDADE)
			+ "/TP_INSTITUICAO/"
			+ escape(TP_INSTITUICAO)
			+ "/DS_TP_INSTITUICAO/"
			+ escape(DS_TP_INSTITUICAO)
			+ "/NO_INSTITUICAO/"
			+ escape(NO_INSTITUICAO)
			+ "/NO_CURSO/"
			+ escape(NO_CURSO)
			+ "/DT_CONCLUSAO/"
			+ escape(DT_CONCLUSAO.replace('/', '-').replace('/', '-'));

	$.ajax({
		url : URL,
		success : function(data) {
			var find = $(data).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

			$("#htmlDivFormacaoAcademica").html(data);
			atualizaRemover();
			$.alerts._hide();
		}
	});
}

// aba logradouro
var focusoutCEP = function() {
	if ($(this).val() != '') {
		$
				.ajax({
					url : baseUrl
							+ '/index.php/manutencao/usuario/obter-informacoes-por-cep/NU_CEP/'
							+ $("#NU_CEP").val() + '/TP_ENDERECO/'
							+ $("#TP_ENDERECO").val(),
					success : function(data) {
						var find = $(data).find('#loginBase #login').html();
						if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

						var pai = $("label[for=TP_ENDERECO]");
						$("label[for=NU_CEP]").remove();
			        	var labelHTML = $(data).find("label[for=NU_CEP]");
			        	pai.after(labelHTML);

						var endLabel = $("label[for=DS_ENDERECO]");
			        	var val = $(data).find("input[name=DS_ENDERECO]").val();
			        	$(endLabel).find('input').val(val);

			        	var baiLabel = $("label[for=DS_BAIRRO_ENDERECO]");
			        	var val = $(data).find("input[name=DS_BAIRRO_ENDERECO]").val();
			        	$(baiLabel).find('input').val(val);

			        	//remove e adiciona o combo de uf com o valor vindo do ajax
			        	var ufLabel = $("label[for=CO_UF_ENDERECO]");
			        	$(ufLabel).find('select').remove();
			        	var ufLabelHTML = $(data).find("select[name=CO_UF_ENDERECO]");
			        	ufLabel.append(ufLabelHTML);
			        	//se tiver mensagem de erro adiciona o combo antes da mensagem e deixa o campo vermelho
			        	var obj = $("#CO_UF_ENDERECO");
			        	var pai = obj.parent();

			        	if(pai.find('.msgErro').html() != null){
			        		$(obj).addClass("campoErro");
			        	}

			        	obj.remove();
			        	pai.find('.campoRequerido').after(obj);

			        	//remove e adiciona o combo de municipio com o valor vindo do ajax
			        	var munLabel = $("label[for=CO_MUNICIPIO_ENDERECO]");
			        	$(munLabel).find('select').remove();
			        	var munLabelHTML = $(data).find("select[name=CO_MUNICIPIO_ENDERECO]");
			        	munLabel.append(munLabelHTML);
			        	//se tiver mensagem de erro adiciona o combo antes da mensagem e deixa o campo vermelho
			        	var obj = $("#CO_MUNICIPIO_ENDERECO");
			        	var pai = obj.parent();
			        	if(pai.find('.msgErro').html() != null){
			        		$(obj).addClass("campoErro");
			        	}
			        	obj.remove();
			        	pai.find('.campoRequerido').after(obj);


						$("#CO_UF_ENDERECO").change(ufChangeLog);

						initilizeMasks();

						$('#NU_CEP').bind('copy paste cut drop', function(event) {
					        event.preventDefault();
					        this.value = '';
					    });

						$('#DS_EMAIL_USUARIO_CONFIRM').bind('copy paste cut drop', function(event) {
					        event.preventDefault();
					        this.value = '';
					    });

						$("#NU_CEP").focusout(focusoutCEP);
					}
				});
	}
};

$("#NU_CEP").focusout(focusoutCEP);
$("#CO_UF_ENDERECO").change(ufChangeLog);

function ufChangeLog() {
    $.ajax({
        type : "POST",
        url : baseUrl
                + '/index.php/manutencao/usuario/renderiza-municipio-cad/CO_UF_ENDERECO/'
                + $("#CO_UF_ENDERECO").val()
                + '/SG_UF_ATUACAO_PERFIL_CAD/'
                + $("#SG_UF_ATUACAO_PERFIL_CAD").val(),
        success : function(html) {
        	var find = $(html).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

			var endLabel = $("label[for=DS_ENDERECO]");
        	var val = $(html).find("input[name=DS_ENDERECO]").val();
        	$(endLabel).find('input').val(val);

        	var baiLabel = $("label[for=DS_BAIRRO_ENDERECO]");
        	var val = $(html).find("input[name=DS_BAIRRO_ENDERECO]").val();
        	$(baiLabel).find('input').val(val);


        	//remove e adiciona o combo de municipio com o valor vindo do ajax
        	var munLabel = $("label[for=CO_MUNICIPIO_ENDERECO]");
        	$(munLabel).find('select').remove();
        	var munLabelHTML = $(html).find("select[name=CO_MUNICIPIO_ENDERECO]");
        	munLabel.append(munLabelHTML);
        	//se tiver mensagem de erro adiciona o combo antes da mensagem e deixa o campo vermelho
        	var obj = $("#CO_MUNICIPIO_ENDERECO");
        	var pai = obj.parent();
        	if(pai.find('.msgErro').html() != null){
        		$(obj).addClass("campoErro");
        	}
        	obj.remove();
        	pai.find('.campoRequerido').after(obj);



        	$('#NU_CEP').val('');

            initilizeMasks();
        }
    });
};

// Aba Dados Funcionais
function campoQualOcupacao(limpar){
	 if($("#CO_OCUPACAO_USUARIO").val() == "2" || $("#CO_OCUPACAO_USUARIO").val() == "4" || $("#CO_OCUPACAO_USUARIO").val() == "7" ){
		$("label[for=CO_LOCAL_LOTACAO]").show();
		$("#CO_LOCAL_LOTACAO").show();
		if(limpar){
			$("#DS_OCUPACAO_ALTERNATIVA").val("");
		}
		$("label[for=DS_OCUPACAO_ALTERNATIVA]").css('display','none');
	} else if($("#CO_OCUPACAO_USUARIO").val() == "18"){
		if(limpar){
			$("#DS_OCUPACAO_ALTERNATIVA").val("");
		}
		$("label[for=DS_OCUPACAO_ALTERNATIVA]").css('display','inline');

		$("#DS_LOCAL_LOTACAO_ALTERNATIVA").hide();
		$("label[for=DS_LOCAL_LOTACAO_ALTERNATIVA]").hide();

		$("#CO_LOCAL_LOTACAO").hide();
		$("label[for=CO_LOCAL_LOTACAO]").hide();

	}else{
		if(limpar){
			$("#DS_OCUPACAO_ALTERNATIVA").val("");
		}
		$("label[for=DS_OCUPACAO_ALTERNATIVA]").hide();
		$("label[for=CO_LOCAL_LOTACAO]").hide();
		$("#CO_LOCAL_LOTACAO").hide();
		$("label[for=DS_LOCAL_LOTACAO_ALTERNATIVA]").hide();
	}
}

$("#CO_OCUPACAO_USUARIO").change(function() {
	campoQualOcupacao(1);
});

function campoQualLugar(limpar){
	if ($("#CO_LOCAL_LOTACAO").val() == "8") {
		if(limpar){
			$("#DS_LOCAL_LOTACAO_ALTERNATIVA").val("");
		}
		$("#DS_LOCAL_LOTACAO_ALTERNATIVA").show();
		$("label[for=DS_LOCAL_LOTACAO_ALTERNATIVA]").css('display','inline');
	} else {
		if(limpar){
			$("#DS_LOCAL_LOTACAO_ALTERNATIVA").val("");
		}
		$("#DS_LOCAL_LOTACAO_ALTERNATIVA").hide();
		$("label[for=DS_LOCAL_LOTACAO_ALTERNATIVA]").hide();
	}
}




$("#CO_LOCAL_LOTACAO").change(function() {
	campoQualLugar(1);
});

var htmlAuxiliar = '';

// ABA OUTRAS INFORMAÇÕES
if ($("#htmlDivOutrasInformacoes") != null) {

	function vinculaUsuarioAjax() {
		$
				.ajax({
					url : baseUrl+"/index.php/manutencao/vinculaativusuario/form/",
					success : function(data) {
						var find = $(data).find('#loginBase #login').html();
						if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

						var htmlPrincipal = '';
						htmlAuxiliar = '';


						$(data).find('form').each(function() {
							if (this.id == 'principal') {
								htmlPrincipal += $(this).html();
							} else {
								htmlAuxiliar += $(this).html();
							}
						});

						$("#htmlDivOutrasInformacoes").html(htmlPrincipal);
						$(document).ready(function() {
							configuraAtividades();
						});
					}
				});
	}
	$("#htmlDivOutrasInformacoes").html("Carregando...");
}


$(document).ready(function() {
    $('#DS_EMAIL_USUARIO_CONFIRM').bind('copy paste cut drop', function(event) {
        event.preventDefault();
        this.value = '';
    });


    $.ajax({
            url : baseUrl+"/index.php/manutencao/formacaoacademica/list/" + READONLY,
            success : function(data) {
                    var find = $(data).find('#loginBase #login').html();
                    if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

                    $("#htmlDivFormacaoAcademica").html(data);
                    atualizaRemover();
                    vinculaUsuarioAjax();
            }
    });


    $('#NU_CEP').bind('copy paste cut drop', function(event) {
        event.preventDefault();
        this.value = '';
    });


    $("label[for=CO_OCUPACAO_USUARIO]").css('display','inline');
    $("label[for=DS_OCUPACAO_ALTERNATIVA]").css('display','inline');


    $("label[for=CO_LOCAL_LOTACAO]").css('display','inline');
    $("label[for=DS_LOCAL_LOTACAO_ALTERNATIVA]").css('display','inline');
    campoQualOcupacao(0);
    campoQualLugar(0);

    $('#DS_TELEFONE_USUARIO').setMask('phone');

	if($('#DS_CELULAR_USUARIO').val().length < 11){
	    $('#DS_CELULAR_USUARIO').setMask('(99) 9999-99999');
	}else{
	    $('#DS_CELULAR_USUARIO').setMask('(99) 99999-9999');
	}
	$('#DS_CELULAR_USUARIO').bind('focusout', function(){
		console.log($(this).val().length);
		if($(this).val().length > 14){
			$('#DS_CELULAR_USUARIO').setMask('(99) 99999-9999');
		}else{
			$('#DS_CELULAR_USUARIO').setMask('(99) 9999-99999');
		}
	});
});


function initilizeMasks() {

	$("input.cpf").setMask("cpf");
	$("input.cnpj").setMask("cnpj");
	$("input.fone").setMask("phone");
	$("input.phone").setMask("phone");
	$("input.cep").setMask("cep");
	$("input.date").datePicker({
		startDate : "01/01/1970"
	}).setMask({
		mask : "39/19/2999",
		autoTab : false
	});
	$("input.decimal").setMask("decimal");
	$("input.signed-decimal").setMask("signed-decimal");
	$("input.decimal6").setMask({
		mask : "99,999",
		type : "reverse",
		defaultValue : "000"
	});
	$("input.decimal10").setMask({
		mask : "99,999.999",
		type : "reverse",
		defaultValue : "000"
	});
	$(".signed-decimal").keyup(
			function() {
				($(".signed-decimal").val().indexOf("-") != -1) ? $(
						".signed-decimal").addClass("formatValorNegativo") : $(
						".signed-decimal").removeClass("formatValorNegativo")
			});
	if ($(".signed-decimal").val()) {
		($(".signed-decimal").val().indexOf("-") != -1) ? $(".signed-decimal")
				.addClass("formatValorNegativo") : $(".signed-decimal")
				.removeClass("formatValorNegativo")
	}
	$("input.time").setMask({
		mask : "29:59:59",
		autoTab : false,
		setSize : true
	});
	$(".date").bind("change", function(d) {
		if (d.keyCode == 13) {
			return false
		}
		if ($(this).val() != "") {
			if (!Helper.Date.validate($(this).val())) {
				$this = $(this);
				$(this).val("");
				Dialog.alert("Data inválida", "Atenção", function() {
					$this.focus()
				})
			}
		}
		return false
	}).bind("click", function() {
		$(this).trigger("change")
	}).bind("blur", function() {
		$(this).trigger("change")
	}).bind("keydown", function(d) {
		return !(d.keyCode == 13)
	});
	$(".pickList").pickList();
	$("textarea[maxlength]").not(".editorHtml").each(function() {
		Helper.displayLength($(this), $(this).attr("maxlength"))
	});
}
