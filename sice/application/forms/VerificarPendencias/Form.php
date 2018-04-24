<?php

/**
 * Form de avaliar bolsas
 *
 * @author rafael.paiva
 * @since 26/06/2012
 */
class VerificarPendencias_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Verificar Pendencias.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author rafael.paiva
	 * @since 26/06/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/financeiro/bolsa/list'"));

		//Adicionado Componentes no formulário
		$this->addElements(array($btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes',));

		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		return $this;
	}
}
