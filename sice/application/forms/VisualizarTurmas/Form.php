<?php

/**
 * Form de visualizar turmas
 *
 * @author rafael.paiva
 * @since 28/06/2012
 */
class VisualizarTurmas_Form extends Fnde_Form {
	/**
	 * Construtor do formul�rio de Visualizar Turmas.
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author rafael.paiva
	 * @since 28/06/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		//Adicionando elementos no formul�rio
		$this->addElement(new Html("htmlOrientacao"));
		$this->addElement(new Html("htmlBolsistas"));

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "$.alerts._hide()"));

		//Adicionado Componentes no formul�rio
		$this->addElement($btCancelar);

		$obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		return $this;
	}

}
