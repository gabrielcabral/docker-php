<?php

/**
 * Form de avaliar bolsas
 *
 * @author poliane.silva	
 * @since 04/07/2012
 */
class EnviarSgb_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Envio da Bolsa ao SGB.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author poliane.silva	
	 * @since 04/07/2012
	 */
	public function __construct( $arDados ) {

		//$this->setAttrib("class", "labelLongo");
		//Adicionando elementos no formulário

		$nuSeqBolsa = $this->createElement('hidden', 'NU_SEQ_BOLSA_URL');
		$nuSeqBolsa->setValue($arDados['NU_SEQ_BOLSA']);
		$this->addElement($nuSeqBolsa);

		$this->addElement(new Html("htmlPeriodo"));
		$this->addElement(new Html("htmlBolsistas"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "mensagem" => "", "class" => "btnConfirmar",
						"title" => "Confirmar"));

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/financeiro/bolsa/list/'"));

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
