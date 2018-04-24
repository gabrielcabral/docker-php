<?php

/**
 * Form de Filtro Usuario
 *
 * @author poliane.silva
 * @since 29/03/2014
 */
class Usuario_FormHistoricoPerfil extends Fnde_Form {

	/**
	 * Construtor do formulário de Historico do perfil
	 * @return object - objeto do formulario
	 * @author poliane.silva
	 * @since 29/03/2014
	 */

	public function __construct() {

		//adiciona elementos no formulario
		$this->addElement(new Html('htmlDadosUsuario'));
		$this->addElement(new Html('htmlDadosPerfil'));
		$this->addElement(new Html('htmlDadosHistUsuario'));
		
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar",
						'title'=>'Cancelar',
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

