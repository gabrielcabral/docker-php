<?php

/**
 * Form MotivoCancelamento
 * 
 * @author Izabel Rodrigues
 * @since 13/07/2012
 */
class MotivoCancelamento_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Motivo de Cancelamento de uma turma.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 */
	public function __construct( $arDados, $arExtra ) {

		$this->addElement(new Html("hiddenBolsa"));
		//Adicionando elementos no formulário
		$dsBolsista = $this->createElement('text', 'NO_USUARIO',
				array("label" => "Bolsista: ", "readonly" => "readonly", 'size' => '40'));
		$dsBolsista->setValue($arDados['NO_USUARIO']);

		$coMotivoCancelamento = $this->createElement('select', 'CO_JUSTIF_CANCELAMENTO',
				array('name' => 'CO_JUSTIF_CANCELAMENTO', 'label' => 'Justificativa: '));
		$coMotivoCancelamento->addMultiOption(null, 'Selecione');
		$coMotivoCancelamento->setRequired();

		$dsObservacao = $this->createElement('textarea', 'DS_OBSERVACAO',
				array("label" => "Observação: ", 'rows' => '3', 'maxlength' => '200'));

		$this->addElements(array($dsBolsista, $coMotivoCancelamento, $dsObservacao,));

		$this->addDisplayGroup(array("NO_USUARIO", "CO_JUSTIF_CANCELAMENTO", "DS_OBSERVACAO"),
				'justificativacancelamento', array("legend" => "Justificativa"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar",
						"mensagem" => "Deseja realmente solicitar cancelamento desta turma?",
						"class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "$.alerts._hide()"));

		//Adicionado Componentes no formulário
		$this->addElements(array($btConfirmar, $btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');
		$this->hiddenBolsa->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'hiddenBolsa', 'style' => 'display:none'));

		return $this;

	}

	/**
	 * Adicionar opções do combo de justificativa
	 * @param array $arMotivoCancelamento
	 */
	public function setJustificativa( $arMotivoCancelamento ) {
		$this->getElement('CO_JUSTIF_CANCELAMENTO')->addMultiOptions($arMotivoCancelamento);
	}
}
