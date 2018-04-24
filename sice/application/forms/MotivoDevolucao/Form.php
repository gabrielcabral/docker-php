<?php

/**
 * Form de Motivo de devolução
 *
 * @author poliane.silva
 * @since 11/07/2012
 */
class MotivoDevolucao_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Motivo de Devolução.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		//Adicionando elementos no formulário
		$nuSeqBolsa = $this->createElement('hidden', 'NU_SEQ_BOLSA');
		$nuSeqBolsa->setValue($arDados['NU_SEQ_BOLSA']);

		$coJustificativa = $this->createElement('select', 'CO_JUSTIFICATIVA_DEVOLUCAO',
				array('name' => 'CO_JUSTIFICATIVA_DEVOLUCAO', 'label' => 'Justificativa: '));
		$coJustificativa->addMultiOption(null, 'Selecione');

		$coJustificativa->setRequired();

		$this->addElements(array($nuSeqBolsa, $coJustificativa));

		$this->addDisplayGroup(array('NU_SEQ_BOLSA_URL', "CO_JUSTIFICATIVA_DEVOLUCAO",), 'motivodevolucao');

		$btConfirmar = $this->createElement('submit', 'Confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", 
				//"mensagem"=>"Deseja realmente solicitar cancelamento desta turma?",
				"class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'Cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "$.alerts._hide()"));

		//Adicionado Componentes no formulário
		$this->addElements(array($btConfirmar, $btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('Confirmar', 'Cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');
		$this->motivodevolucao->removeDecorator('fieldset');
		return $this;
	}

	/**
	 * Adicionar opções no combo de justificativa
	 * @param array $rsJustificativa
	 */
	public function setJustificativa( $rsJustificativa ) {
		$this->getElement('CO_JUSTIFICATIVA_DEVOLUCAO')->addMultiOptions($rsJustificativa);
	}

}
