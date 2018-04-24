<?php

/**
 * Form de Filtro PeriodoVinculacao
 * 
 * @author vinicius.cancado
 * @since 18/04/2012
 */
class PeriodoVinculacao_FormFilter extends Fnde_Form {
	/**
	 * Construtor do formulário de pesquisa da tela de Período de Vinculação. 
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		$nunuSeqTipoPerfil = $this->createElement('select', 'NU_SEQ_TIPO_PERFIL',
				array('name' => 'NU_SEQ_TIPO_PERFIL', 'label' => 'Perfil: ', $arDados['NU_SEQ_TIPO_PERFIL']));

		$nunuSeqTipoPerfil->setRequired(true);

		// Adiciona os elementos ao formulário
		$this->addElements(array($nunuSeqTipoPerfil));

		$this->addDisplayGroup(array('NU_SEQ_TIPO_PERFIL'), 'dadosperiodovinculacao', array("legend" => "Filtro"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/periodovinculacao/clear-search/'"));

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
	 * Método para setar Tipo de Perfil no formulario
	 */
	public function setTipoPerfil( $rsTipoPerfil ) {

		$nunuSeqTipoPerfil = $this->getElement('NU_SEQ_TIPO_PERFIL');
		$nunuSeqTipoPerfil->addMultiOption(null, "Selecione");
		$nunuSeqTipoPerfil->addMultiOptions($rsTipoPerfil);

	}

}
