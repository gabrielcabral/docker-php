<?php

/**
 * Form de cadastro FormacaoAcademica
 * 
 * @author diego.matos
 * @since 10/04/2012
 */
class FormacaoAcademica_Form extends Fnde_Form {
	/**
	 * Construtor do formulário do modal de Formação Acadêmica.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário

		$this->setAttrib("id", "formFormacao");

		$nutpEscolaridade = $this->createElement('select', 'TP_ESCOLARIDADE',
				array('name' => 'TP_ESCOLARIDADE', 'label' => 'Escolaridade: ', $arDados['TP_ESCOLARIDADE']));

		$nutpEscolaridade->setRequired(true);

		$nutpEscolaridade->addMultiOption(null, "Selecione");

		$nutpInstituicao = $this->createElement('radio', 'TP_INSTITUICAO', array("label" => "Tipo de Instituição: "));
		$nutpInstituicao->setRequired(true);
		$nutpInstituicao->addMultiOption("1", "Pública");
		$nutpInstituicao->addMultiOption("2", "Privada");
		$nutpInstituicao->addMultiOption("3", "Comunitária");

		$nunoInstituicao = $this->createElement('text', 'NO_INSTITUICAO',
				array("label" => "Instituição: ", "maxlength" => "80"));
		$nunoInstituicao->setRequired(true);

		$nunoCurso = $this->createElement('text', 'NO_CURSO', array("label" => "Curso: ", "maxlength" => "80"));
		$nunoCurso->setRequired(true);

		$nudtConclusao = $this->createElement('text', 'DT_CONCLUSAO',
				array("label" => "Data da Conclusão: ", "class" => "date dp-applied"));
		$nudtConclusao->setRequired(true);

		// Adiciona os elementos ao formulário
		$this->addElements(array($nutpEscolaridade, $nutpInstituicao, $nunoInstituicao, $nunoCurso, $nudtConclusao));

		$obDisplayGroupCampos = $this->addDisplayGroup(
				array('TP_ESCOLARIDADE', 'TP_INSTITUICAO', 'NO_INSTITUICAO', 'NO_CURSO', 'DT_CONCLUSAO',),
				'dadosformacaoacademica');

		$btConfirmar = $this->createElement('button', 'confirmarFormacao',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelarFormacao',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "$.alerts._hide()"));

		//Adicionado Componentes no formulário
		$this->addElements(array($btConfirmar, $btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('confirmarFormacao', 'cancelarFormacao'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');
		$obDisplayGroupCampos->dadosformacaoacademica->removeDecorator('fieldset');
		$nutpInstituicao->addDecorator('HtmlTag', array('tag' => 'fieldset ', 'class' => 'agrupador inLine'));

		return $this;
	}

	/**
	 * Insere os valores no select de Escolaridade
	 * @author gustavo.gomes
	 * @param array $rsEscolaridade
	 */
	public function setEscolaridade( $rsEscolaridade ) {

		$this->getElement('TP_ESCOLARIDADE')->addMultiOptions($rsEscolaridade);

	}
}
