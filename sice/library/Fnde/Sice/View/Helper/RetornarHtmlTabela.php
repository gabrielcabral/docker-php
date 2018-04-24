<?php

class Fnde_Sice_View_Helper_RetornarHtmlTabela{
	
	public function retornarHtmlTabela($infoTurma, $infoComplementarTurma, $quantCursistas){
		
		return "<div class='listagem' style='display:inline'>"
			. "		<table class='borders' cellspacing='0' cellpadding='0' width='100%'>"
			. "			<caption>Dados da Turma</caption>" . "		</table>"
			. "		<table class='borders' cellspacing='0' cellpadding='0' width='100%'>"
			. "			<tr class='alt'>"
			. "				<td style='background-color:#E6EFF4; width: 25%;'>Cód.Turma</td>"
			. "				<td style='width: 25%;'><b>{$infoTurma['NU_SEQ_TURMA']}</b></td>"
			. "				<td style='background-color:#E6EFF4;width: 25%;'>Data de início</td>"
			. "				<td style='width: 25%;' ><b>{$infoTurma['DT_INICIO']}</b></td>" . "			</tr>"
			. "			<tr>" . "				<td style='background-color:#E6EFF4'>UF</td>"
			. "				<td><b>{$infoTurma['UF_TURMA']}</b></td>"
			. "				<td style='background-color:#E6EFF4'>Data fim prevista</td>"
			. "				<td><b>{$infoTurma['DT_FIM']}</b></td>" . "			</tr>" . "			<tr>"
			. "				<td style='background-color:#E6EFF4' >Mesorregião</td>"
			. "				<td><b>{$infoTurma['NO_MESO_REGIAO']}</b></td>"
			. "				<td style='background-color:#E6EFF4' >Nome do articulador</td>"
			. "				<td><b>{$infoTurma['NO_ARTICULADOR']}</b></td>" . "			</tr>" . "			<tr>"
			. "				<td style='background-color:#E6EFF4'>Município</td>"
			. "				<td><b>{$infoTurma['NO_MUNICIPIO']}</b></td>"
			. "				<td style='background-color:#E6EFF4'>Nome do tutor</td>"
			. "				<td><b>{$infoTurma['NO_TUTOR']}</b></td>" . "			</tr>" . "			<tr>"
			. "				<td style='background-color:#E6EFF4'>Curso</td>"
			. "				<td><b>{$infoTurma['DS_NOME_CURSO']}</b></td>"
			. "				<td style='background-color:#E6EFF4'>Total carga horária</td>"
			. "				<td><b>{$infoComplementarTurma['NU_CARGA_CURSO']}</b></td>" . "			</tr>"
			. "			<tr>" . "				<td style='background-color:#E6EFF4'>Pré-requisito</td>"
			. "				<td>"
			. ( $infoComplementarTurma['DS_PREREQUISITO_CURSO'] == 'N' ? "<b>Não</b>" : "<b>Sim</b>" )
			. "				</td>" . "				<td style='background-color:#E6EFF4'>Carga horária a distância</td>"
			. "				<td><b>{$infoComplementarTurma['NU_CARGA_DISTANCIA']}</b></td>" . "			</tr>"
			. "			<tr>" . "				<td style='background-color:#E6EFF4'>Qtd. Mínima de cursistas</td>"
			. "				<td><b>{$infoComplementarTurma['NU_MIN_ALUNOS']}</b></td>"
			. "				<td style='background-color:#E6EFF4'>Carga horária presencial</td>"
			. "				<td><b>{$infoComplementarTurma['NU_CARGA_PRESENCIAL']}</b></td>" . "			</tr>"
			. "			<tr>" . "				<td style='background-color:#E6EFF4'>Cursistas matriculados</td>"
			. "				<td><b>{$quantCursistas}</b></td>"
			. "				<td style='background-color:#E6EFF4'>Situação</td>"
			. "				<td><b>{$infoTurma['DS_ST_TURMA']}</b></td>" . "			</tr>" . "		</table>"
			. "</div>";
	}
	
	
}