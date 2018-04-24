<?php

/**
 * Controller do Solicitar Homologação
 * 
 * @author diego.matos
 * @since 11/07/2012
 */

class Financeiro_DetalhesBolsistaController extends Fnde_Sice_Controller_Action {
	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 11/07/2012
	 */
	public function formAction() {
		$this->_helper->layout()->disableLayout();
		
		//Recupera o objeto de formulário para validação
		$form = $this->getForm();

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 26/06/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {
		$businessBolsa = new Fnde_Sice_Business_Bolsa();
		
		$form = new DetalhesBolsista_Form($arDados, $arExtra);

		$form->setDecorators(array('FormElements', 'Form'));

		//Recupera os dados de pesquisa da tela de filtrar bolsas.
		$arParam = $this->_getAllParams();

		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$businessTurma = new Fnde_Sice_Business_Turma();
		
		$arSessao = $_SESSION['searchParam']['param'];
		$arParam['SG_UF'] = $arSessao['SG_UF'];
		
		if($businessBolsa->isBolsaAntiga($arSessao['NU_SEQ_PERIODO_VINCULACAO'])){
			$arBolsistas = $businessUsuario->pesquisarDadosBolsistaPorIdAntigo(array('NU_SEQ_BOLSA' => $arParam['NU_SEQ_BOLSA']));
			$arTurmas = $businessTurma->obterDadosTurmasAvaliadasAntigo($arParam);
		}else{
			$arBolsistas = $businessUsuario->pesquisarDadosBolsistaPorId(array('NU_SEQ_BOLSA' => $arParam['NU_SEQ_BOLSA']));
			$arTurmas = $businessTurma->obterDadosTurmasAvaliadas($arParam);
		}

		$htmlBolsista = $form->getElement("htmlBolsista");
		$strDadosBolsistas = $this->retornaHtmlBolsista($arBolsistas);
		$htmlBolsista->setValue($strDadosBolsistas);

		$htmlTurmas = $form->getElement("htmlTurmasAvaliadas");
		$strDadosTurmas = $this->retornaHtmlTurmas($arTurmas);
		$htmlTurmas->setValue($strDadosTurmas);

		return $form;
	}

	/**
	 * Retorna o HTML da tabela com os dados dos bolsista.
	 * @param array $arBolsistas Dados dos bolsistas.
	 * @return string HTML.
	 */
	public function retornaHtmlBolsista( $arBolsista ) {

		$html = "";
		$html .= "<div class='listagem' style='display:inline'>";
		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";
		$html .= "<caption><i>Dados do Bolsista</i></caption>";
		$html .= "</table>";

		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";

		$html .= "<tr class='alt'>";
		$html .= "<td style='background-color:#E6EFF4; width:150px;'>";
		$html .= "Nome";
		$html .= "</td>";
		$html .= "<td style='width:150px'>";
		$html .= "<b>{$arBolsista['NO_USUARIO']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4; width:150px;'>";
		$html .= "Perfil";
		$html .= "</td>";
		$html .= "<td style='width:150px;'>";
		$html .= "<b>{$arBolsista['DS_TIPO_PERFIL']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr class='alt'>";
		$html .= "<td style='background-color:#E6EFF4;'>";
		$html .= "Mesorregião";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arBolsista['NO_MESO_REGIAO']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Município";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arBolsista['NO_MUNICIPIO']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr class='alt'>";
		$html .= "<td style='background-color:#E6EFF4;'>";
		$html .= "Estado";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arBolsista['SG_UF_ATUACAO_PERFIL']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Situação";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arBolsista['ST_USUARIO']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "</table>";

		$html .= "</div>";

		return $html;
	}

	/**
	 * Retorna HTML das turmas avaliadas.
	 * @param array $arBolsistas
	 */
	public function retornaHtmlTurmas( $arTurmas ) {

		$html = "<div class='listagem'  style='display:inline'>";
		$html .= "<table id='tbBolsistas' class='borders' cellspacing='0' cellpadding='0' width='100%'>";
		$html .= "	<caption><i>Listagem das turmas avaliadas</i></caption>";
		$html .= "	<thead><tr class='alt'>";
		$html .= "		<th>Id da turma</th>";
		$html .= "		<th>Curso</th>";
		$html .= "		<th>Qtd cursistas</th>";
		$html .= "		<th><center>Avaliado por</center></th>";
		$html .= "		<th><center>Perfil Avaliador</center></th>";
		$html .= "		<th><center>Data da avaliação</center></th>";
		$html .= "	</tr></thead>";
		$html .= "	<tbody>";
		foreach ( $arTurmas as $arTurma ) {
			$html .= "<tr class='alt'>";
			$html .= "	<td>" . $arTurma['NU_SEQ_TURMA'] . "</td>";
			$html .= "	<td>" . $arTurma['DS_NOME_CURSO'] . "</td>";
			$html .= "	<td><center>" . $arTurma['QTD_CURSISTA'] . "</center></td>";
			$html .= "	<td>" . $arTurma['NO_USUARIO'] . "</td>";
			$html .= "	<td><center>" . $arTurma['DS_TIPO_PERFIL'] . "</center></td>";
			$html .= "	<td><center>" . $arTurma['DT_AVALIACAO'] . "</center></td>";
			$html .= "</tr>";
		}
		$html .= "	</tbody>";
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}
}
