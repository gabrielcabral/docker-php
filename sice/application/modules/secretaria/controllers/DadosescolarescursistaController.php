<?php

/**
 * Controller do DadosEscolaresCursista
 * 
 * @author diego.matos
 * @since 25/04/2012
 */

class Secretaria_DadosEscolaresCursistaController extends Fnde_Sice_Controller_Action {

	/**
	 * A��o de listagem
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function listAction() {
		$this->setTitle('DadosEscolaresCursista');
		$this->setSubtitle('Filtrar');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'dadosescolarescursista', 'list', ' ') => 'filtrar',
				$this->getUrl('secretaria', 'dadosescolarescursista', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		//seta novos valores na sess�o
		if ( $this->_request->isPost() ) {
			parent::setSearchParam();
		}

		//recupera valores da sess�o
		$arFilter = parent::getSearchParam();

		$form = $this->getFormFilter();
		$form->populate($arFilter);

		$rsRegistros = array();

		if ( $this->_request->isPost() || isset($arFilter['startlist']) || isset($arFilter['start'])
				|| !empty($arFilter) ) {
			if ( $form->isValid($arFilter) ) {

				$obBusiness = new Fnde_Sice_Business_DadosEscolaresCursista();
				$rsRegistros = $obBusiness->search($form->getValues());
				if ( !count($rsRegistros) ) {
					$this->addInstantMessage(Fnde_Message::MSG_INFO,
							'N�o foram encontrados registros para os filtros informados!');
				}
			} else {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
				$this->addInstantMessage(Fnde_Message::MSG_ERROR,
						Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			}
		}

		//chama filtro form
		$this->view->formFilter = $form;

		//Chamando componente zend.grid dentro do helper
		if ( $rsRegistros ) {

			$rowAction = array(
					'edit' => array('label' => 'editar',
							'url' => $this->view->Url(array('action' => 'form', 'id' => '')) . '%s',
							'params' => array('NU_SEQ_USUARIO_CURSISTA'), 'attribs' => array('class' => 'icoEditar')),
					'delete' => array('label' => 'Excluir',
							'url' => $this->view->Url(
									array('action' => 'del-dadosescolarescursista', 'NU_SEQ_USUARIO_CURSISTA' => ''))
									. '%s', 'params' => array('NU_SEQ_USUARIO_CURSISTA'),
							'attribs' => array('class' => 'icoExcluir excluir',
									'mensagem' => 'Confirma a exclus�o das informa��es a regional selecionada?')));

			$arrHeader = array('coMesorregiao', 'coSegmento', 'coRedeEnsino', 'coMunicipioEscola', 'coEscola',
					'coUfEscola',);

			$grid = new Fnde_View_Helper_DataTables();
			$grid->setAutoCallJs(true);
			$this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setTitle('DadosEscolaresCursista')->setRowAction(
					$rowAction)->setId('NU_SEQ_USUARIO_CURSISTA')->setColumnsHidden(array('NU_SEQ_USUARIO_CURSISTA'))->setRowInput(
					Fnde_View_Helper_DataTables::INPUT_TYPE_CHECKBOX)->setTableAttribs(array('id' => 'edit'));
		}
	}

	/**
	 * Remove um registro de DadosEscolaresCursista
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function delDadosEscolaresCursistaAction() {
		$arParam = $this->_getAllParams();

		$obDadosEscolaresCursista = new Fnde_Sice_Business_DadosEscolaresCursista();
		$resposta = $obDadosEscolaresCursista->del($arParam['NU_SEQ_USUARIO_CURSISTA']);

		$resposta = ( string ) $resposta;

		if ( $resposta ) {
			$this->addMessage(Fnde_Message::MSG_SUCCESS, "Opera��o realizada com sucesso!");
		} elseif ( $resposta == '0' ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, "Exclus�o do registro j� realizada.");
		} else {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"DadosEscolaresCursista n�o pode ser exclu�do, pois o mesmo est� associado." . $resposta);
		}

		$this->_redirect("/secretaria/dadosescolarescursista/list");
	}

	/**
	 * Monta o formul�rio e renderiza na view
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function formAction() {
		$this->setTitle('DadosEscolaresCursista');
		$this->setSubtitle('Cadastro');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'dadosescolarescursista', 'list', ' ') => 'filtrar',
				$this->getUrl('secretaria', 'dadosescolarescursista', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		// Recuperando array de dados do banco para setar valores no formul�rio
		$arDados = $this->getArDadosFormulario();

		//Recuperando array de dados extras para setar valores extras no formul�rio
		$arExtra = $this->getArExtraFormulario();

		//Recupera o objeto de formul�rio para valida��o
		$form = $this->getForm($arDados, $arExtra);

		if ( $arDados['NU_SEQ_USUARIO_CURSISTA'] ) {
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
	 * @since 25/04/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {

		$this->setTitles(
				array('nuSeqUsuarioCursista', 'coMesorregiao', 'coSegmento', 'coRedeEnsino', 'coMunicipioEscola',
						'coEscola', 'coUfEscola',));
		$this->setNameList(
				array('NU_SEQ_USUARIO_CURSISTA', 'CO_MESORREGIAO', 'CO_SEGMENTO', 'CO_REDE_ENSINO',
						'CO_MUNICIPIO_ESCOLA', 'CO_ESCOLA', 'CO_UF_ESCOLA',));

		$form = new DadosEscolaresCursista_Form();
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/dadosescolarescursista/save')->setMethod(
				'post')->setAttrib('id', 'form');

		return $form;
	}

	/**
	 * Metodo acessorio get de namelist.
	 */
	public function getNameList() {
		return $this->_arList;
	}

	/**
	 * Metodo acessorio set de namelist.
	 * @param array $arList
	 */
	public function setNameList( $arList ) {
		$this->_arList = $arList;
	}

	/**
	 * Metodo acessorio get de titles.
	 */
	public function getTitles() {
		return $this->_arTitles;
	}

	/**
	 * Metodo acessorio set de titles.
	 * @param unknown_type $arTitles
	 */
	public function setTitles( $arTitles ) {
		$this->_arTitles = $arTitles;
	}

	/**
	 * Retorna o formulario de pesquisa.
	 * @param array $arDados
	 */
	public function getFormFilter( $arDados = array(), $obGrid = null ) {
		$form = new DadosEscolaresCursista_FormFilter();
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/dadosescolarescursista/list')->setMethod(
				'post');

		return $form;
	}

}
