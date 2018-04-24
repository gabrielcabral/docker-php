<?php

/**
 * Form de avaliar bolsas
 *
 * @author rafael.paiva
 * @since 26/06/2012
 */
class AvaliarBolsas_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Avaliação de Bolsistas.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author rafael.paiva
	 * @since 26/06/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		//$this->setAttrib("class", "labelLongo");
		//Adicionando elementos no formulário

		$nuSeqBolsas = $this->createElement('hidden', 'NU_SEQ_BOLSA');
		$nuSeqBolsas->setValue($arDados['NU_SEQ_BOLSA']);

		$qtdBolsas = $this->createElement('hidden', 'QTD_BOLSAS');
		$qtdBolsas->setValue($arDados['QTD_BOLSAS']);

		$this->addElement($qtdBolsas);

		$this->addElement(new Html("htmlPeriodo"));
		$this->addElement(new Html("htmlBolsas"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "mensagem" => "", "class" => "btnConfirmar",
						"onclick" => "$(this.form).find(':disabled').attr('disabled',false);", "title" => "Confirmar"));

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/financeiro/avaliarbolsas/form'"));

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
