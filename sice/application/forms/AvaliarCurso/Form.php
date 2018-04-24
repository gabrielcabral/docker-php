<?php

/**
 * Form de Avaliar Curso
 * 
 * @author poliane.silva
 * @since 31/08/2012
 */
class AvaliarCurso_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Avaliação do Curso.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author poliane.silva
	 * @since 31/08/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		$nuSeqTurma = $this->createElement('hidden', 'NU_SEQ_TURMA');
		$nuSeqTurma->setValue( ( $arExtra['NU_SEQ_TURMA'] ? $arExtra['NU_SEQ_TURMA'] : $arDados['NU_SEQ_TURMA'] ));

		if ( $arExtra['NU_SEQ_TURMA'] ) {
			$nuSeqTurmaUrl = $this->createElement('hidden', 'NU_SEQ_TURMA_URL');
			$nuSeqTurmaUrl->setValue($arExtra['NU_SEQ_TURMA']);
			$this->addElements(array($nuSeqTurmaUrl));
		}

		$nuSeqUsuario = $this->createElement('hidden', 'NU_SEQ_USUARIO');
		$nuSeqUsuario->setValue($arDados['NU_SEQ_USUARIO']);

		$this->addElement(new Html("htmlDadosTurma"));
		$this->addElement(new Html("htmlDadosAvaliador"));

		$nuQuestao1 = $this->createElement('radio', 'NU_QUESTAO_1',
				array(
						"label" => Fnde_Sice_Model_AvaliacaoCurso::q1));
		$nuQuestao1 = $this->adicionaQuestaoBasica($nuQuestao1);

		$nuQuestao2 = $this->createElement('radio', 'NU_QUESTAO_2',
				array("label" => Fnde_Sice_Model_AvaliacaoCurso::q2));
		$nuQuestao2 = $this->adicionaQuestaoBasica($nuQuestao2);

		$nuQuestao3 = $this->createElement('radio', 'NU_QUESTAO_3',
				array("label" => Fnde_Sice_Model_AvaliacaoCurso::q3));
		$nuQuestao3 = $this->adicionaQuestao3($nuQuestao3);

		$nuQuestao4 = $this->createElement('radio', 'NU_QUESTAO_4',
				array(
						"label" => Fnde_Sice_Model_AvaliacaoCurso::q4));
		$nuQuestao4 = $this->adicionaQuestaoBasica($nuQuestao4);

		$nuQuestao5 = $this->createElement('radio', 'NU_QUESTAO_5',
				array(
						"label" => Fnde_Sice_Model_AvaliacaoCurso::q5));
		$nuQuestao5 = $this->adicionaQuestao5($nuQuestao5);

		$nuQuestao6 = $this->createElement('radio', 'NU_QUESTAO_6',
				array(
						"label" => Fnde_Sice_Model_AvaliacaoCurso::q6));
		$nuQuestao6 = $this->adicionaQuestao6($nuQuestao6);

		$nuQuestao7 = $this->createElement('radio', 'NU_QUESTAO_7',
				array("label" => Fnde_Sice_Model_AvaliacaoCurso::q7));
		$nuQuestao7 = $this->adicionaQuestaoBasica($nuQuestao7);

		$nuQuestao8 = $this->createElement('radio', 'NU_QUESTAO_8',
				array("label" => Fnde_Sice_Model_AvaliacaoCurso::q8));
		$nuQuestao8 = $this->adicionaQuestaoBasica($nuQuestao8);

		$nuQuestao9 = $this->createElement('radio', 'NU_QUESTAO_9',
				array("label" => Fnde_Sice_Model_AvaliacaoCurso::q9));
		$nuQuestao9 = $this->adicionaQuestaoBasica($nuQuestao9);

		$nuQuestao10 = $this->createElement('radio', 'NU_QUESTAO_10',
				array("label" => Fnde_Sice_Model_AvaliacaoCurso::q10));
		$nuQuestao10 = $this->adicionaQuestao10($nuQuestao10);

		// Adiciona os elementos ao formulário.
		$this->addElements(
				array($nuSeqTurma, $nuSeqUsuario, $nuQuestao1, $nuQuestao2, $nuQuestao3, $nuQuestao4, $nuQuestao5,
						$nuQuestao6, $nuQuestao7, $nuQuestao8, $nuQuestao9, $nuQuestao10));

		$this->addDisplayGroup(
				array('NU_QUESTAO_1', 'NU_QUESTAO_2', 'NU_QUESTAO_3', 'NU_QUESTAO_4', 'NU_QUESTAO_5', 'NU_QUESTAO_6',
						'NU_QUESTAO_7', 'NU_QUESTAO_8', 'NU_QUESTAO_9', 'NU_QUESTAO_10'), 'questoes',
				array("legend" => "Dados de Avaliação"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						"onClick" => "window.location='" . $this->getView()->baseUrl()
								. "/index.php'"));
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
	 * Adiciona as opções das questões
	 * @param elemento $nuQuestao
	 */
	private function adicionaQuestaoBasica( $nuQuestao ) {
		$nuQuestao->setRequired(true);
		$nuQuestao->addMultiOption("1", Fnde_Sice_Model_AvaliacaoCurso::qBasicar1);
		$nuQuestao->addMultiOption("2", Fnde_Sice_Model_AvaliacaoCurso::qBasicar2);
		$nuQuestao->addMultiOption("3", Fnde_Sice_Model_AvaliacaoCurso::qBasicar3);
		$nuQuestao->addMultiOption("4", Fnde_Sice_Model_AvaliacaoCurso::qBasicar4);
		return $nuQuestao;
	}

	/**
	 * Adiciona as opções da questão 3
	 * @param elemento $nuQuestao
	 */
	private function adicionaQuestao3( $nuQuestao3 ) {
		$nuQuestao3->setRequired(true);
		$nuQuestao3->addMultiOption("1", Fnde_Sice_Model_AvaliacaoCurso::q3r1);
		$nuQuestao3->addMultiOption("2", Fnde_Sice_Model_AvaliacaoCurso::q3r2);
		$nuQuestao3->addMultiOption("3", Fnde_Sice_Model_AvaliacaoCurso::q3r3);
		$nuQuestao3->addMultiOption("4", Fnde_Sice_Model_AvaliacaoCurso::q3r4);
		return $nuQuestao3;
	}

	/**
	 * Adiciona as opções da questão 5
	 * @param elemento $nuQuestao
	 */
	private function adicionaQuestao5( $nuQuestao5 ) {
		$nuQuestao5->setRequired(true);
		$nuQuestao5->addMultiOption("1", Fnde_Sice_Model_AvaliacaoCurso::q5r1);
		$nuQuestao5->addMultiOption("2", Fnde_Sice_Model_AvaliacaoCurso::q5r2);
		$nuQuestao5->addMultiOption("3", Fnde_Sice_Model_AvaliacaoCurso::q5r3);
		$nuQuestao5->addMultiOption("4", Fnde_Sice_Model_AvaliacaoCurso::q5r4);
		return $nuQuestao5;
	}

	/**
	 * Adiciona as opções da questão 6
	 * @param elemento $nuQuestao
	 */
	private function adicionaQuestao6( $nuQuestao6 ) {
		$nuQuestao6->setRequired(true);
		$nuQuestao6->addMultiOption("1", Fnde_Sice_Model_AvaliacaoCurso::q6r1);
		$nuQuestao6->addMultiOption("2", Fnde_Sice_Model_AvaliacaoCurso::q6r2);
		$nuQuestao6->addMultiOption("3", Fnde_Sice_Model_AvaliacaoCurso::q6r3);
		$nuQuestao6->addMultiOption("4", Fnde_Sice_Model_AvaliacaoCurso::q6r4);
		return $nuQuestao6;
	}

	/**
	 * Adiciona as opções da questão 10
	 * @param elemento $nuQuestao
	 */
	private function adicionaQuestao10( $nuQuestao10 ) {
		$nuQuestao10->setRequired(true);
		$nuQuestao10->addMultiOption("1", Fnde_Sice_Model_AvaliacaoCurso::q10r1);
		$nuQuestao10->addMultiOption("2", Fnde_Sice_Model_AvaliacaoCurso::q10r2);
		$nuQuestao10->addMultiOption("3", Fnde_Sice_Model_AvaliacaoCurso::q10r3);
		$nuQuestao10->addMultiOption("4", Fnde_Sice_Model_AvaliacaoCurso::q10r4);
		return $nuQuestao10;
	}
}
