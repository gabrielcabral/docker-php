<?php

/**
 * Form de Filtro TipoCurso
 * 
 * @author diego.matos
 * @since 30/03/2012
 */
class TipoCurso_FormFilter extends Fnde_Base_Form {
	/**
	 * Construtor do formulário de pesquisa da tela de Tipo de Curso.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$nudsTipoCurso = $this->createElement('text', 'DS_TIPO_CURSO', array("label" => "dsTipoCurso: "));

		// Adiciona os elementos ao formulário
		$this->addElements(array($nudsTipoCurso,));

		$this->addDisplayGroup(array('DS_TIPO_CURSO',), 'dadostipocurso', array("legend" => "Filtro TipoCurso"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/tipocurso/clear-search/'"));

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
