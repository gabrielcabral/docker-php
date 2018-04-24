<?php

/**
 * Form de cadastro SolicitarAutorizacao
 * 
 * @author diego.matos
 * @since 22/06/2012
 */
class Aceite_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Termo de Aceite.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 22/06/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$this->addElement(new Html("htmlAceite"));

		$chkAceitacao = $this->createElement("multiCheckbox", "CK_ACEITACAO", array('class' => 'checkbox'));
		$str = " Li o texto e estou de acordo com o seu conteúdo.";
		$chkAceitacao->addMultiOption("1", $str);

		$this->addElements(array($chkAceitacao));

		$this->addDisplayGroup(array("htmlAceite", "CK_ACEITACAO"), 'termoAceite', array("legend" => "Termo de Aceite"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar",
						"disabled" => "disabled", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						"onClick" => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/financeiro/aceite/form/'"));
		//Adicionado Componentes no formulário
		$this->addElements(array($btConfirmar, $btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		$chkAceitacao->removeDecorator("HtmlTag");
		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		return $this;
	}
}
