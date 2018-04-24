<?php

/**
 * Controller do AutorizarNaoAutorizar
 *
 * @author poliane.silva
 * @since 21/05/2012
 */

class Secretaria_AutorizarNaoAutorizarController extends Fnde_Sice_Controller_Action {

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array(), $bAutorizar = true ) {

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

		$infoComplementarTurma = $objTurma->pesquisarDadosComplementaresTurma(
				array('NU_SEQ_CURSO' => $infoTurma['NU_SEQ_CURSO'],
						'NU_SEQ_CONFIGURACAO' => $infoTurma['NU_SEQ_CONFIGURACAO']));

		if ( $arDados['NU_SEQ_TURMA'] ) {
			$result = $obHistorico->getMotivoObservacao($arDados['NU_SEQ_TURMA'], 8);
		} else {
			$result = "";
		}

		$form = new AutorizarNaoAutorizar_Form($arDados, $result, $bAutorizar);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/autorizarnaoautorizar/nao-autorizar')->setMethod(
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
	 * Retorna o HTML com os dados dos alunos matriculados.
	 * @param int $codTurma
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
	 * Carrega tela de autorizacao.
	 */
	public function carregarAutorizarAction() {
		$this->setTitle('Turma');
		$this->setSubtitle('Autorizar');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'turma', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$arParam = $this->_getAllParams();

		$obBusinessTurma = new Fnde_Sice_Business_Turma();
		$turma = $obBusinessTurma->getTurmaPorId($arParam['NU_SEQ_TURMA']);

		if ( $turma['ST_TURMA'] != "3" ) {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"A turma selecionada com a situação {$turma['DS_ST_TURMA']} e não pode ser executada a ação Autorizar/Não autorizar Turma.");
			$this->_redirect("/secretaria/turma/list");

		}

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Carrega tela de nao autorizacao.
	 */
	public function carregarNaoAutorizarAction() {
		$this->setTitle('Turma');
		$this->setSubtitle('Não Autorizar Turma');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'turma', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$arParam = $this->_getAllParams();

		$obBusinessTurma = new Fnde_Sice_Business_Turma();
		$turma = $obBusinessTurma->getTurmaPorId($arParam['NU_SEQ_TURMA']);

		if ( $turma['ST_TURMA'] != "3" ) {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"A turma selecionada com a situação {$turma['DS_ST_TURMA']} e não pode ser executada a ação Autorizar/Não autorizar Turma.");
			$this->_redirect("/secretaria/turma/list");
		}

		$form = $this->getForm($arParam, null, false);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Autoriza turma.
	 */
	public function autorizarTurmaAction() {
		$obTurma = new Fnde_Sice_Business_Turma();
		$obComponente = new Fnde_Sice_Business_Componentes();
		$arParam = $this->_getAllParams();

		$obUsuario = new Fnde_Sice_Business_Usuario();

		$turma = $obTurma->getTurmaPorId($arParam['NU_SEQ_TURMA']);
		$usuario = $obUsuario->getUsuarioById($turma['NU_SEQ_USUARIO_TUTOR']);
		$dataAtual = date('d/m/Y');

		if ( Fnde_Sice_Business_Componentes::dataBRToEUA($turma['DT_INICIO'])
				< Fnde_Sice_Business_Componentes::dataBRToEUA($dataAtual) ) {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"Esta turma está com a data de início {$turma['DT_INICIO']}, portanto não poderá ser autorizada. Solicite ao tutor {$usuario['NO_USUARIO']} a alteração da data de início da turma.");
			$this->_redirect(
					"/secretaria/autorizarnaoautorizar/carregar-autorizar/NU_SEQ_TURMA/" . $arParam['NU_SEQ_TURMA']);
		}

		try {

			$obComponente->validaAutorizacao($arParam);

			$result = $obTurma->alteraSituacaoTurma($arParam, 4);
			if ( $result ) {
				$this->addMessage(Fnde_Message::MSG_SUCCESS, 'Turma autorizada com sucesso.');
				$this->_redirect("/secretaria/turma/list");
			}

		} catch ( Exception $e ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->_redirect(
					"/secretaria/autorizarnaoautorizar/carregar-autorizar/NU_SEQ_TURMA/" . $arParam['NU_SEQ_TURMA']);
		}
	}

	/**
	 * Nao autoriza turma.
	 */
	public function naoAutorizarAction() {
		$obTurma = new Fnde_Sice_Business_Turma();
		$arParam = $this->_getAllParams();

		// Se os dados não foram enviados por post retorna para a index
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($this->_request->getParams(), null, false);

		$htmlTurma = $form->getElement('htmlTurma')->getValue();
		$htmlAlunosMatriculados = $form->getElement('htmlAlunosMatriculados')->getValue();

		if ( !$form->isValid($_POST) ) {
			$form->getElement('htmlTurma')->setValue($htmlTurma);
			$form->getElement('htmlAlunosMatriculados')->setValue($htmlAlunosMatriculados);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			$this->view->form = $form;
			$this->setTitle('Turma');
			$this->setSubtitle('Não Autorizar Turma');
			return $this->render('form');
		}

		try {
			$result = $obTurma->alteraSituacaoTurma($arParam, 5);
			if ( $result ) {
				$this->addMessage(Fnde_Message::MSG_SUCCESS, 'Turma não autorizada com sucesso.');
				$this->_redirect("/secretaria/turma/list");
			}
		} catch ( Exception $e ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->_redirect(
					"/secretaria/autorizarnaoautorizar/carregar-nao-autorizar/NU_SEQ_TURMA/" . $arParam['NU_SEQ_TURMA']);
		}
	}
}
