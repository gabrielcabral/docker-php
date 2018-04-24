<?php

/**
 * Form de Filtro Usuario
 *
 */
class Usuario_FormHistoricoTermos extends Fnde_Form {

	/**
	 * Construtor do formul�rio de Historico dos termos de compromisso
	 * @return object - objeto do formulario
	 */

	public function __construct() {

		//adiciona elementos no formulario
		$this->addElement(new Html('htmlDadosUsuario'));
		$this->addElement(new Html('htmlDadosTermos'));

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar",
						'title'=>'Cancelar',
						'onClick' => "window.location='" . $this->getView()->baseUrl()
						. "/index.php/manutencao/usuario/list/'"));

		//Adicionado Componentes no formul�rio
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

