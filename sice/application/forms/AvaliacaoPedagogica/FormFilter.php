<?php

/**
 * Form de Filtro Avaliação Pedagógica
 * 
 * @author poliane.silva
 * @since 28/05/2012
 */
class AvaliacaoPedagogica_FormFilter extends Fnde_Form {
	/**
	 * Construtor do Formulário de Pesquisa da tela de Avaliação Pedagógica.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author poliane.silva
	 * @since 28/05/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$tipoCurso = $this->createElement("select", 'NU_SEQ_TIPO_CURSO',
				array('name' => 'NU_SEQ_TIPO_CURSO', 'label' => 'Tipo de Curso: ', $arDados['NU_SEQ_TIPO_CURSO']));
		$tipoCurso->setRequired(true);
		$tipoCurso->addMultiOption(null, "Selecione");

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

		$ufTurma->addMultiOption(null, 'Selecione');
		$ufTurma->setValue($arDados["UF_TURMA"]);

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
				array("label" => "ID: ", "class" => "inteiro", "maxlength" => "7"));

		$modulo = $this->createElement('select', 'NU_SEQ_MODULO', array("label" => "Módulo: "));
		$modulo->addMultiOption(null, "Selecione");

		$curso = $this->createElement('select', 'NU_SEQ_CURSO', array("label" => "Curso: "));
		$curso->addMultiOption(null, "Selecione");

		$dtInicio = $this->createElement('text', 'DT_INICIO',
				array("label" => "Data início: ", "class" => "date dp-applied"));
		$dtFim = $this->createElement('text', 'DT_FIM',
				array("label" => "Data fim prevista: ", "class" => "date dp-applied"));

		$situacaoTurma = $this->createElement('select', 'ST_TURMA', array("label" => "Situação: "));
		$situacaoTurma->addMultiOptions(array(null => "Selecione", 3 => "Aguardando autorização",  8 => "Aguardando cancelamento",  4 => "Ativa",  9 => "Cancelada",  12 => "Em avaliação",  11 => "Finalizada",  10 => "Finalizada Atrasada",  5 => "Não autorizada",  1 => "Pré-turma",  7 => "Rejeitar Cancelamento",  2 => "Solicitado autorização", 6 => "Solicitado cancelamento"));

		$dtFinalizacao = $this->createElement('text', 'DT_FINAL',
				array("label" => "Data finalização: ", "class" => "date dp-applied"));

		// Adiciona os elementos ao formulário
		$this->addElements(
				array($tipoCurso, $ufTurma, $mesorregiao, $municipio, $usuarioTutor, $usuarioArticulador, $idTurma,
						$modulo, $curso, $dtInicio, $dtFim, $situacaoTurma, $dtFinalizacao,));

		$this->addDisplayGroup(
				array('NU_SEQ_TIPO_CURSO', 'UF_TURMA', 'CO_MESORREGIAO', 'CO_MUNICIPIO', 'NU_SEQ_USUARIO_TUTOR',
						'NU_SEQ_USUARIO_ARTICULADOR', 'NU_SEQ_TURMA', 'NU_SEQ_MODULO', 'NU_SEQ_CURSO', 'DT_INICIO',
						'DT_FIM', 'ST_TURMA', 'DT_FINAL',), 'dadosavalped', array("legend" => "Filtro"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/secretaria/avaliacaopedagogica/clear-search/'"));

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
	 * Adiciona as opções do combo de Tipo de Turma
	 * @param array $arTipoCurso
	 */
	public function setTipoCurso( $arTipoCurso ) {
		$this->getElement('NU_SEQ_TIPO_CURSO')->addMultiOptions($arTipoCurso);
	}

	/**
	 * Adiciona as opções do combo de UF
	 * @param array $arUf
	 */
	public function setUf( $arUf ) {
		$this->getElement('UF_TURMA')->setMultiOptions($arUf);
	}

	/**
	 * Adiciona as opções do combo de Mesorregiao
	 * @param array $arMesorregiao
	 */
	public function setMesorregiao( $arMesorregiao ) {
		$this->getElement('CO_MESORREGIAO')->setMultiOptions($arMesorregiao);
	}

	/**
	 * Adiciona as opções do combo de Município
	 * @param array $arMunicipio
	 */
	public function setMunicipio( $arMunicipio, $mesorregiao ) {
		$this->getElement('CO_MUNICIPIO')->setMultiOptions($arMunicipio);
		$this->getElement('CO_MESORREGIAO')->setValue($mesorregiao);
	}

	/**
	 * Adiciona as opções do combo de Tutor
	 * @param array $arTutor
	 */
	public function setTutor( $arTutor ) {
		$this->getElement('NU_SEQ_USUARIO_TUTOR')->setMultiOptions($arTutor);
	}

	/**
	 * Adiciona as opções do combo de Articulador
	 * @param array $arArticulador
	 */
	public function setArticulador( $arArticulador ) {
		$this->getElement('NU_SEQ_USUARIO_ARTICULADOR')->setMultiOptions($arArticulador);
	}

	/**
	 * Adiciona as opções do combo de Módulo
	 * @param array $arModulo
	 */
	public function setModulo( $arModulo ) {
		$this->getElement('NU_SEQ_MODULO')->addMultiOptions($arModulo);
	}

	/**
	 * Adiciona as opções do combo de Curso
	 * @param array $arCurso
	 */
	public function setCurso( $arCurso ) {
		$this->getElement('NU_SEQ_CURSO')->addMultiOptions($arCurso);
	}

}
