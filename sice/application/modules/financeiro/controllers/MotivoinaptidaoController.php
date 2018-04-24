<?php

/**
 * Controller do MotivoinaptidaoController
 *
 * @author poliane.silva
 * @since 17/05/2012
 */

class Financeiro_MotivoInaptidaoController extends Fnde_Sice_Controller_Action {

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {

		$form = new MotivoInaptidao_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/financeiro/motivoinaptidao/salvar-motivo-inaptidao')->setMethod(
				'post')->setAttrib('id', 'form');

		$this->setUsuarioFormulario($form, $arDados['NU_SEQ_BOLSA']);
		$this->setJustificativaForm($form);

		return $form;
	}

	/**
	 * Insere o conteúdo da Justificativa no Formulario
	 *
	 * @param $noUsuario
	 *
	 * @return void
	 *
	 * @author gustavo.gomes
	 */
	public function setJustificativaForm( $form ) {

		$rsJustificativa = Fnde_Sice_Business_Componentes::getAllByTable('JustifInaptidaoBolsista',
				array('NU_SEQ_JUSTIF_INAPTIDAO', 'DS_JUSTIF_INAPTIDAO'));
		$form->setJustificativaForm($rsJustificativa);

	}

	/**
	 * Insere nome do Usuario Bolsista no Formulario
	 *
	 * @param $form, $nuBolsa
	 * 
	 * @return void
	 * 
	 * @author gustavo.gomes
	 */
	public function setUsuarioFormulario( $form, $nuBolsa ) {

		$obUsuario = new Fnde_Sice_Business_Usuario();
		$businessBolsa = new Fnde_Sice_Business_Bolsa();
		$bolsa = $businessBolsa->getBolsaById($nuBolsa);

		if ( $bolsa['NU_SEQ_USUARIO'] ) {
			$usuario = $obUsuario->getUsuarioById($bolsa['NU_SEQ_USUARIO']);
			$noUsuario = $usuario['NO_USUARIO'];
		}

		$form->setNoUsuarioForm($noUsuario);

	}

	/**
	 * Monta o formulário e renderiza a view.
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
	 * Método para gravar o motivo de inaptidão no banco de dados.
	 */
	public function salvarMotivoInaptidaoAction() {
		// Se os dados não foram enviados por post retorna para a index
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}

		$arParams = $this->_getAllParams();

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

		//Recupera os dados de pesquisa da tela de filtrar bolsas.
		$businessBolsa = new Fnde_Sice_Business_Bolsa();
		$bolsa = $businessBolsa->getBolsaById($arParams['NU_SEQ_BOLSA']);
		$bolsa['NU_SEQ_JUSTIF_INAPTIDAO'] = $arParams['CO_JUSTIFICATIVA_INAPTIDAO'];
		$bolsa['DS_OBSERVACAO_INAPTIDAO'] = $arParams['DS_OBSERVACAO'];
		$bolsa['ST_APTIDAO'] = 'I';
		$obBolsa = new Fnde_Sice_Model_Bolsa();

		//pega o usuario da sessão, o usuario que está logado
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = null;
		if ( $cpfUsuarioLogado ) {
			$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
		}

		try {
			$where = " NU_SEQ_BOLSA = " . $bolsa['NU_SEQ_BOLSA'];
			$obBolsa->fixDateToBr();
			if ( $obBolsa->update($bolsa, $where) ) {
				$historico['NU_SEQ_BOLSA'] = $bolsa['NU_SEQ_BOLSA'];
				$historico['NU_SEQ_USUARIO'] = $arUsuario['NU_SEQ_USUARIO'];
				$historico['NU_SEQ_JUSTIF_INAPTIDAO'] = $bolsa['NU_SEQ_JUSTIF_INAPTIDAO'];
				$historico['ST_BOLSA'] = $bolsa['ST_BOLSA'];
				$historico['DS_OBSERVACAO'] = $arParams['DS_OBSERVACAO'];
				$historico['DT_HISTORICO'] = date('d/m/Y G:i:s');
				$historico['ST_APTIDAO'] = 'I';
				$obModeloHis = new Fnde_Sice_Model_HistoricoBolsa();
				$obModeloHis->insert($historico);
			}
			$this->addMessage(Fnde_Message::MSG_SUCCESS, "Justificativa inserida com sucesso.");
			$this->addInstantMessage(Fnde_Message::MSG_SUCCESS, "FECHAR_POPUP");
		} catch ( Exception $e ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
		}

		$this->view->form = $form;
		return $this->render('form');
	}
}
