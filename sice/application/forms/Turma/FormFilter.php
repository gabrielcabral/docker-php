<?php

/**
 * Form de Filtro Turma
 * 
 * @author diego.matos
 * @since 25/04/2012
 */
class Turma_FormFilter extends Fnde_Form {
	/**
	 * Construtor do formulário de pesquisa da tela de Turma.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function __construct() {
		$tipoCurso = $this->createElement('select', 'NU_SEQ_TIPO_CURSO', array('label' => 'Tipo de Curso:'));
		$tipoCurso->setRequired(true);

		$ufTurma = $this->createElement('select', 'UF_TURMA', array("label" => "UF: "));
//		$ufTurma->setRequired(true);

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        foreach ($perfilUsuario as $perfil) {
            if (
                $perfil != Fnde_Sice_Model_PerfilUsuario::SICE_COORD_NACIONAL_ADM
                && $perfil != Fnde_Sice_Model_PerfilUsuario::SICE_COORD_NACIONAL_EQUIPE
                && $perfil != Fnde_Sice_Model_PerfilUsuario::SICE_COORD_NACIONAL_GESTOR
            )
            {
                $ufTurma->setRequired(true);
            }
        }

		$mesorregiao = $this->createElement('select', 'CO_MESORREGIAO', array("label" => "Mesorregião: "));
		$mesorregiao->addMultiOption(null, 'Selecione');

		$municipio = $this->createElement('select', 'CO_MUNICIPIO', array("label" => "Município: "));
		$municipio->addMultiOption(null, 'Selecione');

		$usuarioTutor = $this->createElement('select', 'NU_SEQ_USUARIO_TUTOR', array("label" => "Tutor: "));
		$usuarioTutor->addMultiOption(null, "Selecione");

		$usuarioArticulador = $this->createElement('select', 'NU_SEQ_USUARIO_ARTICULADOR',
				array("label" => "Articulador: "));
		$usuarioArticulador->addMultiOption(null, "Selecione");

		$idTurma = $this->createElement('text', 'NU_SEQ_TURMA',
				array("label" => "Número turma: ", "maxlength" => "7", "class" => "inteiro"));

		$modulo = $this->createElement('select', 'NU_SEQ_MODULO', array("label" => "Módulo: "));
		$modulo->addMultiOption(null, "Selecione");

		$curso = $this->createElement('select', 'NU_SEQ_CURSO', array("label" => "Curso: "));
		$curso->addMultiOption(null, "Selecione");

		$dtInicio = $this->createElement('text', 'DT_INICIO',
				array("label" => "Data início: ", "class" => "date dp-applied"));
		$dtFim = $this->createElement('text', 'DT_FIM',
				array("label" => "Data fim prevista: ", "class" => "date dp-applied"));

		$situacaoTurma = $this->createElement('select', 'ST_TURMA', array("label" => "Situação: "));
		$situacaoTurma->addMultiOption(null, "Selecione");

		$dtFinalizacao = $this->createElement('text', 'DT_FINAL',
				array("label" => "Data finalização: ", "class" => "date dp-applied"));

		// Adiciona os elementos ao formulário
		$this->addElements(
				array($tipoCurso, $ufTurma, $mesorregiao, $municipio, $usuarioTutor, $usuarioArticulador, $idTurma,
						$modulo, $curso, $dtInicio, $dtFim, $situacaoTurma, $dtFinalizacao,));

		$this->addDisplayGroup(
				array('NU_SEQ_TIPO_CURSO', 'UF_TURMA', 'CO_MESORREGIAO', 'CO_MUNICIPIO', 'NU_SEQ_USUARIO_TUTOR',
						'NU_SEQ_USUARIO_ARTICULADOR', 'NU_SEQ_TURMA', 'NU_SEQ_MODULO', 'NU_SEQ_CURSO', 'DT_INICIO',
						'DT_FIM', 'ST_TURMA', 'DT_FINAL',), 'dadosturma', array("legend" => "Filtro Turma"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/secretaria/turma/clear-search/'"));

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
	 * Seta o valor das opções do combo de Tipo de Curso.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setTipoCurso( $options ) {
		$element = $this->getElement('NU_SEQ_TIPO_CURSO');
		$element->setMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de UF.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setUf( $options ) {
		$element = $this->getElement('UF_TURMA');
		$element->setMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Mesorregiao.
	 * Seta tambem o valor do combo quando necessario.
	 * @param array $arOptions Array valido com as opções.
	 * @param value $value Codigo da mesorregiao para setar o valor selecionado.
	 */
	public function setMesorregiao( $options, $value = null ) {
		$element = $this->getElement('CO_MESORREGIAO');
		$element->setMultiOptions($options);
		if ( $value ) {
			$element->setValue($value);
		}
	}

	/**
	 * Seta o valor das opções do combo de Municipio.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setMunicipio( $options ) {
		$element = $this->getElement('CO_MUNICIPIO');
		$element->setMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Tutor.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setTutor( $options ) {
		$element = $this->getElement('NU_SEQ_USUARIO_TUTOR');
		$element->setMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Articulador.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setArticulador( $options ) {
		$element = $this->getElement('NU_SEQ_USUARIO_ARTICULADOR');
		$element->setMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Modulo.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setModulo( $options ) {
		$element = $this->getElement('NU_SEQ_MODULO');
		$element->addMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Curso.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setCurso( $options ) {
		$element = $this->getElement('NU_SEQ_CURSO');
		$element->addMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Situação da turma.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setStTurma( $options ) {
		$element = $this->getElement('ST_TURMA');
		$element->addMultiOptions($options);
	}

}
