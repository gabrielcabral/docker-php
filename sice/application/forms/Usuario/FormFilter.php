<?php

/**
 * Form de Filtro Usuario
 *
 * @author diego.matos
 * @since 10/04/2012
 */
class Usuario_FormFilter extends Fnde_Form {
	/**
	 * Construtor do formulário de pesquisa da tela de Usuário.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function __construct( $arDados, $arExtra = null ) {
		//Adicionando elementos no formulário
		$sgUfAtuacaoPerfil = $this->createElement('select', 'SG_UF_ATUACAO_PERFIL', array("label" => "UF: "));
		//$sgUfAtuacaoPerfil->setRequired(true);

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

		$coMesorregiao = $this->createElement('select', 'CO_MESORREGIAO', array("label" => "Mesorregião: "));
		$coMesorregiao->addMultiOption(null, 'Selecione');

		$coMunicipioPerfil = $this->createElement('select', 'CO_MUNICIPIO_PERFIL', array("label" => "Município: "));
		$coMunicipioPerfil->addMultiOption(null, 'Selecione');

		$noUsuario = $this->createElement('text', 'NO_USUARIO', array("label" => "Nome: ", "maxlength" => "70"));

		$nuCpf = $this->createElement('text', 'NU_CPF', array("label" => "CPF: ", "class" => "cpf"));

		$nuSeqTipoPerfil = $this->createElement('select', 'NU_SEQ_TIPO_PERFIL', array('label' => 'Perfil: '));
		$nuSeqTipoPerfil->addMultiOption(null, 'Selecione');

		$stUsuario = $this->createElement('select', 'ST_USUARIO', array("label" => "Situação: "));
		$stUsuario->addMultiOption(null, 'Selecione');
		$stUsuario->addMultiOption('A', 'Ativo');
		$stUsuario->addMultiOption('D', 'Inativo');
		$stUsuario->addMultiOption('L', 'Liberação Pendente');

		// Adiciona os elementos ao formulário
		$this->addElements(
				array($sgUfAtuacaoPerfil, $coMesorregiao, $coMunicipioPerfil, $noUsuario, $nuCpf, $nuSeqTipoPerfil,
						$stUsuario));

		$this->addDisplayGroup(
				array('SG_UF_ATUACAO_PERFIL', 'CO_MESORREGIAO', 'CO_MUNICIPIO_PERFIL', 'NO_USUARIO', 'NU_CPF',
						'NU_SEQ_TIPO_PERFIL', 'ST_USUARIO'), 'filtroUsuario', array("legend" => "Filtro"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/usuario/clear-search/'"));

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
	 * Seta o valor das opções do combo de UF.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setUf( $options ) {
		$element = $this->getElement('SG_UF_ATUACAO_PERFIL');
		$element->setMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Mesorregiao.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setMesorregiao( $options ) {
		$element = $this->getElement('CO_MESORREGIAO');
		$element->setMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Municipio.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setMunicipio( $options ) {
		$element = $this->getElement('CO_MUNICIPIO_PERFIL');
		$element->setMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Perfil.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setTipoPerfil( $options ) {
		$element = $this->getElement('NU_SEQ_TIPO_PERFIL');
		$element->setMultiOptions($options);
	}

}
