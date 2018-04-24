<?php

/**
 * Form de cadastro PeriodoVinculacao
 * 
 * @author vinicius.cancado
 * @since 18/04/2012
 */
class PeriodoVinculacao_Form extends Fnde_Form {
	/**
	 * Construtor do formul�rio da tela de Per�odo de Vincula��o.
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formul�rio
		$nuPeriodoVinculacao = $this->createElement('hidden', 'NU_SEQ_PERIODO_VINCULACAO');

		$nuvlExercicio = $this->createElement('text', 'VL_EXERCICIO',
				array("label" => "Exerc�cio: ", "maxlength" => "4", "class" => "inteiro"));
		$nuvlExercicio->setRequired(true);

		// Adiciona os elementos ao formul�rio
		$this->addElements(array($nuPeriodoVinculacao, $nuvlExercicio));

		$this->addDisplayGroup(
				array('NU_SEQ_PERIODO_VINCULACAO', 'DT_INCLUSAO', 'DT_FINAL', 'VL_EXERCICIO', 'DT_INICIAL',
						'NU_SEQ_TIPO_PERFIL',), 'dadosperiodovinculacao', array("legend" => "Cadastro"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/periodovinculacao/list/'"));

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
