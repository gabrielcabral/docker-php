<?php

/**
 * Form de cadastro SolicitarAutorizacao
 * 
 * @author rafael.paiva
 * @since 08/05/2012
 */
class SolicitarAutorizacao_Form extends Fnde_Form {
	/**
	 * Construtor do formul�rio da tela de Solicitar Autoriza��o.
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formul�rio
		$nuTurma = $this->createElement('hidden', 'NU_SEQ_TURMA');
		$nuTurma->setValue($arDados['NU_SEQ_TURMA']);

		$nuMinAlunos = $this->createElement('hidden', 'NU_MIN_ALUNOS');
		$nuMinAlunos->setValue($arDados['NU_MIN_ALUNOS']);

		$nuAlunosMatriculados = $this->createElement('hidden', 'NU_ALUNOS_MATRICULADOS');
		$nuAlunosMatriculados->setValue($arDados['NU_ALUNOS_MATRICULADOS']);

		$this->addElement(new Html("htmlTurma"));
		$this->addElement(new Html("htmlAlunosMatriculados"));

		// Adiciona os elementos ao formul�rio.
		$this->addElements(array($nuTurma, $nuMinAlunos, $nuAlunosMatriculados));

		$this->addDisplayGroup(array("htmlAlunosMatriculados",), 'dadosalunosmatriculados',
				array("legend" => "Cursistas Matriculados"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "mensagem" => "", "class" => "btnConfirmar",
						"title" => "Solicitar autoriza��o"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/secretaria/turma/list/'"));

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
