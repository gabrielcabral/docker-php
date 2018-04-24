<?php

/**
 * Form de cadastro ConfirmarDados
 * 
 * @author diego.matos
 * @since 06/09/2012
 */
class ConfirmarDados_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Confirmação de Dados (Complementar ao Alterar Senha).
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 06/09/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		$this->setAttrib('class', 'labelLongo');

		//Adicionando elementos no formulário
		$nuCpf = $this->createElement('text', 'NU_CPF',
				array("label" => "CPF: ", 'value' => $arDados['NU_CPF'], "class" => "cpf"));
		$nuCpf->setRequired(true);

		$dtNascimentoUsuario = $this->createElement('text', 'DT_NASCIMENTO',
				array("label" => "Data de Nascimento: ", 'size' => '7'));
		$dtNascimentoUsuario->setRequired(true);

		$emailUsuario = $this->createElement('text', 'DS_EMAIL_USUARIO',
				array("label" => "E-mail: ", 'value' => $arDados['DS_EMAIL_USUARIO'], 'maxlength' => '60',
						'size' => '60'));
		$emailUsuario->setRequired(true);

		// Adiciona os elementos ao formulário.
		$this->addElements(array($nuCpf, $dtNascimentoUsuario, $emailUsuario));

		$this->addDisplayGroup(array("NU_CPF", "DT_NASCIMENTO", "DS_EMAIL_USUARIO",), 'dadosConfirmacao',
				array("legend" => "Dados de Confirmação"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						"onclick" => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/confirmardados/form/'"));

		//Adicionado Componentes no formulário
		$this->addElements(array($btConfirmar, $btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		return $this;
	}
}
