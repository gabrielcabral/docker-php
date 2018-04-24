<?php

/**
 * Form de cadastro HistoricoTurma
 * 
 * @author diego.matos
 * @since 25/04/2012
 */
class HistoricoTurma_Form extends Fnde_Form {
	/**
	 * Construtor do formul�rio da tela de Hist�rico da Turma.
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formul�rio
		$this->addElement(new Html("htmlTurma"));
		$this->addElement(new Html("htmlAlunosMatriculados"));
		$this->addElement(new Html("htmlHistorico"));

		// Adiciona os elementos ao formul�rio
		$this->addElements(array());

		$this->addDisplayGroup(array("htmlAlunosMatriculados"), 'dadosalunosmatriculados',
				array("legend" => "Cursistas Matriculados"));

		$this->addDisplayGroup(array("htmlHistorico"), 'dadoshistoricoturma', array("legend" => "Hist�rico"));

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/secretaria/turma/list/'"));

		//Adicionado Componentes no formul�rio
		$this->addElements(array($btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		return $this;
	}

}
