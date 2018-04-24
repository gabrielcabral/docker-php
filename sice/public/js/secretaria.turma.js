// TELA DE FILTRO

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
			if ($('input[name=NU_SEQ_TURMA]:checked').val() == null) {
				Dialog.error('Nenhuma turma selecionada.', null, null);
			} else {
				window.location.href = $('#table_main_action').val()
						+ "/NU_SEQ_TURMA/"
						+ $('input[name=NU_SEQ_TURMA]:checked').val();
			}
		});
// Fim combo mais ações

var ufChange = function() {

	popUp.carregando();

	$.ajax({
		type : "POST",
		url : baseUrl
				+ '/index.php/secretaria/turma/renderiza-mesoregiao/UF_TURMA/'
				+ $("#UF_TURMA").val(),

		data : {
			UF_TURMA : $("#UF_TURMA").val(),
		},

		success : function(html) {
			var find = $(html).find('#loginBase #login').html();
			if (find != null && find != '') {
				location.href = baseUrl+'/index.php';
				return;
			}

			$("#CO_MESORREGIAO option").remove();
			var labelHTML = $(html).find("#CO_MESORREGIAO option");
			$("#CO_MESORREGIAO").append(labelHTML);
			$("#CO_MESORREGIAO").val('');

			$("#CO_MUNICIPIO option").remove();
			var labelHTML = $(html).find("#CO_MUNICIPIO option");
			$("#CO_MUNICIPIO").append(labelHTML);
			$("#CO_MUNICIPIO").val('');

            $("#NU_SEQ_USUARIO_ARTICULADOR option").remove();
            var labelHTML = $(html).find("#NU_SEQ_USUARIO_ARTICULADOR option");
            $("#NU_SEQ_USUARIO_ARTICULADOR").append(labelHTML);

			popUp.carregando();
		}
	});
};

var mesorregiaoChange = function() {
	popUp.carregando();

	$.ajax({
		type : "POST",
		url : baseUrl
				+ '/index.php/secretaria/turma/renderiza-municipio/CO_MESORREGIAO/'+ $("#CO_MESORREGIAO").val() + '/UF_TURMA/'+ $('#UF_TURMA').val(),

		data : {
			CO_MESORREGIAO : $("#CO_MESORREGIAO").val(),
		},

		success : function(html) {
			var find = $(html).find('#loginBase #login').html();
			if (find != null && find != '') {
				location.href = baseUrl+'/index.php';
				return;
			}

			$("#CO_MUNICIPIO option").remove();
			var labelHTML = $(html).find("#CO_MUNICIPIO option");
			$("#CO_MUNICIPIO").append(labelHTML);
			$("#CO_MUNICIPIO").val('');

			popUp.carregando();
		}

	});
};

var municipioChange = function() {
	popUp.carregando();

	$.ajax({
		type : "POST",
		url : baseUrl
				+ '/index.php/secretaria/turma/municipio-change/',
		data : {
			CO_MUNICIPIO : $("#CO_MUNICIPIO").val(),
			UF_TURMA : $("#UF_TURMA").val(),
		},
		success : function(html) {
			var find = $(html).find('#loginBase #login').html();
			if (find != null && find != '') {
				location.href = baseUrl+'/index.php';
				return;
			}
			$("#CO_MESORREGIAO").val($(html).find("#CO_MESORREGIAO").val());

			$("#NU_SEQ_USUARIO_TUTOR option").remove();
			var labelHTML = $(html).find("#NU_SEQ_USUARIO_TUTOR option");
			$("#NU_SEQ_USUARIO_TUTOR").append(labelHTML);

			$("#NU_SEQ_USUARIO_ARTICULADOR option").remove();
			var labelHTML = $(html).find("#NU_SEQ_USUARIO_ARTICULADOR option");
			$("#NU_SEQ_USUARIO_ARTICULADOR").append(labelHTML);

			popUp.carregando();
		}
	});
};

$("#UF_TURMA").change(ufChange);
$("#CO_MESORREGIAO").change(mesorregiaoChange);
$("#CO_MUNICIPIO").change(municipioChange);

$(".icoExcluir").click(function() {
	if ($(this).attr('mensagem') != '') {
		var link = this;
		Dialog.confirm($(this).attr('mensagem'), 'Confirmação', function(r) {
			if (r == true) {
				location.href = link.href;
			}
		});
		return false;
	}
});

$(".confInativo").live('click',function() {
    popUp.carregando();
    $.ajax({
        type : "GET",
        url : baseUrl
        + '/index.php/secretaria/turma/get-configuracao/NU_SEQ_TURMA/'
        + $(this).attr('id'),
        success : function(html) {
            popUp.carregando();
			oFnde.dialog.alert(html, null);
		}
    });

    return false;

});

// FIM TELA DE FILTRO

// TELA DE CADASTRO
// Função para pesquisa ajax dos municípios por UF
var ufCadChange = function() {
	popUp.carregando();

	$.ajax({
		type : "POST",
		url : baseUrl + '/index.php/secretaria/turma/renderiza-municipio-cad/',
		data : {
			UF_TURMA_CAD : $("#UF_TURMA_CAD").val(),
		},
		success : function(html) {
			var find = $(html).find('#loginBase #login').html();
			if (find != null && find != '') {
				location.href = baseUrl+'/index.php';
				return;
			}

			$("#CO_MUNICIPIO_CAD option").remove();
			var labelHTML = $(html).find("#CO_MUNICIPIO_CAD option");
			$("#CO_MUNICIPIO_CAD").append(labelHTML);

			$('#CO_MUNICIPIO_CAD').val("");
			$('#NO_MESORREGIAO_CAD').val("");

            $("#NU_SEQ_USUARIO_ARTICULADOR option").remove();
            var labelHTML = $(html).find("#NU_SEQ_USUARIO_ARTICULADOR option");
            $("#NU_SEQ_USUARIO_ARTICULADOR").append(labelHTML);

			popUp.carregando();
		}
	});
};

// Função para pesquisa ajax da mesorregião por município
var municipioCadChange = function() {
	popUp.carregando();

	$.ajax({
		type : "POST",
		url : baseUrl
				+ '/index.php/secretaria/turma/municipio-change-cad/',

		data : {
			CO_MUNICIPIO_CAD : $("#CO_MUNICIPIO_CAD").val(),
		},
		success : function(html) {
			var find = $(html).find('#loginBase #login').html();
			if (find != null && find != '') {
				location.href = baseUrl+'/index.php';
				return;
			}

			$('#NO_MESORREGIAO_CAD').val(html['NO_MESORREGIAO']);
			$('#CO_MESORREGIAO_CAD').val(html['CO_MESORREGIAO']);

			popUp.carregando();
		}
	});
};

// Função para pesquisa ajax das informações de curso por curso selecionado
var cursoCadChange = function() {
	popUp.carregando();

	$.ajax({
		type : "POST",
		url : baseUrl
				+ '/index.php/secretaria/turma/renderiza-info-curso-cad/NU_SEQ_CURSO_CAD/'
				+ $("#NU_SEQ_CURSO_CAD").val() + "/NU_SEQ_TIPO_CURSO_CAD/"
				+ $("#NU_SEQ_TIPO_CURSO_CAD").val() + "/NU_SEQ_CONFIGURACAO/" + $("#NU_SEQ_CONFIGURACAO").val(),
		data : {
			NU_SEQ_CURSO_CAD : $("#NU_SEQ_CURSO_CAD").val(),
			NU_SEQ_TIPO_CURSO_CAD : $("#NU_SEQ_TIPO_CURSO_CAD").val(),
			NU_SEQ_CONFIGURACAO : $("#NU_SEQ_CONFIGURACAO").val(),
		},
		success : function(html) {
			var find = $(html).find('#loginBase #login').html();
			if (find != null && find != '') {
				location.href = baseUrl+'/index.php';
				return;
			}

			var minAluLabel = $("label[for=NU_MIN_ALUNOS]");
			var val = $(html).find("input[name=NU_MIN_ALUNOS]").val();
			$(minAluLabel).find('input').val(val);

			var cargaPresLabel = $("label[for=NU_CARGA_PRESENCIAL]");
			var val = $(html).find("input[name=NU_CARGA_PRESENCIAL]")
					.val();
			$(cargaPresLabel).find('input').val(val);

			var cargaDistLabel = $("label[for=NU_CARGA_DISTANCIA]");
			var val = $(html).find("input[name=NU_CARGA_DISTANCIA]")
					.val();
			$(cargaDistLabel).find('input').val(val);

			var cargaCursoLabel = $("label[for=NU_CARGA_CURSO]");
			var val = $(html).find("input[name=NU_CARGA_CURSO]").val();
			$(cargaCursoLabel).find('input').val(val);

			var minConcLabel = $("label[for=NU_MIN_CONCLUSAO]");
			var val = $(html).find("input[name=NU_MIN_CONCLUSAO]")
					.val();
			$(minConcLabel).find('input').val(val);

			var maxConcLabel = $("label[for=NU_MAX_CONCLUSAO]");
			var val = $(html).find("input[name=NU_MAX_CONCLUSAO]")
					.val();
			$(maxConcLabel).find('input').val(val);

			popUp.carregando();
		}
	});
};

// Função para pesquisa ajax dos cursos por tipo
var tipoCursoCadChange = function() {
	popUp.carregando();

	$.ajax({
		type : "POST",
		url : baseUrl
				+ '/index.php/secretaria/turma/renderiza-curso-por-tipo-cad/NU_SEQ_TIPO_CURSO_CAD/'
				+ $("#NU_SEQ_TIPO_CURSO_CAD").val(),

		data : {
			NU_SEQ_TIPO_CURSO_CAD : $("#NU_SEQ_TIPO_CURSO_CAD").val(),
		},
		success : function(html) {
			var find = $(html).find('#loginBase #login').html();
			if (find != null && find != '') {
				location.href = baseUrl+'/index.php';
				return;
			}

			$("#NU_SEQ_CURSO_CAD option").remove();
			var labelHTML = $(html).find("#NU_SEQ_CURSO_CAD option");
			$("#NU_SEQ_CURSO_CAD").append(labelHTML);

			$("#NU_MIN_ALUNOS").val("");
			$("#NU_CARGA_PRESENCIAL").val("");
			$("#NU_CARGA_DISTANCIA").val("");
			$("#NU_CARGA_CURSO").val("");
			$("#NU_MIN_CONCLUSAO").val("");
			$("#NU_MAX_CONCLUSAO").val("");

			popUp.carregando();
		}
	});
};

//Função para pesquisa ajax do articulador por mesorregiao do tutor selecionado
var tutorCadChange = function() {
	popUp.carregando();

	$.ajax({
		type : "POST",
		url : baseUrl
				+ '/index.php/secretaria/turma/renderiza-articulador-cad/NU_SEQ_USUARIO_TUTOR/'
				+ $("#NU_SEQ_USUARIO_TUTOR").val() + "/RENDERIZA_ARTICULADOR/1",

		data : {
			NU_SEQ_USUARIO_TUTOR : $("#NU_SEQ_USUARIO_TUTOR").val(),
		},
		success : function(html) {
			var find = $(html).find('#loginBase #login').html();
			if (find != null && find != '') {
				location.href = baseUrl+'/index.php';
				return;
			}

			$("#NU_SEQ_USUARIO_ARTICULADOR option").remove();
			var labelHTML = $(html).find("#NU_SEQ_USUARIO_ARTICULADOR option");
			$("#NU_SEQ_USUARIO_ARTICULADOR").append(labelHTML);

			$("#NU_SEQ_USUARIO_ARTICULADOR").val($(html).find("#NU_SEQ_USUARIO_ARTICULADOR").val());

			popUp.carregando();
		}
	});
};


$("#NU_SEQ_TIPO_CURSO_CAD").change(tipoCursoCadChange);
$("#NU_SEQ_CURSO_CAD").change(cursoCadChange);
$("#UF_TURMA_CAD").change(ufCadChange);
$('#NU_SEQ_USUARIO_TUTOR').change(tutorCadChange);
$('#CO_MUNICIPIO_CAD').change(municipioCadChange);

// FIM DA TELA DE CADASTRO

//Código para alteração SM03 - Desabilitar edição para alguns status de turma
var configurarIcones = function(){
    $('#edit tr').each(function(){
        if($(this).children('td').length > 0){
             if(($($(this).children('td')[10]).html().toLowerCase() != 'Pré-turma'.toLowerCase())
				 && ($($(this).children('td')[10]).html().toLowerCase() != 'Em avaliação'.toLowerCase())
				 && ($($(this).children('td')[10]).html().toLowerCase() != 'Aguardando Autorização'.toLowerCase())
                     && ($($(this).children('td')[10]).html().toLowerCase() != 'Ativa'.toLowerCase())){
				 $(this).find('.icoAvaliar').attr('class','icoAvaliar disabled');
				 $(this).find('.icoAvaliar').attr("href", "#a");
            }

			if(
				$($(this).children('td')[10]).html().toLowerCase() == 'Cancelada'.toLowerCase()
				|| $($(this).children('td')[10]).html().toLowerCase() == 'Finalização Atrasada'.toLowerCase()
				|| $($(this).children('td')[10]).html().toLowerCase() == 'Finalizada'.toLowerCase()
			) {
				$(this).find('.icoEditar').attr('class', 'icoEditar disabled');
				$(this).find('.icoEditar').attr("href", "#a");
			}

            var substring = "inativa";
			if( $($(this).children('td')[11]).html().toLowerCase().indexOf(substring) !== -1
				&& $($(this).children('td')[10]).html().toLowerCase() != 'Finalizada'.toLowerCase()
				&& $($(this).children('td')[10]).html().toLowerCase() != 'Cancelada'.toLowerCase()
			){

                var href = $(this).find('.icoEditar').attr('href');
                var id_conf = href.split('/').pop();

                if(href){
                    $(this).find('.icoEditar').attr('id', id_conf);
				}

                $(this).find('.icoEditar').addClass('confInativo');

                $(this).find('.icoEditar').attr('href', '');
			}
        }
    });
}

configurarIcones();

$(".paginate_button").click(configurarIcones());