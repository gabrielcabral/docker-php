<?php

/**
 * Form MotivoCancelamento
 * 
 * @author Izabel Rodrigues
 * @since 13/07/2012
 */
class MotivoDevolucaoAvaliacao_Form extends Fnde_Form {

	/**
	 * Construtor do formulário da tela de Motivo de Devolução.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		$this->addElement(new Html("hiddenBolsa"));

		//Adicionando elementos no formulário
		$dsBolsista = $this->createElement('text', 'NO_USUARIO',
				array("label" => "Bolsista: ", "readonly" => "readonly", 'size' => '40'));
		$dsBolsista->setValue($arDados['NO_USUARIO']);

		$coMotivoDevolucaoAvaliacao = $this->createElement('select', 'CO_JUSTIFICATIVA_DEVOLUCAO',
				array('name' => 'CO_JUSTIFICATIVA_DEVOLUCAO', 'label' => 'Justificativa: '));
		$coMotivoDevolucaoAvaliacao->addMultioption(null, "Selecione");
		$coMotivoDevolucaoAvaliacao->setRequired(true);

		//fim bloco a ser modificado
		$dsObservacao = $this->createElement('textarea', 'DS_OBSERVACAO',
				array("label" => "Observação: ", 'rows' => '3', 'maxlength' => '200'));

		$this->addElements(array($dsBolsista, $coMotivoDevolucaoAvaliacao, $dsObservacao,));

		$this->addDisplayGroup(array("NO_USUARIO", "CO_JUSTIFICATIVA_DEVOLUCAO", "DS_OBSERVACAO"),
				'justificativadevolucaoavalicao', array("legend" => "Justificativa"));

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
	 * Adicionar as opções do combo de justificativa
	 * @param array $rsJustificativa
	 */
	public function setJustificativa( $rsJustificativa ) {
		$this->getElement("CO_JUSTIFICATIVA_DEVOLUCAO")->addMultioptions($rsJustificativa);
	}

}
