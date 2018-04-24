<?php

/**
 * Controller do Alterar Senha
 *
 * @author rafael.paiva
 * @since 06/09/2012
 */

class Manutencao_AlterarSenhaController extends Fnde_Sice_Controller_Action {
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
		$this->setSubtitle('Alterar Senha');

		//Recupera o objeto de formulário para validação
		$form = $this->getForm();

		$params = $this->_getAllParams();

		if ( $this->_request->isPost() ) {
			if ( $form->isValid($params) && $this->validaDados($form) ) {
				$this->alterarSenha();
			} else {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
				$this->addInstantMessage(Fnde_Message::MSG_ERROR,
						Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			}
		}

		//Montando grid Dados do Usuário
		$form->getElement('htmlUsuario')->setValue($this->retornaHtmlUsuario());

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
		$form = new AlterarSenha_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/alterarsenha/form')->setMethod('post');

		return $form;
	}

	/**
	 * Valida os dados do formulário de acordo com as regras de negocios.
	 * @param form $form Formulario para validacao. 
	 * 
	 * @author rafael.paiva
	 */
	public function validaDados( $form ) {
		$segweb = new Fnde_Model_Segweb;
		$params = $this->_getAllParams();

		try {
			//Validando E5. Se a senha atual é diferente da senha do usuario autenticado.
			$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
			$result = $segweb->authenticate($usuarioLogado->username, $params['SENHA_ATUAL']);
			if ( $result['result'] != 1 ) {
				$form->getElement('SENHA_ATUAL')->addError(
						"A senha atual informada é diferente da senha atual do usuário autenticado no sistema.");
				return false;
			}

			return true;
		} catch ( Exception $e ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			return false;
		}
	}

	/**
	 * Altera a senha do usuario no SEGWEB.
	 * 
	 * @author rafael.paiva
	 */
	public function alterarSenha() {
		$segweb = new Fnde_Model_Segweb;
		$usuarioLogado = (array) Zend_Auth::getInstance()->getIdentity();

		try {
			//Parametros SEGWEB.
			$nuSeqUsuario = $usuarioLogado['nu_seq_usuario'];

			//Alterando a senha do usuario no SEGWEB.
			$result = $segweb->setNovaSenha($nuSeqUsuario);

			if ( $result['result'] == 1 ) {
				$this->addMessage(Fnde_Message::MSG_SUCCESS,
						"Senha alterada com sucesso. A nova senha foi encaminhada para o e-mail: {$usuarioLogado['email']}.");
				//Redireciona para a tela de confirmar dados.
				$this->_redirect("/manutencao/confirmardados/form");
			} else {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, $result['message']['text']);
			}
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Retorna o HTML da grid com os dados do usuário
	 * 
	 * @author rafael.paiva
	 */
	public function retornaHtmlUsuario() {
		$usuarioBusiness = new Fnde_Sice_Business_Usuario;
		$mesorregiaoBusiness = new Fnde_Sice_Business_MesoRegiao();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$dadosUsuario = $usuarioBusiness->getUsuarioByCpf($usuarioLogado->cpf);

		$mesorregiao = $mesorregiaoBusiness->getMesoRegiaoById($dadosUsuario['CO_MESORREGIAO']);
		$municipio = $mesorregiaoBusiness->getMunicipioById($dadosUsuario['CO_MUNICIPIO_PERFIL']);

		$html .= "<div class='listagem' style='display:inline'>";
		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";
		$html .= "<caption><i>Dados do Usuário</i></caption>";
		$html .= "</table>";

		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";

		$html .= "<tr class='alt'>";
		$html .= "<td style='background-color:#E6EFF4; width= 300px;'>";
		$html .= "CPF";
		$html .= "</td>";
		$html .= "<td style='width:300px;'>";
		$html .= "<b>" . Fnde_Sice_Business_Componentes::formataCpf($dadosUsuario['NU_CPF']) . "</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4;width:300px;'>";
		$html .= "Situação";
		$html .= "</td>";
		$html .= "<td style='width:300px;'><b>";
		$html .= ( $dadosUsuario['ST_USUARIO'] == 'A' ? 'Ativo'
				: ( $dadosUsuario['ST_USUARIO'] == 'D' ? 'Inativo'
						: ( $dadosUsuario['ST_USUARIO'] == 'L' ? 'Liberação Pendente' : null ) ) );
		$html .= "</b></td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Nome";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$dadosUsuario['NO_USUARIO']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4' >";
		$html .= "UF";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$dadosUsuario['SG_UF_ATUACAO_PERFIL']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Mesorregião";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$mesorregiao['NO_MESO_REGIAO']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4' >";
		$html .= "Município";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$municipio[0]['NO_MUNICIPIO']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "</table>";

		$html .= "</div><br/>";

		return $html;
	}
}
