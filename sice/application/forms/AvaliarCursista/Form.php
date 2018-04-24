<?php

/**
 * Form de cadastro AvaliarCursista
 * 
 * @author rafael.paiva
 * @since 28/05/2012
 */
class AvaliarCursista_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Avaliação do Cursista.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author rafael.paiva
	 * @since 28/05/2012
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
		$this->addElement(new Html("htmlCriteriosSugeridos"));
		$this->addElement(new Html("htmlAlunosMatriculados"));

		// Adiciona os elementos ao formulário.
		$this->addElements(array($nuTurma, $nuMinAlunos, $nuAlunosMatriculados));

		$this->addDisplayGroup(array("htmlAlunosMatriculados",), 'dadosalunosmatriculados',
				array("legend" => "Cursistas"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar",
						"mensagem" => "Deseja realmente avaliar os cursistas da turma?", "class" => "btnConfirmar",
						"title" => "Confirmar", "onclick" => "$(this.form).find(':disabled').attr('disabled',false);"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/secretaria/avaliacaopedagogica/list'"));

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
