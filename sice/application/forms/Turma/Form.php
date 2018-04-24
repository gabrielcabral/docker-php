<?php

/**
 * Form de cadastro Turma
 * 
 * @author diego.matos
 * @since 25/04/2012
 */
class Turma_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Turma.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		//Adicionando elementos no formulário
		$nuTurma = $this->createElement('hidden', 'NU_SEQ_TURMA');
		$nuTurma->setValue($arDados['NU_SEQ_TURMA']);

		$nuConf = $this->createElement('hidden', 'NU_SEQ_CONFIGURACAO');
		$nuConf->setValue($arDados['NU_SEQ_CONFIGURACAO']);

		$nuStTurma = $this->createElement('hidden', 'ST_TURMA');
		$nuStTurma->setValue($arDados['ST_TURMA']);

		$this->setAttrib("class", "labelLongo");

		$tipoCurso = $this->createElement('select', 'NU_SEQ_TIPO_CURSO_CAD', array('label' => 'Tipo de curso: '));
		$tipoCurso->setRequired(true);

		//ADICIONANDO INFORMAÇÃO DA CONFIGURAÇÃO CONFORME SOLICITADO PELO CLIENTE NA BATERIA DE TESTES.
		$dadosConfiguracao = $this->createElement('text', 'DADOS_CONFIGURACAO', array("label" => "Nº da Configuração: "));
		$dadosConfiguracao->setValue($arDados['NU_SEQ_CONFIGURACAO']);
		$dadosConfiguracao ->setAttrib("readonly", true);

		$curso = $this->createElement('select', 'NU_SEQ_CURSO_CAD', array("label" => "Curso: "));
		$curso->addMultiOption(null, 'Selecione');
		$curso->setRequired(true);

		$ufTurma = $this->createElement('select', 'UF_TURMA_CAD', array("label" => "UF: "));
		$ufTurma->addMultiOption(null, 'Selecione')->setRequired(true);
		if($arExtra['block']['uf']){
			$ufTurma->setAttrib('disabled', 'disabled');
		}
		$ufTurma->setValue($arDados['UF_TURMA']);

		$nucoMunicipio = $this->createElement('select', 'CO_MUNICIPIO_CAD', array("label" => "Município: "));
		$nucoMunicipio->addMultiOption(null, 'Selecione');
		$nucoMunicipio->setRequired(true);

		$nucoMesorregiao = $this->createElement('text', 'NO_MESORREGIAO_CAD', array("label" => "Mesorregião: "));
		$nucoMesorregiao->setAttrib("readonly", true);
		$nucoMesorregiao->setRequired(true);
		$nucoMesorregiaoHidden = $this->createElement('hidden', 'CO_MESORREGIAO_CAD');


		$usuarioTutor = $this->createElement('select', 'NU_SEQ_USUARIO_TUTOR', array('label' => 'Tutor: '));
		$usuarioTutor->setRequired(true);

		$usuarioArticulador = $this->createElement('select', 'NU_SEQ_USUARIO_ARTICULADOR',
				array('label' => 'Articulador: '));
		$usuarioArticulador->setRequired(true);

		//Criando e adicionando no form os elementos com os dados do curso.
		$this->setElementosDadosCurso();

		$validator = new Fnde_Sice_Validate_DateGreatherThanValidator(new Zend_Date(date('d/m/Y'), 'D/M/Y'));
		$nudtInicio = $this->createElement('text', 'DT_INICIO',
				array("label" => "Data início: ", "class" => "date dp-applied"));
		$nudtInicio->setRequired(true)->addValidator('date');
		
                /* sgd 27200 
                 * apenas no insert sera verificado a data de inicio maior que a data atual
                 */
                if (!$arDados['NU_SEQ_TURMA']) $nudtInicio->addValidator($validator);

		$validator = new Fnde_Sice_Validate_DateGreatherThanValidator('DT_INICIO');
		$validator->setMessage("A data fim prevista não pode ser menor que a data início.", "dataAtual");
		$nudtFim = $this->createElement('text', 'DT_FIM',
				array("label" => "Data fim prevista: ", "class" => "date dp-applied"));
		$nudtFim->setRequired(true)->addValidator('date');
		$nudtFim->addValidator($validator);

		$nudtFinalizacao = $this->createElement('text', 'DT_FINALIZACAO',
				array("label" => "Data finalização: ", "class" => "date dp-applied"));
		$nudtFinalizacao->setAttrib('disabled', 'disabled');

		// Adiciona os elementos ao formulário
		$this->addElements(
				array($nuTurma, $nuConf, $nuStTurma, $dadosConfiguracao, $tipoCurso, $curso, $ufTurma, $nucoMunicipio, $nucoMesorregiao, $usuarioTutor, $usuarioArticulador,
						$nudtInicio, $nudtFim, $nudtFinalizacao,
						$nucoMesorregiaoHidden,));

		$this->addDisplayGroup(
				array('NU_SEQ_TURMA', 'ST_TURMA', 'NU_SEQ_CONFIGURACAO','DADOS_CONFIGURACAO', 'NU_SEQ_TIPO_CURSO_CAD', 'NU_SEQ_CURSO_CAD',
						'UF_TURMA_CAD', 'CO_MUNICIPIO_CAD', 'NO_MESORREGIAO_CAD', 'CO_MESORREGIAO_CAD',
						'NU_SEQ_USUARIO_TUTOR', 'NU_SEQ_USUARIO_ARTICULADOR', 'NU_MIN_ALUNOS', 'NU_CARGA_PRESENCIAL',
						'NU_CARGA_DISTANCIA', 'NU_CARGA_CURSO', 'NU_MIN_CONCLUSAO', 'NU_MAX_CONCLUSAO', 'DT_INICIO',
						'DT_FIM', 'DT_FINALIZACAO',), 'dadosturma', array("legend" => "Dados Cadastrais"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar",
						"onclick" => "$(this.form).find(':disabled').attr('disabled',false);", "title" => "Confirmar"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/secretaria/turma/list/'"));

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

	private function setElementosDadosCurso() {
		$nuMinAlunos = $this->createElement('text', 'NU_MIN_ALUNOS',
				array("label" => "Mínimo de alunos: ", "disabled" => "disabled", "size" => "5"));
		$nuCargaPresencial = $this->createElement('text', 'NU_CARGA_PRESENCIAL',
				array("label" => "Carga presencial: ", "disabled" => "disabled", "size" => "5"));
		$nuCargaPresencial->setDescription("Em horas");
		$nuCargaDistancia = $this->createElement('text', 'NU_CARGA_DISTANCIA',
				array("label" => "Carga à distância: ", "disabled" => "disabled", "size" => "5"));
		$nuCargaDistancia->setDescription("Em horas");
		$nuCargaCurso = $this->createElement('text', 'NU_CARGA_CURSO',
				array("label" => "Carga curso: ", "disabled" => "disabled", "size" => "5"));
		$nuCargaCurso->setDescription("Em horas");
		$nuMinConclusao = $this->createElement('text', 'NU_MIN_CONCLUSAO',
				array("label" => "Mínimo p/ Conclusão: ", "disabled" => "disabled", "size" => "5"));
		$nuMinConclusao->setDescription("Em dias");
		$nuMaxConclusao = $this->createElement('text', 'NU_MAX_CONCLUSAO',
				array("label" => "Máximo p/ Conclusão: ", "disabled" => "disabled", "size" => "5"));
		$nuMaxConclusao->setDescription("Em dias");

		// Adiciona os elementos ao formulário
		$this->addElements(
				array($nuMinAlunos, $nuCargaPresencial, $nuCargaDistancia, $nuCargaCurso, $nuMinConclusao,
						$nuMaxConclusao,));
	}

	/**
	 * Seta o valor das opções do combo de Tipo de Curso.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setTipoCurso( $options ) {
		$element = $this->getElement('NU_SEQ_TIPO_CURSO_CAD');
		$element->setMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Curso.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setCurso( $options ) {
		$element = $this->getElement('NU_SEQ_CURSO_CAD');
		$element->addMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Curso.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setTutor( $options ) {
		$element = $this->getElement('NU_SEQ_USUARIO_TUTOR');
		$element->addMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Articulador.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setArticulador( $options ) {
		$element = $this->getElement('NU_SEQ_USUARIO_ARTICULADOR');
		$element->addMultiOptions($options);
	}

	/**
	 * Seta o valor dos campos de infos de acordo com o Curso selecionado.
	 * @param array $infoCurso Array valido com as opções.
	 */
	public function setInfoCurso( $infoCurso ) {

		$this->getElement('NU_MIN_ALUNOS')->setValue($infoCurso['NU_MIN_ALUNOS']);
		$this->getElement('NU_CARGA_PRESENCIAL')->setValue($infoCurso['NU_CARGA_PRESENCIAL']);
		$this->getElement('NU_CARGA_DISTANCIA')->setValue($infoCurso['NU_CARGA_DISTANCIA']);
		$this->getElement('NU_CARGA_CURSO')->setValue($infoCurso['NU_CARGA_CURSO']);
		$this->getElement('NU_MIN_CONCLUSAO')->setValue($infoCurso['NU_MIN_CONCLUSAO']);
		$this->getElement('NU_MAX_CONCLUSAO')->setValue($infoCurso['NU_MAX_CONCLUSAO']);

	}

	/**
	 * Seta o valor das opções do combo de UF.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setUf( $options ) {
		$element = $this->getElement('UF_TURMA_CAD');
		$element->addMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Municipio.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setMunicipio( $options ) {
		$element = $this->getElement('CO_MUNICIPIO_CAD');
		$element->addMultiOptions($options);
	}

	/**
	 * Seta o valor das opções do combo de Mesorregiao.
	 * @param array $arOptions Array valido com as opções.
	 */
	public function setMesorregiao( $options ) {
		$element = $this->getElement('NO_MESORREGIAO_CAD');
		$element->addMultiOptions($options);
	}

	/**
	 * Disabilita os campos.
	 */
	public function setDisable() {
		$this->getElement('NU_SEQ_TIPO_CURSO_CAD')->setAttrib("disabled", true);
		$this->getElement('NU_SEQ_CURSO_CAD')->setAttrib("disabled", true);
		$this->getElement('NU_MIN_ALUNOS')->setAttrib("disabled", true);
		$this->getElement('NU_CARGA_PRESENCIAL')->setAttrib("disabled", true);
		$this->getElement('NU_CARGA_DISTANCIA')->setAttrib("disabled", true);
		$this->getElement('NU_CARGA_CURSO')->setAttrib("disabled", true);
		$this->getElement('NU_MIN_CONCLUSAO')->setAttrib("disabled", true);
		$this->getElement('NU_MAX_CONCLUSAO')->setAttrib("disabled", true);
		//$this->getElement('DT_INICIO')->setAttrib("disabled", true);
		//$this->getElement('DT_FIM')->setAttrib("disabled", true);
		$this->getElement('DT_FINALIZACAO')->setAttrib("disabled", true);
		//alterado conforme solicitação do Mantis
		$this->getElement('UF_TURMA_CAD')->setAttrib("disabled", true);
		//$this->getElement('CO_MUNICIPIO_CAD')->setAttrib("disabled", false);
	}

	/**
	 * Seta o valor de Tipo de Curso pelo curso.
	 * @param $value
	 */
	public function setValueTipoCurso( $value ) {
		$element = $this->getElement('NU_SEQ_TIPO_CURSO_CAD');
		$element->setValue($value);
	}

}
