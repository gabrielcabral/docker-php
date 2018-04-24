<?php

/**
 * Controller do FormacaoAcademica
 * 
 * @author diego.matos
 * @since 10/04/2012
 */

class Manutencao_FormacaoAcademicaController extends Fnde_Sice_Controller_Action {

	/**
	 * Função de pesquisa
	 * 
	 * @author diego.matos
	 * @since 10/04/2012 
	 */
	public function listAction() {

		$this->_helper->layout()->disableLayout();

		$arParam = $this->_getAllParams();

		$grid = new Fnde_View_Helper_DataTables();
		$grid->setActionColumn("Ação");
		$grid->setFooterActive(false);

		if ( $arParam['READONLY'] != 1 ) {
			$rowAction = array(
					'excluir' => array('label' => 'Excluir',
							'url' => $this->view->Url(array('action' => 'del-formacao', 'ID' => '')) . '%s',
							'params' => array('ID'),
							'attribs' => array('class' => 'icoExcluir',
									'mensagem' => 'Deseja realmente excluir o registro?', 'title' => 'Excluir')),);
		} else {
			$rowAction = array(
					'excluir' => array('label' => 'Excluir', 'url' => "#e", 'params' => array('ID'),
							'attribs' => array('class' => 'icoExcluir disabled', 'mensagem' => '', 'title' => 'Excluir')),);
		}
		$arrHeader = array('Escolaridade', 'Tipo Instituição', 'Instituição', 'Curso', 'Data Conclusão',);

		$rsData = $this->getRsDataDaSessao();
		$grid->setHeaderActive(false);
		$grid->setAutoCallJs(true);
		$this->view->grid = $grid->setData($rsData)->setHeader($arrHeader)->setRowAction($rowAction)->setId('ID')->setColumnsHidden(
				array('ID', 'TP_ESCOLARIDADE', 'TP_INSTITUICAO'))->setTableAttribs(
				array('id' => 'edit', 'style' => 'text-align:center'));

	}

	/**
	 * Método para adicionar uma formção acadêmica à seção para ser gravada no banco de dados.
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function addFormacaoAction() {

		$this->_helper->layout()->disableLayout();
		$arParam = $this->_getAllParams();

		$rsData = $this->getRsDataDaSessao();

		if ( count($rsData) == 0 ) {
			$rsData[] = array('ID' => count($rsData), 'DS_TP_ESCOLARIDADE' => $arParam['DS_TP_ESCOLARIDADE'],
					'DS_TP_INSTITUICAO' => $arParam['DS_TP_INSTITUICAO'],
					'TP_ESCOLARIDADE' => $arParam['TP_ESCOLARIDADE'], 'TP_INSTITUICAO' => $arParam['TP_INSTITUICAO'],
					'NO_INSTITUICAO' => $arParam['NO_INSTITUICAO'], 'NO_CURSO' => $arParam['NO_CURSO'],
					'DT_CONCLUSAO' => str_replace("-", "/", $arParam['DT_CONCLUSAO']),);

		} else {
			for ( $i = 0; $i < count($rsData); $i++ ) {
				$data = $rsData[$i];
				if ( $data['DS_TP_ESCOLARIDADE'] == $arParam['DS_TP_ESCOLARIDADE'] ) {
					$erro = true;
					break;
				}
			}

			if ( !$erro ) {
				$rsData[] = array('ID' => count($rsData), 'DS_TP_ESCOLARIDADE' => $arParam['DS_TP_ESCOLARIDADE'],
						'DS_TP_INSTITUICAO' => $arParam['DS_TP_INSTITUICAO'],
						'TP_ESCOLARIDADE' => $arParam['TP_ESCOLARIDADE'],
						'TP_INSTITUICAO' => $arParam['TP_INSTITUICAO'], 'NO_INSTITUICAO' => $arParam['NO_INSTITUICAO'],
						'NO_CURSO' => $arParam['NO_CURSO'],
						'DT_CONCLUSAO' => str_replace("-", "/", $arParam['DT_CONCLUSAO']),);

			} else {
				$this->addMessage(Fnde_Message::MSG_ERROR, "Formação acadêmica já adicionada.");
				$this->_redirect("/manutencao/formacaoacademica/list");
			}
		}

		$_SESSION["rsDataFormacaoAcademica"] = $rsData;
		$this->_redirect("/manutencao/formacaoacademica/list");
	}

	/**
	 * Método para remover uma formação acadêmica.
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function delFormacaoAction() {

		$this->_helper->layout()->disableLayout();

		$arParam = $this->_getAllParams();

		$rsData = $this->getRsDataDaSessao();

		$index = null;
		for ( $i = 0; $i < count($rsData); $i++ ) {
			$data = $rsData[$i];
			if ( $data['ID'] == $arParam['ID'] ) {
				$index = $i;
				break;
			}
		}

		unset($rsData[$index]);

		$_SESSION["rsDataFormacaoAcademica"] = array_values($rsData);

		$this->_redirect("/manutencao/formacaoacademica/list");
	}

	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function formAction() {
		$this->setTitle('Formacao Acadêmica');
		$this->setSubtitle('Cadastro');

		$this->_helper->layout()->disableLayout();

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'formacaoacademica', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'formacaoacademica', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		//Recupera o objeto de formulário para validação
		$form = $this->getForm(null, null);

		if ( $arDados['NU_SEQ_FORMACAO_ACADEMICA'] ) {
			$this->view->form = $form->populate($arDados);
		} else {
			$this->view->form = $this->getForm();
		}
		$this->render('form');
	}

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {

		//     	$params = $this->_getAllParams();

		//     	$form = new FormacaoAcademica_Form($arDados, $arExtra);
		//     	$form->setDecorators(array('FormElements', 'Form'));
		//     	$form->setAction($this->view->baseUrl() . '/index.php/manutencao/formacaoacademica/save')->setMethod('post')->setAttrib('id', 'form');

		//     	$html = $form->getElement("htmlTabelaFormacaoAcademica");

		//     	$str = '<h4><div class="" align="left">Nova Configuração</div></h4><br/>'.$this->retornaHtmlNovaConf()."<br/>"
		//     	.$this->retornaHtmlTurma()."<br/>".$this->retornaHtmlGridBolsa()."<br/>".$this->retornaHtmlGridCriterioAvaliacao()."</fieldset></fieldset>";

		//     	$html->setValue($str);

		//     	return $form;

		$form = new FormacaoAcademica_Form($arDados, $arExtra);
		$this->setEscolaridadeForm($form);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/formacaoacademica/save')->setMethod('post')->setAttrib(
				'id', 'form');

		return $form;

	}

	/**
	 * Insere os valores no select de Escolaridade
	 * @author gustavo.gomes
	 * @param array $rsEscolaridade
	 */
	public function setEscolaridadeForm( $form ) {
		$rsEscolaridade = Fnde_Sice_Business_Componentes::getAllByTable("FormacaoAcademica",
				array("NU_SEQ_FORMACAO_ACADEMICA", "DS_FORMACAO_ACADEMICA"));

		$form->setEscolaridade($rsEscolaridade);
	}

	/**
	 * Método que recupera as informações adicionadas à seção para serem persistidas no banco de dados.
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	private function getRsDataDaSessao() {
		$rsData = $_SESSION['rsDataFormacaoAcademica'];
		if ( !isset($rsData) ) {
			$rsData = array();
			$_SESSION['rsDataFormacaoAcademica'] = $rsData;
		}

		foreach ( $rsData as $key ) {
			$sortEsc[] = $key['DS_TP_ESCOLARIDADE'];
			$sortRef[] = $key['ID'];
		}

		array_multisort($sortEsc, SORT_STRING, $sortRef, SORT_ASC, $rsData);

		return $rsData;
	}
}
