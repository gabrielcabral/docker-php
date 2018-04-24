<?php

/**
 * Form de cadastro SolicitarCancelamento
 * 
 * @author poliane.silva
 * @since 17/05/2012
 */
class SolicitarCancelamento_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Solicitar Cancelamento.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$nuTurma = $this->createElement('hidden', 'NU_SEQ_TURMA');
		$nuTurma->setValue($arDados['NU_SEQ_TURMA']);

		$nuMinAlunos = $this->createElement('hidden', 'NU_MIN_ALUNOS');
		$nuMinAlunos->setValue($arDados['NU_MIN_ALUNOS']);

		$nuAlunosMatriculados = $this->createElement('hidden', 'NU_ALUNOS_MATRICULADOS');
		$nuAlunosMatriculados->setValue($arDados['NU_ALUNOS_MATRICULADOS']);

		$this->addElement(new Html("htmlTurma"));
		$this->addElement(new Html("htmlAlunosMatriculados"));

		// Adiciona os elementos ao formulário.
		$this->addElements(array($nuTurma, $nuMinAlunos, $nuAlunosMatriculados));

		$this->addDisplayGroup(array("htmlAlunosMatriculados",), 'dadosalunosmatriculados',
				array("legend" => "Cursistas matriculados"));

		$coMotivoAlteracao = $this->createElement('select', 'CO_MOTIVO_ALTERACAO', array("label" => "Motivo: "));
		$coMotivoAlteracao->addMultiOption(null, 'Selecione');
		$coMotivoAlteracao->addMultiOption(1, 'A pedido da Coordenação Estadual');
		$coMotivoAlteracao->addMultiOption(2, 'A pedido da Coordenação Nacional');
		$coMotivoAlteracao->addMultiOption(3, 'A pedido do Articulador');
		$coMotivoAlteracao->addMultiOption(4, 'Baixa participação dos cursistas após início do curso');
		$coMotivoAlteracao->addMultiOption(5, 'Problemas de ordem pessoal - Tutor');
		$coMotivoAlteracao->setRequired(true);

		$dsObservacao = $this->createElement('textarea', 'DS_OBSERVACAO',
				array("label" => "Observação: ", 'rows' => '5', 'maxlength' => '1000'));

		$stringLength = new Zend_Validate_StringLength();
		$stringLength->setMin(0);
		$stringLength->setMax(1000);
		$stringLength->setEncoding("UTF-8");
		$dsObservacao->addValidator($stringLength);

		$this->addElements(array($coMotivoAlteracao, $dsObservacao));

		$this->addDisplayGroup(array("CO_MOTIVO_ALTERACAO", "DS_OBSERVACAO"), 'justificativacancelamento',
				array("legend" => "Justificativa"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar",
						"mensagem" => "Deseja realmente solicitar cancelamento desta turma?",
						"class" => "btnConfirmar", "title" => "Solicitar Cancelamento"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/secretaria/turma/list/'"));

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
