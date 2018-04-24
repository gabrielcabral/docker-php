<?php

/**
 * Form de cadastro SolicitarCancelamento
 * 
 * @author poliane.silva
 * @since 17/05/2012
 */
class MotivoNaoAprovacao_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Motivo de Não Aprovação.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$nuSeqAvalTurma = $this->createElement('hidden', 'NU_SEQ_AVALIACAO_TURMA');
		$nuSeqAvalTurma->setValue($arDados['NU_SEQ_AVALIACAO_TURMA']);

		$nuTurma = $this->createElement('hidden', 'NU_SEQ_TURMA');
		$nuTurma->setValue($arDados['NU_SEQ_TURMA']);

		$nuBolsa = $this->createElement('hidden', 'NU_SEQ_BOLSA');
		$nuBolsa->setValue($arDados['NU_SEQ_BOLSA']);

		$dsTurma = $this->createElement('text', 'DS_TURMA',
				array("label" => "Turma: ", "readonly" => "readonly", 'size' => '50'));
		$dsTurma->setValue($arDados['NU_SEQ_TURMA'] . " - " . $arDados['DS_NOME_CURSO']);

		$coJustificativa = $this->createElement("select", 'CO_JUSTIFICATIVA_REPROVACAO',
				array('name' => 'CO_JUSTIFICATIVA_REPROVACAO', 'label' => 'Justificativa: '));
		$coJustificativa->setRequired(true);
		$coJustificativa->addMultiOption(null, "Selecione");

		$dsObservacao = $this->createElement('textarea', 'DS_OBSERVACAO',
				array("label" => "Observação: ", 'rows' => '3', 'maxlength' => '200'));

		$this->addElements(array($nuTurma, $nuBolsa, $dsTurma, $coJustificativa, $dsObservacao,));

		$this->addDisplayGroup(array("DS_TURMA", "CO_JUSTIFICATIVA_REPROVACAO", "DS_OBSERVACAO"),
				'justificativacancelamento', array("legend" => "Justificativa da não aprovação"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar",
						"mensagem" => "Deseja realmente solicitar cancelamento desta turma?",
						"class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "avaliarTurmas('" . $this->getView()->baseUrl()
								. "/index.php/financeiro/avaliarturmas/form/NU_SEQ_BOLSA/" . $arDados['NU_SEQ_BOLSA']
								. "')"));

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
	 * Adiciona as opções do combo de justificativa
	 * @param array $arJustificativa
	 */
	public function setJustificativa( $arJustificativa ) {
		$this->getElement('CO_JUSTIFICATIVA_REPROVACAO')->addMultiOptions($arJustificativa);
	}
}
