<?php

/**
 * Form de Filtro CriterioAvaliacao
 * 
 * @author diego.matos
 * @since 10/04/2012
 */
class CriterioAvaliacao_FormFilter extends Fnde_Base_Form {
	/**
	 * Construtor do formul�rio de pesquisa da tela de Crit�rio de Avalia��o.
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formul�rio
		$nudsSituacao = $this->createElement('text', 'DS_SITUACAO', array("label" => "dsSituacao: "));
		$nudsCriterioAvaliacao = $this->createElement('text', 'DS_CRITERIO_AVALIACAO',
				array("label" => "dsCriterioAvaliacao: "));

		// Adiciona os elementos ao formul�rio
		$this->addElements(array($nudsSituacao, $nudsCriterioAvaliacao,));

		$this->addDisplayGroup(array('DS_SITUACAO', 'DS_CRITERIO_AVALIACAO',), 'dadoscriterioavaliacao',
				array("legend" => "Filtro CriterioAvaliacao"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/criterioavaliacao/clear-search/'"));

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
