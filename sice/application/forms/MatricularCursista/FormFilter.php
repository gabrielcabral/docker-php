<?php

/**
 * Form de Filtro Matricular Cursista
 * 
 * @author diego.matos
 * @since 03/05/2012
 */
class MatricularCursista_FormFilter extends Fnde_Base_Form {
	/**
	 * Construtor do formul�rio de pesqusia da tela de Matr�cula de Cursistas na Turma. 
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author diego.matos
	 * @since 03/05/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formul�rio
		$this->addElement(new Html("htmlTurma"));

		$btnAdicionar = $this->createElement('button', 'btnAdicionar',
				array("label" => "Adicionar", "value" => "Adicionar", "class" => "btAddCursista",
						"title" => "Adicionar",
						"href" => $this->getView()->baseUrl() . "/index.php/manutencao/formacaoacademica/form/"));

		// Adiciona os elementos ao formul�rio
		$this->addElements(array($btnAdicionar,));

		$this->addDisplayGroup(array('btnAdicionar',), 'adicionar', array("legend" => "Cursistas matriculados"));

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

		return $this;
	}

}
