<?php

/**
 * Form de cadastro Regiao
 * 
 * @author tiago.ramos
 * @since 03/04/2012
 */
class Regiao_Form extends Fnde_Base_Form {
	/**
	 * Construtor do formulário da tela de Região.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$nuRegiao = $this->createElement('hidden', 'SG_REGIAO');

		$nunoRegiao = $this->createElement('text', 'NO_REGIAO', array("label" => "noRegiao: "));
		$nucoRegiaoSiafi = $this->createElement('text', 'CO_REGIAO_SIAFI', array("label" => "coRegiaoSiafi: "));
		$nucoRegiaoIbge = $this->createElement('text', 'CO_REGIAO_IBGE', array("label" => "coRegiaoIbge: "));

		// Adiciona os elementos ao formulário
		$this->addElements(array($nuRegiao, $nunoRegiao, $nucoRegiaoSiafi, $nucoRegiaoIbge,));

		$this->addDisplayGroup(array('SG_REGIAO', 'NO_REGIAO', 'CO_REGIAO_SIAFI', 'CO_REGIAO_IBGE',), 'dadosregiao',
				array("legend" => "Dados Regiao"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/regiao/list/'"));

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
