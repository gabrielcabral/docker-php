<?php

/**
 * Form de cadastro MesoRegiao
 * 
 * @author tiago.ramos
 * @since 03/04/2012
 */
class MesoRegiao_Form extends Fnde_Base_Form {
	/**
	 * Construtor do formulário da tela de Mesorregião.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$nuMesoRegiao = $this->createElement('hidden', 'CO_MESO_REGIAO');

		$nunoMesoRegiao = $this->createElement('text', 'NO_MESO_REGIAO', array("label" => "noMesoRegiao: "));
		$nucoMunicipioIbge = $this->createElement('text', 'CO_MUNICIPIO_IBGE', array("label" => "coMunicipioIbge: "));
		$nunoMunicipio = $this->createElement('text', 'NO_MUNICIPIO', array("label" => "noMunicipio: "));
		$nunoUf = $this->createElement('text', 'NO_UF', array("label" => "noUf: "));
		$nucoUf = $this->createElement('text', 'CO_UF', array("label" => "coUf: "));

		// Adiciona os elementos ao formulário
		$this->addElements(array($nuMesoRegiao, $nunoMesoRegiao, $nucoMunicipioIbge, $nunoMunicipio, $nunoUf, $nucoUf,));

		$this->addDisplayGroup(
				array('CO_MESO_REGIAO', 'NO_MESO_REGIAO', 'CO_MUNICIPIO_IBGE', 'NO_MUNICIPIO', 'NO_UF', 'CO_UF',),
				'dadosmesoregiao', array("legend" => "Dados MesoRegiao"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/mesoregiao/list/'"));

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
