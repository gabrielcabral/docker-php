<?php

/**
 * Form de cadastro SolicitarCancelamento
 * 
 * @author poliane.silva
 * @since 17/05/2012
 */
class MotivoInaptidao_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Motivo de Inaptidão do Bolsista.
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

		$indice = $this->createElement('hidden', 'INDICE');
		$indice->setValue($arDados['INDICE']);

		$dsBolsista = $this->createElement('text', 'NO_USUARIO',
				array("label" => "Bolsista: ", "readonly" => "readonly", 'size' => '40'));

		$coJustificativa = $this->createElement('select', 'CO_JUSTIFICATIVA_INAPTIDAO',
				array('name' => 'CO_JUSTIFICATIVA_INAPTIDAO', 'label' => 'Justificativa: '));
		$coJustificativa->addMultiOption(null, "Selecione");
		$coJustificativa->setRequired();

		$dsObservacao = $this->createElement('textarea', 'DS_OBSERVACAO',
				array("label" => "Observação: ", 'rows' => '3', 'maxlength' => '200'));

		$this->addElements(array($nuSeqBolsa, $dsBolsista, $coJustificativa, $dsObservacao, $indice,));

		$this->addDisplayGroup(array("NO_USUARIO", "CO_JUSTIFICATIVA_INAPTIDAO", "DS_OBSERVACAO"),
				'justificativacancelamento', array("legend" => "Justificativa Inaptidão"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "$.alerts._hide();limparAvaliacao({$arDados['INDICE']});"));

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
	 * Insere nome do Usuario Bolsista no Formulario
	 *
	 * @param $noUsuario
	 *
	 * @return void
	 * 
	 * @author gustavo.gomes
	 */
	public function setNoUsuarioForm( $noUsuario ) {

		$bolsista = $this->getElement('NO_USUARIO');
		$bolsista->setValue($noUsuario);

	}

	/**
	 * Insere o conteúdo da justificativa no Formulario
	 *
	 * @param $noUsuario
	 *
	 * @return void
	 *
	 * @author gustavo.gomes
	 */
	public function setJustificativaForm( $rsJustificativa ) {

		$this->getElement('CO_JUSTIFICATIVA_INAPTIDAO')->addMultiOptions($rsJustificativa);

	}

}
