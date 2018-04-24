<?php

/**
 * Form de cadastro VincCursistaTurma
 * 
 * @author vinicius.cancado
 * @since 10/09/2012
 */
class NotificarCursista_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Notificação do Cursista.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author viniciuscançado
	 * @since 13/09/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no fo,rmulário
		$nuTurma = $this->createElement('hidden', 'NU_SEQ_TURMA');

		//$this->setAttrib("class", "labelLongo");

		//Adicionando elementos no formulário
		$this->addElement(new Html("htmlTurma"));

		// Adiciona os elementos ao formulário

		$btConfirmar = $this->createElement('button', 'confirmarNotificacao',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/secretaria/notificarcursista/notificarcursista/" . "NU_SEQ_TURMA/'"
								. " + $('#NU_SEQ_TURMA').val()"));

		//Adicionado Componentes no formulário
		$this->addElements(array($nuTurma, $btConfirmar, $btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('confirmarNotificacao', 'cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));
		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		return $this;

	}
}
