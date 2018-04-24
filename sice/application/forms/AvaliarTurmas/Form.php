<?php

/**
 * Form de avaliar turmas
 *
 * @author rafael.paiva
 * @since 27/06/2012
 */
class AvaliarTurmas_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Avaliação de Turmas.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author rafael.paiva
	 * @since 27/06/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$this->addElement(new Html("htmlOrientacao"));

		$nuSeqBolsa = $this->createElement('hidden', 'NU_SEQ_BOLSA');
		$nuSeqBolsa->setValue($arDados['NU_SEQ_BOLSA']);

		//Quantidade de turmas para avaliação, para saber se avaliou todas as turmas.
		$qtTurmas = $this->createElement('hidden', 'QT_TURMAS');

		$this->addElements(array($nuSeqBolsa, $qtTurmas));
		$this->addElement(new Html("htmlTutor"));
		$this->addElement(new Html("htmlTurmas"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "mensagem" => "", "class" => "btnConfirmar",
						"title" => "Confirmar"));

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

		return $this;
	}

}
