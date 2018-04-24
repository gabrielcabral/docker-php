<?php

/**
 * Controller do CancelarTurma
 *
 * @author poliane.silva
 * @since 18/05/2012
 */

class Secretaria_CancelarTurmaController extends Fnde_Sice_Controller_Action {

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {
		$obHistorico = new Fnde_Sice_Business_HistoricoTurma();

		//Recupera os parametros
		$params = $this->_getAllParams();

		$objTurma = new Fnde_Sice_Business_Turma();
		$infoTurma = $objTurma->pesquisaTurma($params, true);
		$infoTurma = $infoTurma[0];
		$infoComplementarTurma = $objTurma->pesquisarDadosComplementaresTurma(
				array('NU_SEQ_CURSO' => $infoTurma['NU_SEQ_CURSO'],
						'NU_SEQ_CONFIGURACAO' => $infoTurma['NU_SEQ_CONFIGURACAO']));
		$quantCursistas = $objTurma->pesquisarVinculosPorTurma($params['NU_SEQ_TURMA']);

		if ( $arDados['NU_SEQ_TURMA'] ) {
			$result = $obHistorico->getMotivoObservacao($arDados['NU_SEQ_TURMA'], 8);
		}

		$form = new CancelarTurma_Form($arDados, $result);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/cancelarturma/cancelar-turma')->setMethod(
				'post')->setAttrib('id', 'form');

		$html = $form->getElement("htmlTurma");
		$str = $this->view->retornarHtmlTabela($infoTurma, $infoComplementarTurma,
				$quantCursistas['QUANT_CURSISTAS']);
		$html->setValue($str);

		$htmlAlunosMatriculados = $form->getElement("htmlAlunosMatriculados");
		$strAlunosMatriculados = $this->retornaHtmlAlunosMatriculados($params['NU_SEQ_TURMA']);
		$htmlAlunosMatriculados->setValue($strAlunosMatriculados);

		$nuMinAlunos = $form->getElement("NU_MIN_ALUNOS");
		$nuMinAlunos->setValue($infoComplementarTurma['NU_MIN_ALUNOS']);

		$nuAlunosMatriculados = $form->getElement("NU_ALUNOS_MATRICULADOS");
		$nuAlunosMatriculados->setValue($quantCursistas['QUANT_CURSISTAS']);

		return $form;
	}

	/**
	 * Retorna HTML de alunos matriculados na turma.
	 * @param int $codTurma Codigo da turma.
	 */
	public function retornaHtmlAlunosMatriculados( $codTurma ) {

		$obBusiness = new Fnde_Sice_Business_HistoricoTurma();
		$arAlunosMatriculados = $obBusiness->getAlunosMatriculadosPorTurma($codTurma);

		$html .= "<div class='listagem datatable'>";
		$html .= "<table>";
		$html .= "<thead><tr><th style='text-align:center' >Contagem</th><th style='text-align:center'>Matrícula</th><th style='text-align:center' >Nome</th><th style='text-align:center' >CPF</th></tr></thead>";
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
	 * Carrega tela de cancelamento.
	 */
	public function carregarCancelarAction() {
		$this->setTitle('Turma');
		$this->setSubtitle('Cancelar');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'turma', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$arParam = $this->_getAllParams();

		$obBusinessTurma = new Fnde_Sice_Business_Turma();
		$turma = $obBusinessTurma->getTurmaPorId($arParam['NU_SEQ_TURMA']);

		if ( $turma['ST_TURMA'] != "8" ) {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"A turma selecionada com a situação {$turma['DS_ST_TURMA']} e não pode ser executada a ação Cancelar Turma.");
			$this->_redirect("/secretaria/turma/list");

		}

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Cancela turma.
	 */
	public function cancelarTurmaAction() {
		$obTurma = new Fnde_Sice_Business_Turma();
		$arParam = $this->_getAllParams();

		try {
			$result = $obTurma->alteraSituacaoTurma($arParam, 9);
			if ( $result ) {
				$this->addMessage(Fnde_Message::MSG_SUCCESS, 'Turma cancelada com sucesso.');
				$this->_redirect("/secretaria/turma/list");
			}

		} catch ( Exception $e ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->_redirect("/secretaria/cancelarturma/carregar-cancelar/NU_SEQ_TURMA/" . $arParam['NU_SEQ_TURMA']);
		}
	}

	/**
	 * Rejeita turma.
	 */
	public function rejeitarCancelarTurmaAction() {
		$obTurma = new Fnde_Sice_Business_Turma();
		$arParam = $this->_getAllParams();

		try {
			$result = $obTurma->alteraSituacaoTurma($arParam, 4);
			if ( $result ) {
				$this->addMessage(Fnde_Message::MSG_SUCCESS, 'Cancelamento rejeitado com sucesso.');
				$this->_redirect("/secretaria/turma/list");
			}
		} catch ( Exception $e ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->_redirect("/secretaria/cancelarturma/carregar-cancelar/NU_SEQ_TURMA/" . $arParam['NU_SEQ_TURMA']);
		}
	}

}
