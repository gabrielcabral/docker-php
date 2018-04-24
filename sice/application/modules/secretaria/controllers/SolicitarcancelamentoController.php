<?php

/**
 * Controller do SolicitarAutorizacao
 *
 * @author poliane.silva
 * @since 17/05/2012
 */

class Secretaria_SolicitarCancelamentoController extends Fnde_Sice_Controller_Action {

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
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

		$form = new SolicitarCancelamento_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/solicitarcancelamento/solicitar-cancelamento')->setMethod(
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
	 * Retorna HTML de alunos matriculados
	 * @param $codTurma Codigo da turma.
	 */
	public function retornaHtmlAlunosMatriculados( $codTurma ) {

		$obBusiness = new Fnde_Sice_Business_HistoricoTurma();
		$arAlunosMatriculados = $obBusiness->getAlunosMatriculadosPorTurma($codTurma);

		$html .= "<div class='listagem datatable'>";
		$html .= "<table>";
		$html .= "<thead><tr><th style='text-align:center' >Contagem</th><th style='text-align:center' >Matrícula</th><th style='text-align:center' >Nome</th><th style='text-align:center' >CPF</th></tr></thead>";
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
	 * Carrega tela de solicitar cancelamento.
	 */
	public function carregarCancelamentoAction() {
		$this->setTitle('Matricular');
		$this->setSubtitle('Solicitar Cancelamento');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'turma', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$arParam = $this->_getAllParams();

		$obBusinessTurma = new Fnde_Sice_Business_Turma();
		$turma = $obBusinessTurma->getTurmaPorId($arParam['NU_SEQ_TURMA']);

		if ( $turma['ST_TURMA'] != "4" ) {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"A turma selecionada com a situação {$turma['DS_ST_TURMA']} e não pode ser executada a ação Solicitar Cancelamento.");
			$this->_redirect("/secretaria/turma/list");
		}

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Solicita o cancelamento.
	 */
	public function solicitarCancelamentoAction() {
		$obTurma = new Fnde_Sice_Business_Turma();
		$arParam = $this->_getAllParams();

		// Se os dados não foram enviados por post retorna para a index
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($this->_request->getParams());

		$htmlTurma = $form->getElement('htmlTurma')->getValue();
		$htmlAlunosMatriculados = $form->getElement('htmlAlunosMatriculados')->getValue();

		if ( !$form->isValid($_POST) ) {
			$form->getElement('htmlTurma')->setValue($htmlTurma);
			$form->getElement('htmlAlunosMatriculados')->setValue($htmlAlunosMatriculados);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			$this->view->form = $form;
			$this->setTitle('Matricular');
			$this->setSubtitle('Solicitar Cancelamento');
			return $this->render('form');
		}

		try {
			$result = $obTurma->alteraSituacaoTurma($arParam, 8);
			if ( $result ) {
				$this->addMessage(Fnde_Message::MSG_SUCCESS, 'Solicitação de cancelamento realizada com sucesso.');
				$this->_redirect("/secretaria/turma/list");
			}

		} catch ( Exception $e ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->_redirect(
					"/secretaria/solicitarcancelamento/carregar-cancelamento/NU_SEQ_TURMA/" . $arParam['NU_SEQ_TURMA']);
		}
	}

}
