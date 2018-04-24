<?php

/**
 * Form de Filtro Modulo
 *
 * @author vinicius.cancado
 * @since 18/04/2012
 */
class Modulo_FormFilter extends Fnde_Form {
	/**
	 * Construtor do formulário de pesquisa da tela de Módulo.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		$nunuSeqTipoCurso = $this->createElement('select', 'NU_SEQ_TIPO_CURSO',
				array('name' => 'NU_SEQ_TIPO_CURSO', 'label' => 'Tipo de Curso: ', $arDados['NU_SEQ_TIPO_CURSO']));

		$nunuSeqTipoCurso->setRequired(true);

		$nudsSiglaCurso = $this->createElement('text', 'DS_SIGLA_MODULO', array("label" => "Sigla Módulo: "));
		$nudsSiglaCurso->setAttrib('maxlength', '15');
		$nudsNomeCurso = $this->createElement('text', 'DS_NOME_MODULO', array("label" => "Nome Módulo: "));
		$nudsNomeCurso->setAttrib('maxlength', '150');
		$nudsNomeCurso->setAttrib('size', '100');

		$situacao = array(null => "Selecione", "A" => "Ativo", "D" => "Inativo");

		$stSituacaoModulo = $this->createElement('select', 'ST_MODULO',
				array('name' => 'ST_MODULO', 'label' => 'Situação:'));

		$stSituacaoModulo->addMultiOptions($situacao);

		// Adiciona os elementos ao formulário
		$this->addElements(array($nunuSeqTipoCurso, $nudsSiglaCurso, $nudsNomeCurso, $stSituacaoModulo));

		$this->addDisplayGroup(array('NU_SEQ_TIPO_CURSO', 'DS_SIGLA_MODULO', 'DS_NOME_MODULO', 'ST_MODULO'), 'Filtro',
				array("legend" => "Filtro"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/modulo/clear-search/'"));

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
	 * Método para setar o tipo de curso no formulario
	 *
	 * @author gustavo.gomes
	 */
	public function setTipoCurso( $rsTipoCurso ) {

		$nuTipoCurso = $this->getElement(NU_SEQ_TIPO_CURSO)->addMultiOption(null, "Selecione");
		$nuTipoCurso->addMultiOptions($rsTipoCurso);

	}

}
