<?php

/**
 * Controller do Motivo Devolucao Avaliacao
 *
 * @author Izabel Rodrigues
 * @since 11/07/2012
 */

class Financeiro_MotivoDevolucaoAvaliacaoController extends Fnde_Sice_Controller_Action {

	/**
	 * Método acessório get do formulário da tela.
	 * @param array $arDados
	 * @param array $arExtra
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {
		$obUsuario = new Fnde_Sice_Business_Usuario();
		if ( !is_array($arDados['NU_SEQ_BOLSA']) ) {
			$arDados['NU_SEQ_BOLSA'] = array($arDados['NU_SEQ_BOLSA']);
		}

		$usuario = array();
		for ( $i = 0; $i < count($arDados['NU_SEQ_BOLSA']); $i++ ) {
			$usuario[$i] = $obUsuario->getUsuarioByIdBolsa($arDados['NU_SEQ_BOLSA'][$i]);
		}

		$arDados['NO_USUARIO'] = $usuario[0]['NO_USUARIO'];
		//Recupera os parametros
		$form = new MotivoDevolucaoAvaliacao_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction(
				$this->view->baseUrl()
						. '/index.php/financeiro/motivodevolucaoavaliacao/salvar-motivo-devolucao-avaliacao')->setMethod(
				'post')->setAttrib('id', 'form');
		$htmlHidden = $form->getElement('hiddenBolsa');
		$htmlHidden->setValue($this->hiddenBolsas($arDados));
		$this->setJustificativa($form);

		return $form;
	}

	/**
	 * Monta o formulário e renderiza a view.
	 * 
	 * @since 11/07/2012
	 */
	public function formAction() {
		$this->_helper->layout()->disableLayout();
		
		$arDados = $this->_getAllParams();
		$form = $this->getForm($arDados);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Método para gravar o motivo de devolução da bolsa para avaliação.
	 */
	public function salvarMotivoDevolucaoAvaliacaoAction() {
		$businessBolsa = new Fnde_Sice_Business_Bolsa();
		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$arParam = $this->_getAllParams();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$resultUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

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

			$htmlHidden = $form->getElement('hiddenBolsa');
			$htmlHidden->setValue($this->hiddenBolsas($arParam));

			$form->getElement("DS_OBSERVACAO")->setValue(utf8_decode($form->getElement("DS_OBSERVACAO")->getValue()));
			$this->view->form = $form;
			return $this->render('form');
		}

		try {

			$arParam['ST_BOLSA'] = 1;
			$sucess = true;
			for ( $i = 0; $i < count($arParam['NU_SEQ_BOLSA']); $i++ ) {
				$altStatus['NU_SEQ_BOLSA'] = $arParam['NU_SEQ_BOLSA'][$i];
				$altStatus['ST_BOLSA'] = $arParam['ST_BOLSA'];
				if ( $businessBolsa->alterarStatusBolsa($altStatus) ) {
					$arHistorico = array('NU_SEQ_BOLSA' => $arParam['NU_SEQ_BOLSA'][$i],
							'NU_SEQ_USUARIO' => $resultUsuario['NU_SEQ_USUARIO'],
							'DT_HISTORICO' => date('d/m/Y G:i:s'), 'ST_BOLSA' => $arParam['ST_BOLSA'],
							'NU_SEQ_JUSTIF_DEV_BOLSA' => $arParam['CO_JUSTIFICATIVA_DEVOLUCAO'],
							'DS_OBSERVACAO' => $arParam['DS_OBSERVACAO']);

					if ( !$businessBolsa->salvarHistoricoBolsa($arHistorico) ) {
						$sucess = false;
					}
				} else {
					$sucess = false;
				}
			}

			if ( $sucess ) {
				$this->addMessage(Fnde_Message::MSG_SUCCESS, 'Justificativa inserida com sucesso');
				$this->addInstantMessage(Fnde_Message::MSG_SUCCESS, "FECHAR_POPUP");
			}

		} catch ( Exception $e ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
		}
		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Método para adicionar os id's da bolsa em campos hidden.
	 * @param array $arDados
	 */
	public function hiddenBolsas( $arDados ) {

		if ( !is_array($arDados['NU_SEQ_BOLSA']) ) {
			$arDados['NU_SEQ_BOLSA'] = array($arDados['NU_SEQ_BOLSA']);
		}

		$html = "";
		for ( $i = 0; $i < count($arDados['NU_SEQ_BOLSA']); $i++ ) {
			$html .= "<input type='hidden' name='NU_SEQ_BOLSA[$i]' id='NU_SEQ_BOLSA' value='{$arDados['NU_SEQ_BOLSA'][$i]}'>";
		}

		return $html;
	}

	/**
	 * Adiciona valores ao combo de justificativa
	 * @param $form
	 */
	public function setJustificativa( $form ) {
		$rsJustificativa = Fnde_Sice_Business_Componentes::getAllByTable('JustifDevBolsa',
				array('NU_SEQ_JUSTIF_DEV_BOLSA', 'DS_JUSTIF_DEV_BOLSA'));

		$form->setJustificativa($rsJustificativa);
	}
}
