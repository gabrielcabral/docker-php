<?php

/**
 * Form de Filtro TipoPerfil
 * 
 * @author diego.matos
 * @since 10/04/2012
 */
class TipoPerfil_FormFilter extends Fnde_Base_Form {
	/**
	 * Construtor do formul�rio de pesquisa da tela de Tipo de Perfil. 
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formul�rio
		$nudsTipoPerfil = $this->createElement('text', 'DS_TIPO_PERFIL', array("label" => "dsTipoPerfil: "));

		// Adiciona os elementos ao formul�rio
		$this->addElements(array($nudsTipoPerfil,));

		$this->addDisplayGroup(array('DS_TIPO_PERFIL',), 'dadostipoperfil', array("legend" => "Filtro TipoPerfil"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/tipoperfil/clear-search/'"));

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
