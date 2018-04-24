<?php

class Fnde_Sice_View_Helper_RetornaHtmlPeriodo{
	
	/**
	 * Retorna o HTML da tabela com os dados do per�odo de vincula��o.
	 * @return string HTML
	 */
	public function retornaHtmlPeriodo( $arPeriodo ) {

		$html .= "<div class='listagem' style='display:inline'>";
		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";
		$html .= "<caption><i>Dados do Per�odo</i></caption>";
		$html .= "</table>";

		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";

		$html .= "<tr class='alt'>";
		$html .= "<td style='background-color:#E6EFF4; width:25%;'>";
		$html .= "Exerc�cio";
		$html .= "</td>";
		$html .= "<td style='width:25%;'>";
		$html .= "<b>{$arPeriodo['VL_EXERCICIO']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4;width:25%;'>";
		$html .= "Situa��o da Bolsa";
		$html .= "</td>";
		$html .= "<td style='width:25%;'>";
		$html .= "<b>{$arPeriodo['DS_SITUACAO_BOLSA']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Per�odo";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arPeriodo['NU_SEQ_PERIODO_VINCULACAO']} - {$arPeriodo['DT_INICIAL']} � {$arPeriodo['DT_FINAL']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4' >";
		$html .= "M�s Refer�ncia";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arPeriodo['MES_REFERENCIA']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Regi�o";
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