<?php

/**
 * Form de Motivo de devolução
 *
 * @author poliane.silva
 * @since 11/07/2012
 */
class CoordenadorEstadualSgb_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da janela modal de seleção do Coordenador 
	 * Estadual para envio de bolsa ao SGB.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		$this->setAttrib("class", "labelLongo");
		//Adicionando elementos no formulário
		$usuarioCoordEst = $this->createElement('select', 'NU_SEQ_USUARIO',
				array('name' => 'NU_SEQ_USUARIO', 'label' => 'Coordenador estadual:', 'style' => 'width:300px'));
		$usuarioCoordEst->addMultiOption(null, 'Selecione');
		$usuarioCoordEst->setRequired(true);

		$this->addElements(array($usuarioCoordEst));

		$this->addDisplayGroup(array('NU_SEQ_USUARIO'), 'coordenadorestadualsgb');

		$btConfirmar = $this->createElement('submit', 'Confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
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
		$this->coordenadorestadualsgb->removeDecorator('fieldset');
		return $this;
	}

	/**
	 * Adiciona as opções do comobo de Coordenador Estadual
	 * @param array $arUsuarios
	 */
	public function setCoordenador( $arUsuarios ) {
		$this->getElement("NU_SEQ_USUARIO")->addMultiOptions($arUsuarios);
	}
}
