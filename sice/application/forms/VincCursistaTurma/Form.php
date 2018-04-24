<?php

/**
 * Form de cadastro VincCursistaTurma
 * 
 * @author rafael.paiva
 * @since 07/05/2012
 */
class VincCursistaTurma_Form extends Fnde_Form {
	/**
	 * Construtor do formul�rio da tela de Matricular Cursista
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author diego.matos
	 * @since 03/05/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formul�rio

		$this->setAttrib("class", "labelLongo");

		//Adicionando elementos no formul�rio
		$this->addElement(new Html("htmlTurma"));
		$this->addElement(new Html("htmlAlunosMatriculados"));

		$btnAdicionar = $this->createElement('button', 'btnAdicionar',
				array("label" => "Adicionar", "value" => "Adicionar", "class" => "btAddCursista btnAdicionar",
						"title" => "Adicionar",
                        "href" => $this->getView()->baseUrl()
                            . "/index.php/secretaria/cadastrarcursista/form/NU_SEQ_TURMA/"
                            . $arDados['NU_SEQ_TURMA']));

		// Adiciona os elementos ao formul�rio
		$this->addElements(array($btnAdicionar,));

		$this->addDisplayGroup(array('htmlAlunosMatriculados', 'btnAdicionar',), 'adicionar',
				array("legend" => "Cursistas matriculados"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/secretaria/turma/list/'"));

		//Adicionado Componentes no formul�rio
		$this->addElements(array($btConfirmar, $btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		$btnAdicionar->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'style' => 'text-align: right'));

		return $this;
	}

}