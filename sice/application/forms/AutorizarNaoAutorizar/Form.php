<?php

/**
 * Form de cadastro AutorizarNaoAutorizar
 * 
 * @author poliane.silva
 * @since 21/05/2012
 */
class AutorizarNaoAutorizar_Form extends Fnde_Form {
	/**
	 * Construtor do formul�rio da tela de Autorizar/N�o Autorizar Turmas.
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function __construct( $arDados, $arExtra, $bAutorizar ) {

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

		if ( !$bAutorizar ) {

			$coMotivoAlteracao = $this->createElement('select', 'CO_MOTIVO_ALTERACAO', array("label" => "Motivo: "));
			$coMotivoAlteracao->addMultiOption(null, 'Selecione');
			$coMotivoAlteracao->addMultiOption(1, 'A pedido da Coordena��o Estadual');
			$coMotivoAlteracao->addMultiOption(2, 'A pedido da Coordena��o Nacional');
			$coMotivoAlteracao->addMultiOption(3, 'A pedido do Articulador');
			$coMotivoAlteracao->addMultiOption(4, 'A pedido do Tutor');
			$coMotivoAlteracao->addMultiOption(5, 'Baixa participa��o dos cursistas ap�s in�cio do curso');

			$coMotivoAlteracao->setRequired(true);

			$dsObservacao = $this->createElement('textarea', 'DS_OBSERVACAO',
					array("label" => "Observa��o: ", 'rows' => '5', 'maxlength' => '1000'));

			$coMotivoAlteracao->setValue($arExtra['CO_MOTIVO_ALTERACAO']);
			$dsObservacao->setValue($arExtra['DS_OBSERVACAO']);

			$this->addElements(array($coMotivoAlteracao, $dsObservacao));

			$this->addDisplayGroup(array("CO_MOTIVO_ALTERACAO", "DS_OBSERVACAO"), 'justificativacancelamento',
					array("legend" => "Justificativa"));

			$btConfirmar = $this->createElement('submit', 'confirmar',
					array("label" => "Confirmar", "value" => "Confirmar",
							"mensagem" => "Deseja realmente n�o autorizar a turma?", "class" => "btnConfirmar",
							"title" => "N�o autorizar turma"));
			$btCancelar = $this->createElement('button', 'cancelar',
					array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar",
							'onClick' => "window.location='" . $this->getView()->baseUrl()
									. "/index.php/secretaria/turma/list/'"));

			//Adicionado Componentes no formul�rio
			$this->addElements(array($btConfirmar, $btCancelar));

			$obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
					array('class' => 'agrupador barraBtsAcoes'));

		} else {

			$btNaoAutorizar = $this->createElement('button', 'naoautorizar',
					array("label" => "N�o Autorizar", "value" => "N�o Autorizar", "title" => ""));
			$btConfirmar = $this->createElement('button', 'confirmarautorizar',
					array("label" => "Confirmar", "value" => "Confirmar",
							"mensagem" => "Deseja realmente autorizar a turma?", "class" => "btnConfirmar",
							"title" => "Autoriza Turma"));
			$btCancelar = $this->createElement('button', 'cancelar',
					array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "",
							'onClick' => "window.location='" . $this->getView()->baseUrl()
									. "/index.php/secretaria/turma/list/'"));

			//Adicionado Componentes no formul�rio
			$this->addElements(array($btNaoAutorizar, $btConfirmar, $btCancelar));

			$obDisplayGroup = $this->addDisplayGroup(array('naoautorizar', 'confirmarautorizar', 'cancelar'), 'botoes',
					array('class' => 'agrupador barraBtsAcoes'));
		}
		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		return $this;
	}

}
