<?php

/**
 * Controller do Visualizar Turmas
 * 
 * @author rafael.paiva
 * @since 28/06/2012
 */

class Financeiro_VisualizarTurmasController extends Fnde_Sice_Controller_Action {

	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 28/06/2012
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
	 * @since 28/06/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {
		$obTurma = new Fnde_Sice_Business_Turma();
		$obBolsa = new Fnde_Sice_Business_Bolsa();
		$params = $this->_getAllParams();

		$form = new VisualizarTurmas_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/turma/salvar-turma')->setMethod('post')->setAttrib(
				'id', 'form');

		//Recupera os dados de pesquisa da tela de filtrar bolsas.
		$arSessao = $_SESSION['searchParam']['param'];

		//Montando a mensagem de orientação
		$msgOrientacao .= "<div id='mensagens'  style='padding: 0px;'>";
		$msgOrientacao .= "<div class='msgOrientacao'>";
		$msgOrientacao .= "	  	<h3>Orientação</h3>";
		$msgOrientacao .= "		<p>";
		$msgOrientacao .= "			Legenda:";
		$msgOrientacao .= "			<ul> ";
		$msgOrientacao .= "				<li type=disc>AD: quantidade de cursistas aprovados com destaque</li> ";
		$msgOrientacao .= "				<li type=disc>A: quantidade de cursistas aprovados</li> ";
		$msgOrientacao .= "				<li type=disc>R: quantidade de cursistas reprovados</li> ";
		$msgOrientacao .= "				<li type=disc>D: quantidade de cursistas desistentes</li> ";
		$msgOrientacao .= "				<li type=disc>CM: cursistas matriculados</li> ";
		$msgOrientacao .= "			</ul> ";
		$msgOrientacao .= "	  	</p>";
		$msgOrientacao .= "</div> ";
		$msgOrientacao .= "</div> ";

		$htmlOrientacao = $form->getElement("htmlOrientacao");
		$htmlOrientacao->setValue($msgOrientacao);
		$arSessao['NU_SEQ_TIPO_PERFIL'] = $params['NU_SEQ_TIPO_PERFIL'];
		
		//Cria HTML com os dados dos Bolsistas.
		if($obBolsa->isBolsaAntiga($arSessao['NU_SEQ_PERIODO_VINCULACAO'])){
			$arBolsistas = $obTurma->getDadosAvaliarTurmasAntigo($arSessao, $params['NU_SEQ_BOLSA']);
		}else{
			$arBolsistas = $obTurma->getDadosAvaliarTurmas($arSessao, $params['NU_SEQ_BOLSA']);
		}
		
		$htmlBolsistas = $form->getElement("htmlBolsistas");
		$strDadosBolsistas = $this->retornaHtmlBolsistas($arBolsistas);
		$htmlBolsistas->setValue($strDadosBolsistas);

		return $form;
	}

	/**
	 * Retorna o HTML da tabela com os dados dos bolsista.
	 * @param array $arBolsistas Dados dos bolsistas.
	 * @return string HTML.
	 */
	public function retornaHtmlBolsistas( $arBolsistas ) {
		//Zend_Debug::dump($arBolsistas); exit;

		$count = 0;

		$html = "<div class='listagem datatable' style='padding: 0px;'>";
		$html .= "<table>";
		$html .= "	<caption><i>Listagem de turmas</i></caption>";
		$html .= "	<thead><tr>";
		$html .= "		<th>Nome Articulador</th>";
		$html .= "		<th>Nome Tutor</th>";
		$html .= "		<th>Curso</th>";
		$html .= "		<th>Qtd módulos</th>";
		$html .= "		<th>Dt. de Início</th>";
		$html .= "		<th>Dt. fim prevista</th>";
		$html .= "		<th>Dt. Finalizada</th>";
		$html .= "		<th>Dias atraso</th>";
		$html .= "		<th style='text-align: center'>AD</th>";
		$html .= "		<th style='text-align: center'>A</th>";
		$html .= "		<th style='text-align: center'>R</th>";
		$html .= "		<th style='text-align: center'>D</th>";
		$html .= "		<th style='text-align: center'>CM</th>";
		$html .= "		<th style='text-align: center'>Status da turma</th>";
		$html .= "	</tr></thead>";
		$html .= "	<tbody>";
		foreach ( $arBolsistas as $bolsista ) {
			if ( $bolsista['DT_FINALIZACAO'] && $bolsista['DT_FIM'] ) {

				$dataFinalizacao = new Zend_Date($bolsista['DT_FINALIZACAO'], 'D/M/Y');

				$dataFimPrevista = new Zend_Date($bolsista['DT_FIM'], 'D/M/Y');

				// calcula a diferença de dias entre as datas de finalização e fim prevista.
				$diferenca = ( int ) floor(
 ( $dataFinalizacao->getTimestamp() - $dataFimPrevista->getTimestamp() ) / ( 3600 * 24 ));

			} else {
				$diferenca = null;
			}

			$html .= "<tr>";
			$html .= "	<td>" . $bolsista['NO_USUARIO_ARTICULADOR'] . "</td>";
			$html .= "	<td>" . $bolsista['NO_USUARIO_TUTOR'] . "</td>";
			$html .= "	<td>" . $bolsista['DS_NOME_CURSO'] . "</td>";
			$html .= "	<td>" . $bolsista['QTD_MODULO'] . "</td>";
			$html .= "	<td>" . $bolsista['DT_INICIO'] . "</td>";
			$html .= "	<td>" . $bolsista['DT_FIM'] . "</td>";
			$html .= "	<td>" . $bolsista['DT_FINALIZACAO'] . "</td>";
			$html .= "	<td style='text-align: center'>" . $diferenca . "</td>";
			$html .= "	<td style='text-align: center'>" . $bolsista['AD'] . "</td>";
			$html .= "	<td style='text-align: center'>" . $bolsista['A'] . "</td>";
			$html .= "	<td style='text-align: center'>" . $bolsista['R'] . "</td>";
			$html .= "	<td style='text-align: center'>" . $bolsista['D'] . "</td>";
			$html .= "	<td style='text-align: center'>" . $bolsista['CM'] . "</td>";

			if ( $bolsista['ST_APROVACAO'] == 'S' ) {
				$html .= "	<td style='text-align: center'> Aprovada </td>";
			} elseif ( $bolsista['ST_APROVACAO'] == 'N' ) {
				$html .= "	<td style='text-align: center'> Reprovada </td>";
			} else {
				$html .= "	<td style='text-align: center'>{$bolsista['ST_APROVACAO']} Não Avaliado </td>";
			}
			$html .= "</tr>";

			$count++;
		}

		$html .= "	</tbody>";
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}
}
