<?php

/**
 * Form de Filtro Modulo
 *
 * @author diego.matos
 * @since 25/06/2012
 */
class Bolsa_FormFilter extends Fnde_Form {
	/**
	 * Construtor do formulário de pesquisa da tela de Bolsas. 
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 25/06/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		$this->setAttrib("class", "labelLongo");
		$cmbAnoExercicio = $this->createElement("select", "VL_EXERCICIO",
				array("label" => "Exercício: ", "style" => "width:200px;"));
		$cmbAnoExercicio->setRequired(true);
		$cmbAnoExercicio->addMultiOption(null, "Selecione");

		$cmbPeriodoVinc = $this->createElement("select", "NU_SEQ_PERIODO_VINCULACAO",
				array("label" => "Período de finalização da turma: ", "style" => "width:200px;"));
		$cmbPeriodoVinc->setRequired(true);
		$cmbPeriodoVinc->addMultiOption(null, "Selecione");
		//$cmbPeriodoVinc->setDescription("Período de vinculação da turma");

		$cmbSituacaoBolsa = $this->createElement('select', 'ST_BOLSA',
				array('name' => 'ST_BOLSA', 'label' => 'Situação: ', "style" => "width:200px;"));
		$cmbSituacaoBolsa->addMultiOption(null, "Selecione");
		$cmbSituacaoBolsa->setRequired(true);

		$cmbRegiao = $this->createElement('select', 'SG_REGIAO',
				array("label" => "Região: ", "style" => "width:200px;"));
		$cmbRegiao->addMultiOption(null, "Selecione");
		$cmbRegiao->setRequired(true);

		$cmbUf = $this->createElement('select', 'SG_UF', array("label" => "UF: ", "style" => "width:200px;"));
		$cmbUf->addMultiOption(null, 'Selecione');
//		$cmbUf->setRequired(true);

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        foreach ($perfilUsuario as $perfil) {
            if (
                $perfil != Fnde_Sice_Model_PerfilUsuario::SICE_COORD_NACIONAL_ADM
                && $perfil != Fnde_Sice_Model_PerfilUsuario::SICE_COORD_NACIONAL_EQUIPE
                && $perfil != Fnde_Sice_Model_PerfilUsuario::SICE_COORD_NACIONAL_GESTOR
            )
            {
                $cmbUf->setRequired(true);
            }
        }

		$cmbMesorregiao = $this->createElement('select', 'CO_MESORREGIAO',
				array("label" => "Mesorregião: ", "style" => "width:200px;"));
		$cmbMesorregiao->addMultiOption(null, 'Selecione');

		$cmbPerfil = $this->createElement('select', 'NU_SEQ_TIPO_PERFIL',
				array("label" => "Perfil: ", "style" => "width:200px;"));
		$cmbPerfil->addMultiOption(null, 'Selecione');

		// Adiciona os elementos ao formulário
		$this->addElements(
				array($cmbAnoExercicio, $cmbPeriodoVinc, $cmbRegiao, $cmbUf, $cmbMesorregiao, $cmbPerfil,
						$cmbSituacaoBolsa));

		$this->addDisplayGroup(
				array('VL_EXERCICIO', 'NU_SEQ_PERIODO_VINCULACAO', 'ST_BOLSA', 'SG_REGIAO', 'SG_UF', 'CO_MESORREGIAO',
						'NU_SEQ_TIPO_PERFIL'), 'Filtro', array("legend" => "Filtro"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/financeiro/bolsa/clear-search/'"));

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
	 * 
	 * @param array $arAno
	 */
	public function setAnoExercicio( $arAno ) {
		$this->getElement('VL_EXERCICIO')->setMultiOptions($arAno);
	}

	/**
	 * 
	 * @param array $arPeriodo
	 */
	public function setPeridoVinculacao( $arPeriodo ) {
		$this->getElement('NU_SEQ_PERIODO_VINCULACAO')->setMultiOptions($arPeriodo);
	}
	/**
	 * 
	 * @param array $arSituacao
	 */
	public function setSituacaoBolsa( $arSituacao ) {
		$this->getElement('ST_BOLSA')->addMultiOptions($arSituacao);
	}

	/**
	 * 
	 * @param array $arRegiao
	 */
	public function setRegiao( $arRegiao ) {
		$this->getElement('SG_REGIAO')->setMultiOptions($arRegiao);
	}

	/**
	 * Preenche os valores do combo de UF
	 * @author poliane.silva
	 * @since 16/11/2012
	 * @param array $arUf
	 * @return combo
	 */
	public function setUf( $arUf ) {
		$this->getElement('SG_UF')->setMultiOptions($arUf);
	}

	/**
	 * Preenche os valores do combo de mesorregião
	 * @author poliane.silva
	 * @since 16/11/2012
	 * @param array $arMesoregiao
	 */
	public function setMesoregiao( $arMesoregiao ) {
		$this->getElement('CO_MESORREGIAO')->setMultiOptions($arMesoregiao);
	}

	/**
	 * PREENCHE OS VALORES DO COMBO DE PERFIL DE ACORDO COM O PERFIL LOGADO.
	 * @author poliane.silva
	 * @since 16/11/2012
	 * @param array $arPerfil
	 */
	public function setPerfilUsuarioLogado( $arPerfil ) {
		$this->getElement('NU_SEQ_TIPO_PERFIL')->addMultiOptions($arPerfil);
	}

}
