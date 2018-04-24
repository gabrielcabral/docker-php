var changeUfAtuacaoEscola = function() {
    popUp.carregando();
	$.ajax({
		url : baseUrl
		+ '/index.php/manutencao/usuario/renderiza-mesoregiao/SG_UF_ATUACAO_PERFIL/'
		+ $("#SG_UF_ESCOLA").val(),

		data : {
			SG_UF_ATUACAO_PERFIL: $("#SG_UF_ESCOLA").val(),
		},

		success : function(data) {
			var find = $(data).find('#loginBase #login').html();
			if(find != null && find != ''){location.href = baseUrl+'/index.php'; return; }

			$("#NO_MESORREGIAO_ESCOLA option").remove();
			var labelHTML = $(data).find("#CO_MESORREGIAO option");
			$("#NO_MESORREGIAO_ESCOLA").append(labelHTML);

			$("#NO_MESORREGIAO_ESCOLA").val($(data).find("#CO_MESORREGIAO").val());

			$("#CO_MUNICIPIO_ESCOLA option").remove();
			var labelHTMLMunicipio = $(data).find("#CO_MUNICIPIO_PERFIL option");
			$("#CO_MUNICIPIO_ESCOLA").append(labelHTMLMunicipio);

			$("#CO_MUNICIPIO_ESCOLA").val($(data).find("#CO_MUNICIPIO_PERFIL").val());
            popUp.carregando();

		}
	});
};
$(document).ready(function() {
	$('#CO_MUNICIPIO_ESCOLA, #CO_REDE_ENSINO').change(function () {
		if($('#CO_REDE_ENSINO').val() && $('#CO_MUNICIPIO_ESCOLA').val()){


			$.ajax({
				url: baseUrl + '/index.php/relatorios/dadosdoscursistas/nomeescolachangefnde',
				type: 'POST',
				data: {
					SG_UF_ESCOLA: $("#SG_UF_ESCOLA").val(),
					CO_MUNICIPIO_ESCOLA: $("#CO_MUNICIPIO_ESCOLA").val(),
					CO_REDE_ENSINO: $("#CO_REDE_ENSINO").val()
				},
				success: function (data) {
					var find = $(data).find('#loginBase #login').html();
					if (find != null && find != '') {
						location.href = baseUrl + '/index.php';
						return;
					}

					$("#CO_ESCOLA option").remove();
					var labelHTML = $(data).find("#CO_ESCOLA option");
					$("#CO_ESCOLA").append(labelHTML);
					$("#CO_ESCOLA").val("");

				}
			});
		}
	});

	$('#CO_MUNICIPIO_ESCOLA').change(function () {

		if($('#CO_REDE_ENSINO').val() && $('#CO_MUNICIPIO_ESCOLA').val()){
            popUp.carregando();
			$.ajax({
				url: baseUrl + '/index.php/relatorios/dadosdoscursistas/dadosmesoregiao',
				type: 'POST',
				data: {
					CO_MUNICIPIO_ESCOLA: $("#CO_MUNICIPIO_ESCOLA").val(),
                    NU_SEQ_USUARIO: $("#NU_SEQ_USUARIO").val()
				},
				success: function (data) {
					var find = $(data).find('#loginBase #login').html();
					if (find != null && find != '') {
						location.href = baseUrl + '/index.php';
						return;
					}

					$("#NO_MESORREGIAO_ESCOLA option").remove();
					var labelHTML = $(data).find("#NO_MESORREGIAO_ESCOLA option");
					$("#NO_MESORREGIAO_ESCOLA").append(labelHTML);
					$("#NO_MESORREGIAO_ESCOLA").val("");

                    popUp.carregando();


				}
			});
		}
	});
});

$("#SG_UF_ESCOLA").change(changeUfAtuacaoEscola);
