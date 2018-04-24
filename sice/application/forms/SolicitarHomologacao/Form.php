<?php

/**
 * Form de avaliar bolsas
 *
 * @author diego.matos
 * @since 04/07/2012
 */
class SolicitarHomologacao_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Solicitar Homologação.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 04/07/2012
	 */
	public function __construct( $arDados) {

		$nuSeqUsuario = $this->createElement('hidden', 'NU_SEQ_BOLSA_URL');
		$nuSeqUsuario->setValue($arDados['NU_SEQ_BOLSA_URL']);

		$this->addElement($nuSeqUsuario);
		$this->addElement(new Html("htmlPeriodo"));
		$this->addElement(new Html("htmlBolsistas"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "mensagem" => "", "class" => "btnConfirmar",
						"title" => "Confirmar"));

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/financeiro/bolsa/list'"));

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
