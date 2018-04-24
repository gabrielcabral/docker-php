<?php

/**
 * Form de cadastro Parametro
 * 
 * @author diego.matos
 * @since 23/04/2012
 */
class Parametro_Form extends Fnde_Base_Form {
	/**
	 * Construtor do formulário da tela de Parâmetro.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 23/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$nuParametro = $this->createElement('hidden', 'SIGLA_PARAMETRO');

		$nudsParametro = $this->createElement('text', 'DS_PARAMETRO', array("label" => "dsParametro: "));

		// Adiciona os elementos ao formulário
		$this->addElements(array($nuParametro, $nudsParametro,));

		$this->addDisplayGroup(array('SIGLA_PARAMETRO', 'DS_PARAMETRO',), 'dadosparametro',
				array("legend" => "Dados Parametro"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/parametro/list/'"));

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
