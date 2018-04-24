<?php

/**
 * Form de cadastro Configuracao
 * 
 * @author diego.matos
 * @since 30/03/2012
 */
class Configuracao_FormMesoregiao extends Fnde_Base_Form {
	/**
	 * Construtor do formul�rio da tela de Mesorregi�o (complementar ao de Configura��o).
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formul�rio
		$nuQuantidadeTurma = $this->createElement('hidden', 'NU_SEQ_QUANTIDADE_TURMA');
		$v = $this->createElement('hidden', 'v');

		$nucoMesorregiao = $this->createElement('select', 'CO_MESORREGIAO', array("label" => "Regi�o : "));
		$nucoMesorregiao->setRequired(true);

		// Adiciona os elementos ao formul�rio
		$this->addElements(array($nuQuantidadeTurma, $v, $nucoMesorregiao));

		$this->addDisplayGroup(array('NU_SEQ_QUANTIDADE_TURMA', 'v', 'CO_MESORREGIAO', 'NU_SEQ_CONFIGURACAO',),
				'dadosquantidadeturma', array("legend" => "Dados QuantidadeTurma"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/quantidadeturma/list/'"));

		//Adicionado Componentes no formul�rio
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
