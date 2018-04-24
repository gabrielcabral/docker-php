<?php

/**
 * Form de cadastro QuantidadeTurma
 * 
 * @author diego.matos
 * @since 30/03/2012
 */
class QuantidadeTurma_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Quantidade de Turma.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		$nuConfiguracao = $this->createElement('hidden', 'NU_SEQ_CONFIGURACAO');
		$nuConfiguracao->setValue($arDados['NU_SEQ_CONFIGURACAO']);
		$v = $this->createElement('hidden', 'v');
		$v->setValue($arDados['v']);

		$nucoRegiao = $this->createElement('select', 'SG_REGIAO', array("label" => "Região : "))->setRequired(true);

		$sgRegiao = $this->createElement('hidden', 'sg_regiao');

		$sgRegiao->setValue($arDados['SG_REGIAO']);

		// Adiciona os elementos ao formulário
		$this->addElements(array($nuConfiguracao, $v, $nucoRegiao, $sgRegiao));

		$this->addDisplayGroup(array('NU_SEQ_CONFIGURACAO', 'v', 'NU_SEQ_QUANTIDADE_TURMA', 'SG_REGIAO',),
				'dadosquantidadeturma', array("legend" => "Mesorregião"));

		$this->addElement(new Html("gridQuantidadeTurmas"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar",
						"title" => "Confirmar",
						'onClick' => "this.form.action='" . $this->getView()->baseUrl()
								. '/index.php/manutencao/quantidadeturma/salvar-quantidade-turma'));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/configuracao/list/'"));

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

	/**
	 * Método para setar região no combo
	 */
	public function setRegiao( $results, $regiaoAtual ) {

		$nucoRegiao = $this->getElement('SG_REGIAO');
		$nucoRegiao->setMultiOptions(array(null => "Selecione"));
		for ( $i = 0; $i < count($results); $i++ ) {
			$nucoRegiao->addMultiOption($results[$i]['SG_REGIAO'], $results[$i]['NO_REGIAO']);
		}
		$nucoRegiao->setValue($regiaoAtual);

	}

}
