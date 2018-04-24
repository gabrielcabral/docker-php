<?php

/**
 * Form de cadastro CriterioAvaliacao
 * 
 * @author diego.matos
 * @since 10/04/2012
 */
class CriterioAvaliacao_Form extends Fnde_Base_Form {
	/**
	 * Construtor do formulário da tela de Critério de Avaliação. 
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$nuCriterioAvaliacao = $this->createElement('hidden', 'NU_SEQ_CRITERIO_AVAL');

		$nudsSituacao = $this->createElement('text', 'DS_SITUACAO', array("label" => "dsSituacao: "));
		$nudsSituacao->setRequired(true);
		$nudsCriterioAvaliacao = $this->createElement('text', 'DS_CRITERIO_AVALIACAO',
				array("label" => "dsCriterioAvaliacao: "));
		$nudsCriterioAvaliacao->setRequired(true);

		// Adiciona os elementos ao formulário
		$this->addElements(array($nuCriterioAvaliacao, $nudsSituacao, $nudsCriterioAvaliacao,));

		$this->addDisplayGroup(array('NU_SEQ_CRITERIO_AVAL', 'DS_SITUACAO', 'DS_CRITERIO_AVALIACAO',),
				'dadoscriterioavaliacao', array("legend" => "Dados CriterioAvaliacao"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/criterioavaliacao/list/'"));

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
