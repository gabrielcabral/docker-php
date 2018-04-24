<?php

/**
 * Form de cadastro Curso
 *
 * @author vinicius.cancado
 * @since 18/04/2012
 */
class Curso_Form extends Fnde_Form {

	/**
	 * Variavel para guardar array para DisplayGroup
	 * @var array
	 */
	private $_arrayGrupo;

	/**
	 * Construtor do formulário da tela de Curso.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function __construct( $arDados, $arExtra ) {
		$this->setAttrib("class", "labelLongo");
		//Adicionando elementos no formulário
		$nuCurso = $this->createElement('hidden', 'NU_SEQ_CURSO');
		$nuCurso->setValue($arDados['NU_SEQ_CURSO']);

		$this->setElementosDadosCadastrais($arDados);

		$nudsObjetivosCurso = $this->createElement('textarea', 'DS_OBJETIVO_CURSO',
				array("label" => "Descrição: ",
						'value' => str_replace(array('\r\n', '\r', '\n'), "\n", $arDados['DS_OBJETIVO_CURSO']),
						"style" => "height:150px;", "maxlength" => "3700"))->setRequired(true);

		$nuTotalHoras = $this->createElement('text', 'TOTAL_HORAS',
				array("label" => "Total de horas: ", 'value' => $arDados['TOTAL_HORAS'], 'class' => 'inteiro',
						"size" => 5, "disabled" => "disabled"));

		// Adiciona os elementos ao formulário
		$this->addElements(array($nuCurso, $nudsObjetivosCurso, $nuTotalHoras,));

		$this->addDisplayGroup(
				array('NU_SEQ_CURSO', 'NU_SEQ_TIPO_CURSO', 'DS_SIGLA_CURSO', 'DS_NOME_CURSO', 'VL_CARGA_HORARIA',
						'QT_MODULOS', 'ST_CURSO', 'DS_PREREQUISITO_CURSO', 'NU_SEQ_CURSO_PREREQUISITO',), 'dadoscurso',
				array("legend" => "Dados Cadastrais"));

		$this->addDisplayGroup(array('DS_OBJETIVO_CURSO',), 'objetivoscurso', array("legend" => "Objetivo do Curso"));

		$qtn = $this->createElement("hidden", "qtn")->setValue( ( $arDados["qtn"] > 0 ? $arDados["qtn"] : 1 ));

		$btnAdicionar = $this->createElement('button', 'adicionar',
				array("label" => "Adicionar", "value" => "Adicionar", "class" => "btnAdicionar", "title" => "Adicionar"));

		$this->addElements(array($qtn, $btnAdicionar));

		$arrayGrupo = array();

		$idx = $this->createElement('hidden', 'idx');

		for ( $idx->setValue(0); $idx->getValue() < $qtn->getValue(); $idx->setValue($idx->getValue() + 1) ) {

			$seqModulo = $this->createElement('select', 'NU_SEQ_MODULO',
					array('name' => 'NU_SEQ_MODULO' . $idx->getValue(), 'label' => 'Módulo:', "required" => true,
							'class' => 'selectModulo',));

			$seqModulo->addMultiOption(null, "Selecione");

			$this->addElement($seqModulo);

			$this->getElement("NU_SEQ_MODULO" . $idx->getValue())->setValue($arExtra[$idx->getValue()]["NU_SEQ_MODULO"]);
			$arrayGrupo[] = 'NU_SEQ_MODULO' . $idx->getValue();
		}

		$arrayGrupo[] = 'adicionar';
		$arrayGrupo[] = 'TOTAL_HORAS';

		$this->setArrayGrupo($arrayGrupo);

		$addDisplayGroup = $this->addDisplayGroup($arrayGrupo, 'modulos', array("legend" => "Módulos", 'class' => ''));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar",
						"onclick" => "$(this.form).find(':disabled').attr('disabled',false);", "title" => "Confirmar"));

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/curso/list/'"));

		//Adicionado Componentes no formulário
		$this->addElements(array($idx, $btConfirmar, $btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		for ( $idx->setValue(0); $idx->getValue() < $qtn->getValue(); $idx->setValue($idx->getValue() + 1) ) {
			$this->getElement("NU_SEQ_MODULO" . $idx->getValue())->addDecorator("htmlTag",
					array("tag" => "div", "class" => ( $idx->getValue() > 0 ? "remove" : "" )));
		}

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		$addDisplayGroup->getElement("adicionar")->addDecorator('HtmlTag',
				array('div' => 'label', "id" => "divBotaoAdcionar",
						"style" => "padding-left:240px !important; margin:10px;"));

		return $this;
	}

	/**
	 * Cria e adiciona os elementos do fieldset Dados Cadastrais.
	 */
	private function setElementosDadosCadastrais( $arDados ) {
		$nunuSeqTipoCurso = $this->createElement('select', 'NU_SEQ_TIPO_CURSO',
				array('name' => 'NU_SEQ_TIPO_CURSO', 'label' => 'Tipo de Curso: ', $arDados['NU_SEQ_TIPO_CURSO']));

		$nunuSeqTipoCurso->setRequired(true)->addMultiOption(null, "Selecione")->setValue($arDados['NU_SEQ_TIPO_CURSO']);

		$nudsSiglaCurso = $this->createElement('text', 'DS_SIGLA_CURSO',
				array("label" => "Sigla do curso: ", 'value' => $arDados['DS_SIGLA_CURSO'], 'maxlength' => '15'))->setRequired(
				true);

		$nudsNomeCurso = $this->createElement('text', 'DS_NOME_CURSO',
				array("label" => "Nome do curso: ", 'value' => $arDados['DS_NOME_CURSO'], 'size' => '100',
						'maxlength' => '150'));
		$nudsNomeCurso->setRequired(true);

		$nuvlCargaHoraria = $this->createElement('text', 'VL_CARGA_HORARIA',
				array("label" => "Carga horária: ", 'value' => $arDados['VL_CARGA_HORARIA'], 'class' => 'inteiro',
						'maxlength' => '3', "size" => 5))->setRequired(true);

		$nuqtModulos = $this->createElement('text', 'QT_MODULOS',
				array("label" => "Qtd. módulo(s): ", 'value' => $arDados['QT_MODULOS'], 'class' => 'inteiro',
						'maxlength' => '3', "size" => 5))->setRequired(true);

		$nustCurso = $this->createElement('select', 'ST_CURSO',
				array("label" => "Situação: ", 'value' => $arDados['ST_CURSO']))->setValue($arDados['ST_CURSO']);
		$nustCurso->addMultiOption('', 'Selecione')->addMultiOption('A', 'Ativo')->addMultiOption('D', 'Inativo')->setRequired(
				true);

		$nudsPrerequisitoCurso = $this->createElement('select', 'DS_PREREQUISITO_CURSO',
				array("label" => "Pré-requisito: ", 'value' => $arDados['DS_PREREQUISITO_CURSO']))->setRequired(true);
		$nudsPrerequisitoCurso->addMultiOption(null, 'Selecione')->addMultiOption('N', 'Não')->addMultiOption('S',
				'Sim');

		$nuSCurso = $this->createElement('select', 'NU_SEQ_CURSO_PREREQUISITO',
				array('name' => 'NU_SEQ_CURSO_PREREQUISITO', 'label' => 'Curso de pré-requisito:',
						$arDados['NU_SEQ_CURSO_PREREQUISITO'], 'required' => true))->addMultiOption(null, 'Selecione');

		$this->addElements(
				array($nunuSeqTipoCurso, $nudsSiglaCurso, $nudsNomeCurso, $nuvlCargaHoraria, $nuqtModulos, $nustCurso,
						$nudsPrerequisitoCurso, $nuSCurso));
	}

	/**
	 * 
	 * Método para inserir o array de tipo de curso no select
	 * 
	 * @author gustavo.gomes 
	 * @param array $rsTipoCurso
	 */
	public function setTipoCurso( $rsTipoCurso ) {
		$this->getElement('NU_SEQ_TIPO_CURSO')->addMultiOptions($rsTipoCurso);
	}

	/**
	 *
	 * Método para inserir o array de pre requisito de curso no select
	 *
	 * @author gustavo.gomes
	 * @param array $rsCursoPrerequisito
	 */
	public function setCursoPreRequisito( $rsCursoPrerequisito ) {

		$this->getElement('NU_SEQ_CURSO_PREREQUISITO')->addMultiOptions($rsCursoPrerequisito);

	}

	/**
	 *
	 * Método para inserir o array de pre requisito de modulo no select
	 *
	 * @author gustavo.gomes
	 * @param array $rsModuloPrerequisito
	 */
	public function setModuloPreRequisito( $rsModuloPrerequisito ) {
		for ( $i = 0; $sair == false; $i++ ) {
			if ( $this->getElement('NU_SEQ_MODULO' . $i) ) {
				$this->getElement('NU_SEQ_MODULO' . $i)->addMultiOptions($rsModuloPrerequisito);
			} else {
				$sair = true;
			}
		}
	}

	/**
	 *
	 * Método para bloquear os campos no formulário
	 *
	 * @author gustavo.gomes
	 */
	public function bloqueiaCampos() {

		$btnAdicionar = $this->getElement('adicionar');

		$qtn = $this->getElement('qtn');

		$idx = $this->getElement('idx')->getValue();

		$arrayGrupo = $this->getArrayGrupo();

		$this->getElement('NU_SEQ_TIPO_CURSO')->setAttrib("disabled", "disabled");
		$this->getElement('VL_CARGA_HORARIA')->setAttrib("disabled", "disabled");
		$this->getElement('QT_MODULOS')->setAttrib("disabled", "disabled");
		$this->getElement('ST_CURSO')->setAttrib("disabled", "disabled");
		$this->getElement('DS_OBJETIVO_CURSO')->setAttrib("disabled", "disabled");
		$this->getElement('NU_SEQ_CURSO_PREREQUISITO')->setAttrib("disabled", "disabled");
		for ( $idx = 0; $idx < $qtn->getValue(); $idx++ ) {
			$this->getElement("NU_SEQ_MODULO" . $idx)->setAttrib("disabled", "disabled");
		}
		$btnAdicionar->setAttrib("disabled", "disabled");

		$this->addDisplayGroup($arrayGrupo, 'modulos', array("legend" => "Módulos", 'class' => 'readonly'));

	}

	/**
	 *  Método que retorna arrayGrupo
	 */
	public function getArrayGrupo() {
		return $this->_arrayGrupo;
	}

	/**
	 *
	 * Método para inserir em arrayGrupo
	 */
	public function setArrayGrupo( $arrayGrupo ) {
		$this->_arrayGrupo = $arrayGrupo;
	}

}
