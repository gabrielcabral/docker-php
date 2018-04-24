<?php

/**
 * Form de cadastro Modulo
 *
 * @author vinicius.cancado
 * @since 18/04/2012
 */
class Modulo_Form extends Fnde_Form {
	/**
	 * Construtor do formul�rio da tela de M�dulo.
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {

		$this->setAttrib("class", "labelLongo");
		//Adicionando elementos no formul�rio
		$nuModulo = $this->createElement('hidden', 'NU_SEQ_MODULO');
		$nuModulo->setValue($arDados['NU_SEQ_MODULO']);

		$vlCargaHorariaHidden = $this->createElement('hidden', 'VL_CARGA_HORARIA_HIDDEN');
		$vlCargaHorariaHidden->setValue($arDados['VL_CARGA_HORARIA']);

		$nudsConteudoProgramaticoHidden = $this->createElement('hidden', 'DS_CONTEUDO_PROGRAMATICO_HIDDEN');
		$nudsConteudoProgramaticoHidden->setValue($arDados['DS_CONTEUDO_PROGRAMATICO']);

		$this->setElementosDadosCadastrais($arDados);

		$nudsConteudoProgramatico = $this->createElement('textarea', 'DS_CONTEUDO_PROGRAMATICO',
				array("label" => "Descri��o: ",
						'value' => str_replace(array('\r\n', '\r', '\n'), "\n", $arDados['DS_CONTEUDO_PROGRAMATICO']),
						"style" => "height:150px;", "maxlength" => "3700"));
		$nudsConteudoProgramatico->setRequired(true);

		// Adiciona os elementos ao formul�rio
		$this->addElements(
				array($nuModulo, $vlCargaHorariaHidden, $nudsConteudoProgramaticoHidden, $nudsConteudoProgramatico));

		$this->addDisplayGroup(
				array('NU_SEQ_MODULO', 'VL_CARGA_HORARIA_HIDDEN', 'DS_CONTEUDO_PROGRAMATICO_HIDDEN',
						'NU_SEQ_TIPO_CURSO', 'DS_SIGLA_MODULO', 'DS_NOME_MODULO', 'VL_CARGA_HORARIA',
						'VL_CARGA_PRESENCIAL', 'VL_CARGA_DISTANCIA', 'ST_MODULO', 'DS_PREREQUISITO_MODULO',
						'NU_SEQ_MODULO_PREREQUISITO', 'VL_MIN_CONCLUSAO', 'VL_MAX_CONCLUSAO',), 'dadosmodulo',
				array("legend" => "Dados Cadastrais"));

		$this->addDisplayGroup(array('DS_CONTEUDO_PROGRAMATICO'), 'conteudoProgramatico',
				array("legend" => "Conte�do Program�tico"));

		$btVisualizarCertificado = $this->createElement('button', 'visualizarCertificado',
				array("label" => "Pr�-visualizar Certificado", "value" => "Pr�-visualizar Certificado",));

		$this->addElements(array($btVisualizarCertificado));
		//         $obDisplayGroup = $this->addDisplayGroup(array('visualizarCertificado'), 'botoes', array('class'=>'agrupador barraBtsAcoes'));

		$btConfirmar = $this->createElement('button', 'confirmar');
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/modulo/list/'"));

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

	private function setElementosDadosCadastrais( $arDados ) {
		$nunuSeqTipoCurso = $this->createElement('select', 'NU_SEQ_TIPO_CURSO',
				array('name' => 'NU_SEQ_TIPO_CURSO', 'label' => 'Tipo de Curso: ', $arDados['NU_SEQ_TIPO_CURSO']));

		$nunuSeqTipoCurso->setRequired(true);
		$nunuSeqTipoCurso->setValue($arDados['NU_SEQ_TIPO_CURSO']);

		$nudsSiglaModulo = $this->createElement('text', 'DS_SIGLA_MODULO',
				array("label" => "Sigla do m�dulo: ", 'value' => $arDados['DS_SIGLA_MODULO'], "maxlength" => "15"));
		$nudsSiglaModulo->setAttrib('maxlength', '15');
		$nudsSiglaModulo->setRequired(true);

		$nudsNomeModulo = $this->createElement('text', 'DS_NOME_MODULO',
				array("label" => "Nome do m�dulo: ", 'value' => $arDados['DS_NOME_MODULO'], "maxlength" => "150"));
		$nudsNomeModulo->setRequired(true);
		$nudsNomeModulo->setAttrib("size", 100);
		$nudsNomeModulo->setAttrib('maxlength', '150');

		$nuvlCargaHoraria = $this->createElement('text', 'VL_CARGA_HORARIA',
				array("label" => "Carga hor�ria: ", 'value' => $arDados['VL_CARGA_HORARIA'], "class" => "inteiro",
						"maxlength" => "3"));
		$nuvlCargaHoraria->setAttrib("size", 5);
		$nuvlCargaHoraria->setRequired(true);

		$nuvlCargaPresencial = $this->createElement('text', 'VL_CARGA_PRESENCIAL',
				array("label" => "Carga Presencial: ", 'value' => $arDados['VL_CARGA_PRESENCIAL'],
						"class" => "inteiro", "maxlength" => "3"));
		$nuvlCargaPresencial->setAttrib("size", 5);
		$nuvlCargaPresencial->setRequired(true);
		$nuvlCargaDistancia = $this->createElement('text', 'VL_CARGA_DISTANCIA',
				array("label" => "Carga a Dist�ncia: ", 'value' => $arDados['VL_CARGA_DISTANCIA'],
						"class" => "inteiro", "maxlength" => "3"));
		$nuvlCargaDistancia->setAttrib("size", 5);
		$nuvlCargaDistancia->setRequired(true);

		$nustModulo = $this->createElement('select', 'ST_MODULO',
				array("label" => "Situa��o: ", 'value' => $arDados['ST_MODULO']));
		$nustModulo->addMultiOption('', 'Selecione');
		$nustModulo->addMultiOption('A', 'Ativo');
		$nustModulo->addMultiOption('D', 'Inativo');
		$nustModulo->setRequired(true);
		$nustModulo->setValue($arDados['ST_MODULO']);

		$nudsPrerequisitoModulo = $this->createElement('select', 'DS_PREREQUISITO_MODULO',
				array("label" => "Pr�-requisito: ", 'value' => $arDados['DS_PREREQUISITO_MODULO']));
		$nudsPrerequisitoModulo->addMultiOption('', 'Selecione');
		$nudsPrerequisitoModulo->addMultiOption('S', 'Sim');
		$nudsPrerequisitoModulo->addMultiOption('N', 'N�o');
		$nudsPrerequisitoModulo->setRequired(true);

		$nuSeqModuloPrerequisito = $this->createElement('select', 'NU_SEQ_MODULO_PREREQUISITO',
				array('name' => 'NU_SEQ_MODULO_PREREQUISITO', 'label' => 'M�dulo de pr�-requisito:',
						'required' => true, $arDados['NU_SEQ_MODULO_PREREQUISITO']));

		if ( $arDados['DS_PREREQUISITO_MODULO'] == "S" ) {
			$nuSeqModuloPrerequisito->setValue($arDados['NU_SEQ_MODULO_PREREQUISITO']);
		}

		$nuvlMinConclusao = $this->createElement('text', 'VL_MIN_CONCLUSAO',
				array("label" => "M�nimo p/conclus�o: ", 'value' => $arDados['VL_MIN_CONCLUSAO'], "class" => "inteiro",
						"maxlength" => "3"));
		$nuvlMinConclusao->setAttrib("size", 5);
		$nuvlMinConclusao->setRequired(true);
		$nuvlMaxConclusao = $this->createElement('text', 'VL_MAX_CONCLUSAO',
				array("label" => "M�ximo p/conclus�o: ", 'value' => $arDados['VL_MAX_CONCLUSAO'], "class" => "inteiro",
						"maxlength" => "3"));
		$nuvlMaxConclusao->setAttrib("size", 5);
		$nuvlMaxConclusao->setRequired(true);

		// Adiciona os elementos ao formul�rio
		$this->addElements(
				array($nuSeqModuloPrerequisito, $nunuSeqTipoCurso, $nudsSiglaModulo, $nudsNomeModulo,
						$nuvlCargaHoraria, $nuvlCargaPresencial, $nuvlCargaDistancia, $nustModulo,
						$nudsPrerequisitoModulo, $nuvlMinConclusao, $nuvlMaxConclusao));
	}

	/**
	 *
	 * M�todo para inserir o tipo de curso no select
	 *
	 * @author gustavo.gomes
	 *
	 * @param $rsTipoCurso
	 */
	public function setTipoCurso( $rsTipoCurso ) {

		$tipoCurso = $this->getElement('NU_SEQ_TIPO_CURSO')->addMultiOption(null, "Selecione");
		$tipoCurso->addMultiOptions($rsTipoCurso);

	}

	/**
	 * M�todo para inserir os preRequisitos no select
	 * @author gustavo.gomes
	 * @param  $preRequisito
	 */
	public function setPreRequisito( $preRequisito ) {
		$obj = $this->getElement('NU_SEQ_MODULO_PREREQUISITO');
		$obj->addMultiOption(null, "Selecione");
		$obj->addMultiOptions($preRequisito);
	}

	/**
	 * M�todo que retorna nuModulo
	 * @author gustavo.gomes
	 * @return void
	 */
	public function getNuModulo() {

		$nuModulo = $this->getElement('NU_SEQ_MODULO');
		return $nuModulo;

	}

	/**
	 * M�todo para desabilitar campos no formulario
	 * @author gustavo.gomes
	 */
	public function setDisabled() {

		$this->getElement('NU_SEQ_TIPO_CURSO')->setAttrib("disabled", "disabled");
		$this->getElement('VL_CARGA_HORARIA')->setAttrib("disabled", "disabled");
		$this->getElement('VL_CARGA_PRESENCIAL')->setAttrib("disabled", "disabled");
		$this->getElement('VL_CARGA_DISTANCIA')->setAttrib("disabled", "disabled");
		$this->getElement('ST_MODULO')->setAttrib("disabled", "disabled");
		$this->getElement('DS_PREREQUISITO_MODULO')->setAttrib("disabled", "disabled");
		$this->getElement('NU_SEQ_MODULO_PREREQUISITO')->setAttrib("disabled", "disabled");
		$this->getElement('VL_MIN_CONCLUSAO')->setAttrib("disabled", "disabled");
		$this->getElement('VL_MAX_CONCLUSAO')->setAttrib("disabled", "disabled");
		$this->getElement('DS_CONTEUDO_PROGRAMATICO')->setAttrib("disabled", "disabled");

	}

	/**
	 * M�todo de inser��o dos atributos do bot�o Confirmar
	 * @author gustavo.gomes
	 * @param $verificador
	 */
	public function setAtribsConfirmar( $verificador ) {

		$btConfirmar = $this->getElement('confirmar');

		if ( $verificador == 'toDisabled' ) {
			$atributos = array("label" => "Confirmar", "value" => "Confirmar",
					"mensagem" => "O m�dulo est� vinculado a um curso. Deseja realmente alterar este m�dulo?",
					"class" => "btnConfirmar", "title" => "Confirmar");
		} else {
			$atributos = array("label" => "Confirmar", "value" => "Confirmar", "mensagem" => "",
					"class" => "btnConfirmar", "onclick" => "$(this.form).find(':disabled').attr('disabled',false);",
					"title" => "Confirmar");
		}
		$btConfirmar->setAttribs($atributos);

	}

}
