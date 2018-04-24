<?php

/**
 * Controller do Motivo Devolucao
 *
 * @author poliane.silva
 * @since 11/07/2012
 */

class Financeiro_MotivoDevolucaoController extends Fnde_Sice_Controller_Action {

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {

		$form = new MotivoDevolucao_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/financeiro/motivodevolucao/salvar-motivo-devolucao')->setMethod(
				'post')->setAttrib('id', 'form');
		$this->setJustificativa($form);

		return $form;
	}

	/**
	 * Monta o formulário e renderiza a view.
	 * 
	 * @author poliane.silva
	 / @since 11/07/2012
	 */
	public function formAction() {

		$this->_helper->layout()->disableLayout();

		$arDados = $this->_getAllParams();

		$form = $this->getForm($arDados);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Método para gravar o motivo de devolução.
	 */
	public function salvarMotivoDevolucaoAction() {

		// Se os dados não foram enviados por post retorna para a index
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($this->_request->getParams());

		if ( !$form->isValid($_POST) ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));

			$this->view->form = $form;
			return $this->render('form');
		}

		$arParams = $this->_getAllParams();

		$businessBolsa = new Fnde_Sice_Business_Bolsa();

		try {
			$arParams['ST_BOLSA'] = 1;
			$businessBolsa->alterarStatusBolsa($arParams);

			$this->addMessage(Fnde_Message::MSG_SUCCESS, "A(s) devolução(ões) foi(ram) realizada(s) com sucesso.");
			$this->addInstantMessage(Fnde_Message::MSG_SUCCESS, "FECHAR_POPUP");
		} catch ( Exception $e ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, "Erro ao tentar devolver.");
			$this->_redirect("/financeiro/homologarbolsas/form/NU_SEQ_BOLSA/{$arParams['NU_SEQ_BOLSA_URL']}");
		}

		$this->view->form = $form;
		return $this->render('form');
	}

	public function setJustificativa( $form ) {
		$rsJustificativa = Fnde_Sice_Business_Componentes::getAllByTable('JustifDevBolsa',
				array('NU_SEQ_JUSTIF_DEV_BOLSA', 'DS_JUSTIF_DEV_BOLSA'));

		$form->setJustificativa($rsJustificativa);
	}
}
