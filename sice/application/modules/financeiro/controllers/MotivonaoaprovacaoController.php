<?php

/**
 * Controller do MotivonaoaprovacaoController
 *
 * @author poliane.silva
 * @since 17/05/2012
 */

class Financeiro_MotivoNaoaprovacaoController extends Fnde_Sice_Controller_Action {

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {
		$obTurma = new Fnde_Sice_Business_Turma();
		$obCurso = new Fnde_Sice_Business_Curso();

		$turma = $obTurma->getTurmaPorId($arDados['NU_SEQ_TURMA']);
		$noCurso = $obCurso->getCursoById($turma['NU_SEQ_CURSO']);

		$arDados['DS_NOME_CURSO'] = $noCurso['DS_NOME_CURSO'];

		$form = new MotivoNaoAprovacao_Form($arDados, $arExtra);

		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction(
				$this->view->baseUrl() . '/index.php/financeiro/motivonaoaprovacao/salvar-motivo-nao-aprovacao')->setMethod(
				'post')->setAttrib('id', 'form');
		$this->setJustificativa($form);
		return $form;
	}

	/**
	 * Monta o form e renderiza a view.
	 * 
	 * @author poliane.silva
	 * @since 17/05/2012
	 */
	public function formAction() {

		$this->_helper->layout()->disableLayout();

		//Recupera os parametros
		$arDados = $this->_getAllParams();

		$form = $this->getForm($arDados);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Salva o motivo de não aprovação.
	 */
	public function salvarMotivoNaoAprovacaoAction() {
		// Se os dados não foram enviados por post retorna para a index
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}

		//Recupera os parâmetros do request
		$arParams = $this->_request->getParams();

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($arParams);

		if ( !$form->isValid($_POST) ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			$form->getElement("DS_OBSERVACAO")->setValue(utf8_decode($form->getElement("DS_OBSERVACAO")->getValue()));
			$this->view->form = $form;
			return $this->render('form');
		}

		//Recupera ID do usuario logado no sistema.
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		$arDadosAprovacao = array('NU_SEQ_TURMA' => $arParams['NU_SEQ_TURMA'],
				'NU_SEQ_USUARIO_AVALIADOR' => $arUsuario['NU_SEQ_USUARIO'], 'ST_APROVACAO' => 'N',
				'NU_SEQ_JUSTIF_REPROV' => $arParams['CO_JUSTIFICATIVA_REPROVACAO'],
				'DS_OBSERVACAO' => $arParams['DS_OBSERVACAO'], 'DT_AVALIACAO' => date('d/m/Y'),);

		$obModelAvaliacaoTurma = new Fnde_Sice_Model_AvaliacaoTurma();
		$obBusinessAvaliacaoTurma = new Fnde_Sice_Business_AvaliacaoTurma();

		try {
			if ( $obBusinessAvaliacaoTurma->getAvaliacaoTurmaById($arDadosAprovacao['NU_SEQ_TURMA']) ) {
				$obModelAvaliacaoTurma->update($arDadosAprovacao, "NU_SEQ_TURMA = {$arDadosAprovacao['NU_SEQ_TURMA']}");
			} else {
				$obModelAvaliacaoTurma->insert($arDadosAprovacao);
			}

			$this->addMessage(Fnde_Message::MSG_SUCCESS, "Justificativa inserida com sucesso.");
			$this->addInstantMessage(Fnde_Message::MSG_SUCCESS, "FECHAR_POPUP");
		} catch ( Exception $e ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
		}

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Adicionar os valores ao formulário
	 * @param form $form
	 */
	private function setJustificativa( $form ) {
		$rsJustificativa = Fnde_Sice_Business_Componentes::getAllByTable('JustifReprovTurma',
				array('NU_SEQ_JUSTIF_REPROV', 'DS_JUSTIF_REPROV'));

		$form->setJustificativa($rsJustificativa);
	}

}
