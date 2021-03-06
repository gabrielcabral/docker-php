<?php

/**
 * Form de Filtro VincCursistaTurma
 * 
 * @author rafael.paiva
 * @since 07/05/2012
 */
class VincCursistaTurma_FormFilter extends Fnde_Base_Form {
	/**
	 * Construtor do formulário de pesquisa da tela de Matricular Cursista.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 03/05/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		$this->setAttrib("class", "labelLongo");

		//Adicionando elementos no formulário
		$this->addElement(new Html("htmlTurma"));

		$btnAdicionar = $this->createElement('button', 'btnAdicionar',
				array("label" => "Adicionar", "value" => "Adicionar", "class" => "btAddCursista",
						"title" => "Adicionar",
						"href" => $this->getView()->baseUrl() . "/index.php/secretaria/cadastrarcursista/form/"));

		// Adiciona os elementos ao formulário
		$this->addElements(array($btnAdicionar,));

		$this->addDisplayGroup(array('btnAdicionar',), 'adicionar', array("legend" => "Cursistas matriculados"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/secretaria/turma/list/clear-search/'"));

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
