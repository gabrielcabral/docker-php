<?php

/**
 * Form de cadastro AlterarSenha
 * 
 * @author rafael.paiva
 * @since 06/09/2012
 */
class AlterarSenha_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Alteração de Senha.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author rafael.paiva
	 * @since 06/09/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		$this->setAttrib('class', 'labelLongo');

		//Html onde vai ser montado a grid com as informacoes do usuario.
		$this->addElement(new Html("htmlUsuario"));

		//Criando os elementos.
		$senhaAtual = $this->createElement('password', 'SENHA_ATUAL',
				array("label" => "Senha atual: ", "maxlength" => "8"));
		$senhaAtual->setRequired(true);

		//Adicionando os elementos ao formulário.
		$this->addElements(array($senhaAtual,));

		$this->addDisplayGroup(array("SENHA_ATUAL",), 'dadosAvaliacao', array("legend" => "Dados de Alteração"));

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
