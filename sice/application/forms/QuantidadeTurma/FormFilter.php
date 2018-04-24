<?php

/**
 * Form de Filtro QuantidadeTurma
 * 
 * @author diego.matos
 * @since 30/03/2012
 */
class QuantidadeTurma_FormFilter extends Fnde_Base_Form {
	/**
	 * Construtor do formulário de pesquisa da tela de Quantidade de Turma. 
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		//Adicionando elementos no formulário
		$nucoMesorregiao = $this->createElement('text', 'CO_MESORREGIAO', array("label" => "coMesorregiao: "));
		$nuSConfiguracao = $this->createElement('text', 'NU_SEQ_CONFIGURACAO', array("label" => "SConfiguracao: "));

		$nuqtTurmas = $this->createElement('text', 'QT_TURMAS', array("label" => "qtTurmas: "));
		$nucoRegiao = $this->createElement('text', 'CO_REGIAO', array("label" => "coRegiao: "));

		// Adiciona os elementos ao formulário
		$this->addElements(array($nucoMesorregiao, $nuSConfiguracao, $nuqtTurmas, $nucoRegiao,));

		$this->addDisplayGroup(array('CO_MESORREGIAO', 'v', 'NU_SEQ_CONFIGURACAO', 'QT_TURMAS', 'CO_REGIAO',),
				'dadosquantidadeturma', array("legend" => "Filtro QuantidadeTurma"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/quantidadeturma/clear-search/'"));

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
