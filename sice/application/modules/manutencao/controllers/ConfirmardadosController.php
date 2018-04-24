<?php

/**
 * Controller do Confirmar Dados
 *
 * @author rafael.paiva
 * @since 06/09/2012
 */

class Manutencao_ConfirmarDadosController extends Fnde_Sice_Controller_Action {
	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 06/09/2012
	 */
	public function formAction() {
		if ( !Fnde_Sice_Business_Componentes::permitirAcesso() ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
			$this->_redirect('index');
		}

		$this->setTitle('Acesso');
		$this->setSubtitle('Confirmar dados');

		//Recupera o objeto de formulário para validação
		$form = $this->getForm();

		$params = $this->_getAllParams();

		if ( $this->_request->isPost() ) {
			if ( $form->isValid($params) && $this->confirmarDados($form) ) {
				//Redireciona para a tela de alterar senha.
				$this->_redirect("/manutencao/alterarsenha/form");
			} else {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
				$this->addInstantMessage(Fnde_Message::MSG_ERROR,
						Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			}
		}

		$this->view->form = $form;
	}

	/**
	 * Retorna o formulario
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 06/09/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {
		$form = new ConfirmarDados_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/confirmardados/form')->setMethod('post');
		return $form;
	}

	/**
	 * Verifica se os dados informados estão corretos.
	 */
	public function confirmarDados( $form ) {
		$usuarioBusiness = new Fnde_Sice_Business_Usuario;

		$params = $this->_getAllParams();
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$dadosUsuario = $usuarioBusiness->getUsuarioByCpf($usuarioLogado->cpf);

		$flag = true;

		//Validando E2. Se o CPF informado é diferente ao CPF do usuário logado.
		if ( trim(preg_replace('/[^0-9]/', '', $params['NU_CPF'])) != $usuarioLogado->cpf ) {
			$form->getElement("NU_CPF")->addError(
					"O CPF informado é diferente do CPF do usuário autenticado no sistema.");
			$flag = false;
		}

		//Validando E3. Se a Data de Nascimento informada é diferente da DT do usuário logado.
		$dtNascimento = new Zend_Date($dadosUsuario['DT_NASCIMENTO'], 'dd/MM/YY');
		if ( $params['DT_NASCIMENTO'] != $dtNascimento->toString('dd/MM/YYYY') ) {
			$form->getElement("DT_NASCIMENTO")->addError(
					"A data de nascimento é diferente da data de nascimento do usuário autenticado no sistema.");
			$flag = false;
		}

		//Validando E4. Se o email informado é diferente do email do usuário logado.
		if ( $params['DS_EMAIL_USUARIO'] != $usuarioLogado->email ) {
			$form->getElement("DS_EMAIL_USUARIO")->addError(
					"O e-mail informado é diferente do e-mail do usuário autenticado no sistema.");
			$flag = false;
		}

		return $flag;
	}

}
