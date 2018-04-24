<?php

/**
 * Form de Filtro LiberarSenha
 *
 * @author rafael.paiva
 * @since 04/06/2012
 */
class LiberarSenha_FormFilter extends Fnde_Form {
	/**
	 * Construtor do formul�rio da tela de Libera��o de Senhas.
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author rafael.paiva
	 * @since 04/06/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formul�rio
		$sgUfAtuacaoPerfil = $this->createElement('select', 'SG_UF_ATUACAO_PERFIL', array("label" => "UF: "));
//		$sgUfAtuacaoPerfil->setRequired(true);

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        foreach ($perfilUsuario as $perfil) {
            if (
                $perfil != Fnde_Sice_Model_PerfilUsuario::SICE_COORD_NACIONAL_ADM
                && $perfil != Fnde_Sice_Model_PerfilUsuario::SICE_COORD_NACIONAL_EQUIPE
                && $perfil != Fnde_Sice_Model_PerfilUsuario::SICE_COORD_NACIONAL_GESTOR
            )
            {
                $sgUfAtuacaoPerfil->setRequired(true);
            }
        }

		$sgUfAtuacaoPerfil->addMultiOption(null, 'Selecione');
		$sgUfAtuacaoPerfil->setValue($arDados['SG_UF_ATUACAO_PERFIL']);

		$coMesorregiao = $this->createElement('select', 'CO_MESORREGIAO', array("label" => "Mesoregi�o: "));
		$coMesorregiao->addMultiOption(null, 'Selecione');

		$coMunicipioPerfil = $this->createElement('select', 'CO_MUNICIPIO_PERFIL', array("label" => "Municipio: "));
		$coMunicipioPerfil->addMultiOption(null, 'Selecione');

		$nuCpf = $this->createElement('text', 'NU_CPF', array("label" => "CPF: ", "class" => "cpf"));

		$nuSeqTipoPerfil = $this->createElement('select', 'NU_SEQ_TIPO_PERFIL', array('label' => 'Perfil: '));
		$nuSeqTipoPerfil->addMultiOption(null, 'Selecione');

		$stUsuario = $this->createElement('select', 'ST_USUARIO', array("label" => "Situa��o: "));
		$stUsuario->addMultiOption(null, 'Selecione');
		$stUsuario->addMultiOption('L', 'Libera��o de Senha');
		$stUsuario->addMultiOption('R', 'Renova��o de senha');

		$nuSeqTurma = $this->createElement('select', 'NU_SEQ_TURMA', array('label' => 'Turma: '));
		$nuSeqTurma->addMultiOption(null, 'Selecione');

		// Adiciona os elementos ao formul�rio
		$this->addElements(
				array($sgUfAtuacaoPerfil, $coMesorregiao, $coMunicipioPerfil, $nuCpf, $nuSeqTipoPerfil, $stUsuario,
						$nuSeqTurma));

		$this->addDisplayGroup(
				array('SG_UF_ATUACAO_PERFIL', 'CO_MESORREGIAO', 'CO_MUNICIPIO_PERFIL', 'NU_CPF', 'NU_SEQ_TIPO_PERFIL',
						'ST_USUARIO', 'NU_SEQ_TURMA'), 'filtroUsuario', array("legend" => "Filtro"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/liberarsenha/clear-search/'"));

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

	/**
	 * Adiciona as op��es do combo de UF
	 * @param array $arUf
	 */
	public function setUfPerfil( $arUf ) {
		$this->getElement('SG_UF_ATUACAO_PERFIL')->setMultiOptions($arUf);
	}

	/**
	 * Adiciona as op��es do combo de Mesorregi�o
	 * @param array $arMesoregiao
	 */
	public function setMesorregiao( $arMesoregiao ) {
		$this->getElement('CO_MESORREGIAO')->setMultiOptions($arMesoregiao);
	}

	/**
	 * Adiciona as op��es do combo de Munic�pio
	 * @param array $arMunicipio
	 */
	public function setMunicipio( $arMunicipio, $coMesorregiao ) {
		$this->getElement('CO_MUNICIPIO_PERFIL')->setMultiOptions($arMunicipio);
		$this->getElement('CO_MESORREGIAO')->setValue($coMesorregiao);
	}

	/**
	 * Adiciona as op��es do combo de Perfil
	 * @param array $arPerfil
	 */
	public function setPerfil( $arPerfil ) {
		$this->getElement('NU_SEQ_TIPO_PERFIL')->setMultiOptions($arPerfil);
	}

	/**
	 * Adiciona as op��es do combo de Turma
	 * @param array $arTurma
	 */
	public function setTurma( $arTurma ) {
		$this->getElement('NU_SEQ_TURMA')->setMultiOptions($arTurma);
	}
}
