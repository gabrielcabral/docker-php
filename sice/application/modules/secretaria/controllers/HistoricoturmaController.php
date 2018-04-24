<?php

/**
 * Controller do HistoricoTurma
 * 
 * @author diego.matos
 * @since 25/04/2012
 */

class Secretaria_HistoricoTurmaController extends Fnde_Sice_Controller_Action {

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {

		//Recupera os parametros
		$params = $this->_getAllParams();

		$objTurma = new Fnde_Sice_Business_Turma();
		$infoTurma = $objTurma->pesquisaTurma($params, true);
		$infoTurma = $infoTurma[0];
		$infoComplementarTurma = $objTurma->pesquisarDadosComplementaresTurma(
				array('NU_SEQ_CURSO' => $infoTurma['NU_SEQ_CURSO'],
						'NU_SEQ_CONFIGURACAO' => $infoTurma['NU_SEQ_CONFIGURACAO']));
		$quantCursistas = $objTurma->pesquisarVinculosPorTurma($params['NU_SEQ_TURMA']);

		$form = new HistoricoTurma_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/historicoturma/save')->setMethod('post')->setAttrib(
				'id', 'form');

		$html = $form->getElement("htmlTurma");
		
		$str = $this->view->retornarHtmlTabela($infoTurma, $infoComplementarTurma,
				$quantCursistas['QUANT_CURSISTAS']);

		$html->setValue($str);

		$htmlAlunosMatriculados = $form->getElement("htmlAlunosMatriculados");
		$strAlunosMatriculados = $this->retornaHtmlAlunosMatriculados($params['NU_SEQ_TURMA']);
		$htmlAlunosMatriculados->setValue($strAlunosMatriculados);

		$htmlHistorico = $form->getElement("htmlHistorico");
		$strHistorico = $this->retornaHtmlHistorico($params['NU_SEQ_TURMA']);
		$htmlHistorico->setValue($strHistorico);

		return $form;
	}

	/**
	 * Retorna HTML de alunos matriculados.
	 * @param int $codTurma Codigo da turma.
	 */
	public function retornaHtmlAlunosMatriculados( $codTurma ) {

		$obBusiness = new Fnde_Sice_Business_HistoricoTurma();
		$arAlunosMatriculados = $obBusiness->getAlunosMatriculadosPorTurma($codTurma);

		$html = "<div class='listagem datatable'>";
		$html .= "<table>";
		$html .= "<thead><tr><th  style='text-align:center' >Contagem</th><th style='text-align:center' >Matrícula</th><th style='text-align:center' >Nome</th><th style='text-align:center' >CPF</th></tr></thead>";
		$html .= "<tbody>";

		$count = 0;

		foreach ( $arAlunosMatriculados as $aluno ) {
			$html .= "<tr><td>" . ++$count . "</td><td>" . $aluno['NU_MATRICULA'] . "</td><td>" . $aluno['NO_USUARIO']
					. "</td><td>" . $aluno['NU_CPF'] . "</td></tr>";
		}

		$html .= "</tbody>";
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}

	/**
	 * Retorna HTML do historico da turma.
	 * @param int $codTurma Codigo da turma.
	 */
	public function retornaHtmlHistorico( $codTurma ) {

		$obBusiness = new Fnde_Sice_Business_HistoricoTurma();
		$arHistorico = $obBusiness->getHistoricoPorTurma($codTurma);

		$html .= "<div class='listagem datatable'>";
		$html .= "<table>";
		$html .= "<thead><tr><th style='text-align:center' >ID</th><th style='text-align:center' >Situação</th><th style='text-align:center'>Data</th><th style='text-align:center' >Autor</th><th style='text-align:center'>Justificativa</th></tr></thead>";
		$html .= "<tbody>";

		foreach ( $arHistorico as $historico ) {
			$html .= " <tr><td>" . $historico['NU_SEQ_HISTORICO_TURMA'] . "</td><td>" . $historico['ST_TURMA'];
			$html .= " </td><td>" . $historico['DT_HISTORICO'] . "</td><td>" . $historico['NO_USUARIO'];

			$historico['DS_OBSERVACAO'] = wordwrap($historico['DS_OBSERVACAO'], 91, '<br>', 1);

			$html .= "</td><td>" . $historico['DS_OBSERVACAO'] . "</td></tr>";
		}

		$html .= "</tbody>";
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}

	/**
	 * Carrega tela de historico.
	 */
	public function carregarHistoricoAction() {
		$this->setTitle('Turma');
		$this->setSubtitle('Visualizar histórico');

		$menu = array($this->getUrl('secretaria', 'turma', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
	}

}
