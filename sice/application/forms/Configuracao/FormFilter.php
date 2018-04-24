<?php

/**
 * Form de Filtro Configuracao
 * 
 * @author diego.matos
 * @since 30/03/2012
 */
class Configuracao_FormFilter extends Fnde_Form {
	/**
	 * Construtor do formulário de pesquisa da tela de Configuração. 
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$nuqtTurmaTutor = $this->createElement('text', 'QT_TURMA_TUTOR',
				array("label" => "Qtd turmas por tutor: ", "maxlength" => "3", "class" => "inteiro"));
		$nuqtAlunosTurma = $this->createElement('text', 'QT_ALUNOS_TURMA',
				array("label" => "Qtd Alunos por Turma: ", "maxlength" => "3", "class" => "inteiro"));

		// Adiciona os elementos ao formulário
		$this->addElements(array($nuqtTurmaTutor, $nuqtAlunosTurma,));

		$this->addDisplayGroup(array('QT_TURMA_TUTOR', 'QT_ALUNOS_TURMA',), 'dadosconfiguracao',
				array("legend" => "Filtro"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar",));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/configuracao/clear-search/'"));

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
