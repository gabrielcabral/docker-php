// TELA DE FILTRO

//mascaras
$("label[for=DT_FINALIZACAO]").hide();
$("#DT_FINALIZACAO").hide();


//Combo mais ações
$("#table_main_action").change(function(){
	if($('#table_main_action').val() == null || $('#table_main_action').val() == ''){
		$("#table_button").attr("disabled","disabled");
	}else{
		$("#table_button").removeAttr("disabled");	
	}
});

$("#table_button").click(function(){
	if($('input[name=NU_SEQ_TURMA]:checked').val() ==  null){
		Dialog.error('Nenhuma turma selecionada.', null, null);
	}else{
		window.location.href = $('#table_main_action').val()  + "/NU_SEQ_TURMA/" + $('input[name=NU_SEQ_TURMA]:checked').val();	
	}
});
//Fim combo mais ações

var ufChange = function() {
	//Efeito de loading.
	popUp.carregando();
	
	$.ajax({
        type: "POST",
        url: baseUrl + '/index.php/secretaria/avaliacaopedagogica/renderiza-mesoregiao/',
        
        data : {
        	UF_TURMA: $("#UF_TURMA").val(),
		},
        
        success: function(data) {
        	$('#CO_MESORREGIAO option').remove();
        	$('#CO_MESORREGIAO').append('<option value="">Selecione</option>');
        	options = data['MESORREGIAO'];
        	for (var i=0; i < options.length; i++) {
        		$('#CO_MESORREGIAO').append('<option value="' + options[i][0] + '">' + options[i][1] + '</option>');
			}
        	
        	$('#CO_MUNICIPIO option').remove();
        	$('#CO_MUNICIPIO').append('<option value="">Selecione</option>');
        	options = data['MUNICIPIO'];
        	for (var i=0; i < options.length; i++) {
        		$('#CO_MUNICIPIO').append('<option value="' + options[i][0] + '">' + options[i][1] + '</option>');
			}
        	
        	//Limpando combos Tutor e Articulador.
        	$(limpaUsuarios);
        	
        	//Termina o efeito de loading.
			popUp.carregando();
        }
    });
};

var mesorregiaoChange = function() {
	//Efeito de loading.
	popUp.carregando();
	
	$.ajax({
        type: "POST",
        url: baseUrl + '/index.php/secretaria/avaliacaopedagogica/renderiza-municipio/',
        
        data : {
        	CO_MESORREGIAO: $("#CO_MESORREGIAO").val(),
        	UF_TURMA: $("#UF_TURMA").val(),
		},
        
        success: function(data) {
        	$('#CO_MUNICIPIO option').remove();
        	$('#CO_MUNICIPIO').append('<option value="">Selecione</option>');
        	options = data['MUNICIPIO'];
        	for (var i=0; i < options.length; i++) {
        		$('#CO_MUNICIPIO').append('<option value="' + options[i][0] + '">' + options[i][1] + '</option>');
			}
        	
        	//Limpando combos Tutor e Articulador.
        	$(limpaUsuarios);
        	
        	//Termina o efeito de loading.
			popUp.carregando();
        }
    });
};

var municipioChange = function(){
	//Efeito de loading.
	popUp.carregando();
	
	$.ajax({
        type: "POST",
        url: baseUrl + '/index.php/secretaria/avaliacaopedagogica/municipio-change/',
        
        data : {
        	CO_MUNICIPIO: $("#CO_MUNICIPIO").val(),
        	UF_TURMA: $("#UF_TURMA").val(),
		},
        success: function(data) {
        	$('#CO_MESORREGIAO').val(data['MESORREGIAO_VAL']);
        	
        	$('#NU_SEQ_USUARIO_TUTOR option').remove();
        	$('#NU_SEQ_USUARIO_TUTOR').append('<option value="">Selecione</option>');
        	options = data['TUTOR'];
        	if (options) {
	        	for (var i=0; i < options.length; i++) {
	        		$('#NU_SEQ_USUARIO_TUTOR').append('<option value="' + options[i][0] + '">' + options[i][1] + '</option>');
				}
        	}
        	
        	$('#NU_SEQ_USUARIO_ARTICULADOR option').remove();
        	$('#NU_SEQ_USUARIO_ARTICULADOR').append('<option value="">Selecione</option>');
        	options = data['ARTICULADOR'];
        	if (options) {
	        	for (var i=0; i < options.length; i++) {
	        		$('#NU_SEQ_USUARIO_ARTICULADOR').append('<option value="' + options[i][0] + '">' + options[i][1] + '</option>');
				}
        	}
        	
        	//Termina o efeito de loading.
			popUp.carregando();
        }
    });
};

var limpaUsuarios = function() {
	//Limpando combo tutor.
	$('#NU_SEQ_USUARIO_TUTOR option').remove();
	$('#NU_SEQ_USUARIO_TUTOR').append('<option value="">Selecione</option>');
	
	//Limpando combo articulador.
	$('#NU_SEQ_USUARIO_ARTICULADOR option').remove();
	$('#NU_SEQ_USUARIO_ARTICULADOR').append('<option value="">Selecione</option>');
};

$("#UF_TURMA").change(ufChange);
$("#CO_MESORREGIAO").change(mesorregiaoChange);
$("#CO_MUNICIPIO").change(municipioChange);
// FIM TELA DE FILTRO

// TELA DE CADASTRO

//Função para exibição das labels aaixo de alguns dos campos da tela
var exibicaoLabels = function(){
	$("#NU_MIN_ALUNOS").after("<br><span class='msgOrientacao'>Em horas</span>");
	$("#NU_CARGA_PRESENCIAL").after("<br><span class='msgOrientacao'>Em horas</span>");
	$("#NU_CARGA_DISTANCIA").after("<br><span class='msgOrientacao'>Em horas</span>");
	$("#NU_CARGA_CURSO").after("<br><span class='msgOrientacao'>Em horas</span>");
	$("#NU_MIN_CONCLUSAO").after("<br><span class='msgOrientacao'>Em dias</span>");
	$("#NU_MAX_CONCLUSAO").after("<br><span class='msgOrientacao'>Em dias</span>");
};

//Função para pesquisa ajax dos municípios por UF
var ufCadChange = function() {
	//Efeito de loading.
	popUp.carregando();
	
	$.ajax({
        type: "POST",
        url: baseUrl + '/index.php/secretaria/avaliacaopedagogica/renderiza-municipio-cad/UF_TURMA_CAD/' + $("#UF_TURMA_CAD").val() ,
        
        data : {
        	UF_TURMA_CAD: $("#UF_TURMA_CAD").val(),
		},
        success: function(html){
        	var find = $(html).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
        	
        	var label = $("label[for=CO_MUNICIPIO_CAD]");
        	var labelHTML = $(html).find("label[for=CO_MUNICIPIO_CAD]");
        	label.html(labelHTML.html());
        	$("#NU_SEQ_TIPO_CURSO_CAD").change(tipoCursoCadChange);
        	$("#UF_TURMA_CAD").change(ufCadChange);
        	$("#NU_SEQ_CURSO_CAD").change(cursoCadChange);
        	$("#CO_MUNICIPIO_CAD").change(municipioCadChange);
        	
        	//Termina o efeito de loading.
			popUp.carregando();
        }
    });
};

//Função para pesquisa ajax da mesorregião por município
var municipioCadChange = function(){
	//Efeito de loading.
	popUp.carregando();
	
	$.ajax({
        type: "POST",
        url: baseUrl + '/index.php/secretaria/avaliacaopedagogica/renderiza-mesorregiao-cad/CO_MUNICIPIO_CAD/' + $("#CO_MUNICIPIO_CAD").val() ,
        
        data : {
        	CO_MUNICIPIO_CAD: $("#CO_MUNICIPIO_CAD").val(),
		},
        success: function(html){
        	var find = $(html).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
        	
        	var label = $("label[for=NO_MESORREGIAO_CAD]");
        	var labelHTML = $(html).find("label[for=NO_MESORREGIAO_CAD]");
        	label.html(labelHTML.html());
        	$("#CO_MESORREGIAO_CAD").val($(html).find("#CO_MESORREGIAO_CAD").val());
        	$("#NU_SEQ_TIPO_CURSO_CAD").change(tipoCursoCadChange);
        	$("#UF_TURMA_CAD").change(ufCadChange);
        	$("#NU_SEQ_CURSO_CAD").change(cursoCadChange);
        	$("#CO_MUNICIPIO_CAD").change(municipioCadChange);
        	
        	//Termina o efeito de loading.
			popUp.carregando();
        }
    });
};

//Função para pesquisa ajax das informações de curso por curso selecionado
var cursoCadChange = function(){
	//Efeito de loading.
	popUp.carregando();
	
	$.ajax({
        type: "POST",
        url: baseUrl + '/index.php/secretaria/avaliacaopedagogica/renderiza-info-curso-cad/NU_SEQ_CURSO/' + $("#NU_SEQ_CURSO_CAD").val() + "/NU_SEQ_TIPO_CURSO/" + $("#NU_SEQ_TIPO_CURSO_CAD") ,
        data : {
        	NU_SEQ_CURSO_CAD: $("#NU_SEQ_CURSO_CAD").val(),
        	NU_SEQ_TIPO_CURSO_CAD: $("#NU_SEQ_TIPO_CURSO_CAD").val(),
		},
        success: function(html){
        	var find = $(html).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
        	
        	var label = $("label[for=NU_SEQ_CURSO]");
        	var labelHTML = $(html).find("label[for=NU_SEQ_CURSO]");
        	label.html(labelHTML.html());
        	
        	var minAluLabel = $("label[for=NU_MIN_ALUNOS]");
        	var minAluHTML = $(html).find("label[for=NU_MIN_ALUNOS]");
        	minAluLabel.html(minAluHTML.html());
        	
        	var cargaPresLabel = $("label[for=NU_CARGA_PRESENCIAL]");
        	var cargaPresHTML = $(html).find("label[for=NU_CARGA_PRESENCIAL]");
        	cargaPresLabel.html(cargaPresHTML.html());
        	
        	var cargaDistLabel = $("label[for=NU_CARGA_DISTANCIA]");
        	var cargaDistHTML = $(html).find("label[for=NU_CARGA_DISTANCIA]");
        	cargaDistLabel.html(cargaDistHTML.html());
        	
        	var cargaCursoLabel = $("label[for=NU_CARGA_CURSO]");
        	var cargaCursoHTML = $(html).find("label[for=NU_CARGA_CURSO]");
        	cargaCursoLabel.html(cargaCursoHTML.html());
        	
        	var minConcLabel = $("label[for=NU_MIN_CONCLUSAO]");
        	var minConcHTML = $(html).find("label[for=NU_MIN_CONCLUSAO]");
        	minConcLabel.html(minConcHTML.html());
        	
        	var maxConcLabel = $("label[for=NU_MAX_CONCLUSAO]");
        	var maxConcHTML = $(html).find("label[for=NU_MAX_CONCLUSAO]");
        	maxConcLabel.html(maxConcHTML.html());
        	
        	$("#NU_SEQ_TIPO_CURSO_CAD").change(tipoCursoCadChange);
        	$("#UF_TURMA_CAD").change(ufCadChange);
        	$("#NU_SEQ_CURSO_CAD").change(cursoCadChange);
        	$("#CO_MUNICIPIO_CAD").change(municipioCadChange);
        	
        	exibicaoLabels();
        	
        	//Termina o efeito de loading.
			popUp.carregando();
        }
    });	
};

//Função para pesquisa ajax dos cursos por tipo
var tipoCursoCadChange = function(){
	//Efeito de loading.
	popUp.carregando();
	
	$.ajax({
        type: "POST",
        url: baseUrl + '/index.php/secretaria/avaliacaopedagogica/renderiza-curso-por-tipo-cad/NU_SEQ_TIPO_CURSO_CAD/' + $("#NU_SEQ_TIPO_CURSO_CAD").val() ,
        
        data : {
        	NU_SEQ_TIPO_CURSO_CAD: $("#NU_SEQ_TIPO_CURSO_CAD").val(),
		},
        success: function(html){
        	var find = $(html).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }
        	
        	var label = $("label[for=NU_SEQ_CURSO_CAD]");
        	var labelHTML = $(html).find("label[for=NU_SEQ_CURSO_CAD]");
        	label.html(labelHTML.html());
        	$("#NU_SEQ_TIPO_CURSO_CAD").change(tipoCursoCadChange);
        	$("#UF_TURMA_CAD").change(ufCadChange);
        	$("#NU_SEQ_CURSO_CAD").change(cursoCadChange);
        	$("#CO_MUNICIPIO_CAD").change(municipioCadChange);
        	
        	$("#NU_MIN_ALUNOS").val("");
        	$("#NU_CARGA_PRESENCIAL").val("");
        	$("#NU_CARGA_DISTANCIA").val("");
        	$("#NU_CARGA_CURSO").val("");
        	$("#NU_MIN_CONCLUSAO").val("");
        	$("#NU_MAX_CONCLUSAO").val("");
        	
        	//Termina o efeito de loading.
			popUp.carregando();
        }
    });
};

$("#NU_SEQ_TIPO_CURSO_CAD").change(tipoCursoCadChange);
$("#UF_TURMA_CAD").change(ufCadChange);
exibicaoLabels();

// FIM DA TELA DE CADASTRO