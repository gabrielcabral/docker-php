<?php

/**
 * Form de Filtro EmitirRelatorios
 *
 * @author diego.matos
 * @since 19/09/2012
 */
class EmitirRelatorios_FormFilter extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Emitir Relatórios.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 19/09/2012
	 */
	public function __construct( $arDados, $arExtra = null ) {

		$uf = $this->createElement('select', 'SG_UF', array("label" => "UF: "));
		$uf->addMultiOption(null, 'Selecione');

		// Adiciona os elementos ao formulário
		$this->addElements(array($uf,));

		$this->addDisplayGroup(array("SG_UF"), 'filtroRelatorios', array("legend" => "Filtro"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/relatorios/emitirrelatorios/clear-search/'"));

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

	/**
	 * Método para inserir as UFs no select
	 * @author gustavo.gomes
	 * @param array $result
	 */
	public function setUf( $result ) {

		$uf = $this->getElement('SG_UF');

		for ( $i = 0; $i < count($result); $i++ ) {
			$uf->addMultiOption($result[$i]['SG_UF'], $result[$i]['SG_UF']);
		}

	}

}
