<?php

/**
 * Form de visualizar historico bolsa
 * 
 * @author fabiana.rose
 * @since 30/08/2012
 */

class VisualizaHistBolsa_Form extends Fnde_Form {

	/**
	 * Construtor do formulário de Historico da Bolsa
	 * @return object - objeto do formulario
	 * @author fabiana.rose
	 * @since 30/08/2012
	 */

	public function __construct( $arDados, $arExtra ) {

		//adiciona elementos no formulario
		$this->addElement(new Html('htmlDadosBolsista'));

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/usuario/list/'"));

		//Adicionado Componentes no formulário
		$this->addElements(array($btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		return $this;
	}

}