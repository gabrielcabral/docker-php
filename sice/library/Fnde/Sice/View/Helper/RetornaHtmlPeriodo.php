<?php

class Fnde_Sice_View_Helper_RetornaHtmlPeriodo{
	
	/**
	 * Retorna o HTML da tabela com os dados do período de vinculação.
	 * @return string HTML
	 */
	public function retornaHtmlPeriodo( $arPeriodo ) {

		$html .= "<div class='listagem' style='display:inline'>";
		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";
		$html .= "<caption><i>Dados do Período</i></caption>";
		$html .= "</table>";

		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";

		$html .= "<tr class='alt'>";
		$html .= "<td style='background-color:#E6EFF4; width:25%;'>";
		$html .= "Exercício";
		$html .= "</td>";
		$html .= "<td style='width:25%;'>";
		$html .= "<b>{$arPeriodo['VL_EXERCICIO']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4;width:25%;'>";
		$html .= "Situação da Bolsa";
		$html .= "</td>";
		$html .= "<td style='width:25%;'>";
		$html .= "<b>{$arPeriodo['DS_SITUACAO_BOLSA']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Período";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arPeriodo['NU_SEQ_PERIODO_VINCULACAO']} - {$arPeriodo['DT_INICIAL']} à {$arPeriodo['DT_FINAL']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4' >";
		$html .= "Mês Referência";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arPeriodo['MES_REFERENCIA']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Região";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arPeriodo['NO_REGIAO']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4' >";
		$html .= "UF";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arPeriodo['SG_UF']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "</table>";

		$html .= "</div>";

		return $html;
	}
	
	
}