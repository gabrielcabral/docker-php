<?php

/**
 * Form de cadastro Uf
 * 
 * @author tiago.ramos
 * @since 03/04/2012
 */
class Uf_Form extends Fnde_Base_Form {
	/**
	 * Construtor do formulário da tela de UF.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$nuUf = $this->createElement('hidden', 'SG_UF');

		$nucoUfSiafiBb = $this->createElement('text', 'CO_UF_SIAFI_BB', array("label" => "coUfSiafiBb: "));
		$nunoUf = $this->createElement('text', 'NO_UF', array("label" => "noUf: "));
		$nusgRegiao = $this->createElement('text', 'SG_REGIAO', array("label" => "sgRegiao: "));
		$nusgRegiao->setRequired(true);
		$nucoUfSiafi = $this->createElement('text', 'CO_UF_SIAFI', array("label" => "coUfSiafi: "));
		$nudsTratamento = $this->createElement('text', 'DS_TRATAMENTO', array("label" => "dsTratamento: "));
		$nucoUfInss = $this->createElement('text', 'CO_UF_INSS', array("label" => "coUfInss: "));
		$nucoUfIbge = $this->createElement('text', 'CO_UF_IBGE', array("label" => "coUfIbge: "));

		// Adiciona os elementos ao formulário
		$this->addElements(
				array($nuUf, $nucoUfSiafiBb, $nunoUf, $nusgRegiao, $nucoUfSiafi, $nudsTratamento, $nucoUfInss,
						$nucoUfIbge,));

		$this->addDisplayGroup(
				array('SG_UF', 'CO_UF_SIAFI_BB', 'NO_UF', 'SG_REGIAO', 'CO_UF_SIAFI', 'DS_TRATAMENTO', 'CO_UF_INSS',
						'CO_UF_IBGE',), 'dadosuf', array("legend" => "Dados Uf"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/uf/list/'"));

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
