<?php

/**
 * Form de avaliar bolsas
 *
 * @author diego.matos
 * @since 04/07/2012
 */
class DetalhesBolsista_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Detalhes do Bolsista.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 04/07/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		//Adicionando elementos no formulário
		$this->addElement(new Html("htmlBolsista"));
		$this->addElement(new Html("htmlTurmasAvaliadas"));

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "$.alerts._hide()"));

		//Adicionado Componentes no formulário
		$this->addElements(array($btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		return $this;
	}
}
