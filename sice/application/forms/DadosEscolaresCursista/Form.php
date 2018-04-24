<?php

/**
 * Form de cadastro DadosEscolaresCursista
 * 
 * @author diego.matos
 * @since 25/04/2012
 */
class DadosEscolaresCursista_Form extends Fnde_Base_Form {
	/**
	 * Construtor do formulário da tela de Dados Escolares do Cursista.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$nuDadosEscolaresCursista = $this->createElement('hidden', 'NU_SEQ_USUARIO_CURSISTA');

		$nucoMesorregiao = $this->createElement('text', 'CO_MESORREGIAO', array("label" => "coMesorregiao: "));
		$nucoSegmento = $this->createElement('text', 'CO_SEGMENTO', array("label" => "coSegmento: "));
		$nucoSegmento->setRequired(true);
		$nucoRedeEnsino = $this->createElement('text', 'CO_REDE_ENSINO', array("label" => "coRedeEnsino: "));
		$nucoRedeEnsino->setRequired(true);
		$nucoMunicipioEscola = $this->createElement('text', 'CO_MUNICIPIO_ESCOLA',
				array("label" => "coMunicipioEscola: "));
		$nucoEscola = $this->createElement('text', 'CO_ESCOLA', array("label" => "coEscola: "));
		$nucoEscola->setRequired(true);
		$nucoUfEscola = $this->createElement('text', 'CO_UF_ESCOLA', array("label" => "coUfEscola: "));
		$nucoUfEscola->setRequired(true);

		// Adiciona os elementos ao formulário
		$this->addElements(
				array($nuDadosEscolaresCursista, $nucoMesorregiao, $nucoSegmento, $nucoRedeEnsino,
						$nucoMunicipioEscola, $nucoEscola, $nucoUfEscola,));

		$this->addDisplayGroup(
				array('NU_SEQ_USUARIO_CURSISTA', 'CO_MESORREGIAO', 'CO_SEGMENTO', 'CO_REDE_ENSINO',
						'CO_MUNICIPIO_ESCOLA', 'CO_ESCOLA', 'CO_UF_ESCOLA',), 'dadosdadosescolarescursista',
				array("legend" => "Dados DadosEscolaresCursista"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/secretaria/dadosescolarescursista/list/'"));

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
