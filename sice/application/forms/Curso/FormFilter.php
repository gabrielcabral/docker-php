<?php

/**
 * Form de Filtro Curso
 * 
 * @author vinicius.cancado
 * @since 18/04/2012
 */
class Curso_FormFilter extends Fnde_Form {
	/**
	 * Construtor do formulário de pesquisa da tela de Curso. 
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		//Adicionando elementos no formulário		

		$nunuSeqTipoCurso = $this->createElement('select', 'NU_SEQ_TIPO_CURSO',
				array('name' => 'NU_SEQ_TIPO_CURSO', 'label' => 'Tipo de curso: ', $arDados['NU_SEQ_TIPO_CURSO']));

		$nunuSeqTipoCurso->addMultiOption(null, "Selecione");
		$nunuSeqTipoCurso->setRequired(true);

		$nudsSiglaCurso = $this->createElement('text', 'DS_SIGLA_CURSO', array("label" => "Sigla curso: "));
		$nudsSiglaCurso->setAttrib('maxlength', '15');
		$nudsNomeCurso = $this->createElement('text', 'DS_NOME_CURSO', array("label" => "Nome do curso: "));
		$nudsNomeCurso->setAttrib('maxlength', '150');
		$nudsNomeCurso->setAttrib('size', '100');

		$situacao = array("A" => "Ativo", "D" => "Inativo");

		$stSituacaoCurso = $this->createElement('select', 'ST_CURSO',
				array('name' => 'ST_CURSO', 'label' => 'Situação:'));
		$stSituacaoCurso->addMultiOption(null, 'Selecione');
		$stSituacaoCurso->addMultiOptions($situacao);

		// Adiciona os elementos ao formulário
		$this->addElements(array($nunuSeqTipoCurso, $nudsSiglaCurso, $nudsNomeCurso, $stSituacaoCurso));

		$this->addDisplayGroup(array('NU_SEQ_TIPO_CURSO', 'DS_SIGLA_CURSO', 'DS_NOME_CURSO', 'ST_CURSO'), 'Filtro',
				array("legend" => "Filtro"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/curso/clear-search/'"));

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

	public function setTipoCurso( $rsTipoCurso ) {
		$this->getElement('NU_SEQ_TIPO_CURSO')->addMultiOptions($rsTipoCurso);
	}
}
