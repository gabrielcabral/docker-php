<?php

/**
 * Controller do MesoRegiao
 * 
 * @author tiago.ramos
 * @since 03/04/2012
 */

class Manutencao_MesoregiaoController extends Fnde_Sice_Controller_Action {

	/**
	 * A��o de listagem
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function listAction() {
		$this->_helper->layout()->disableLayout();

		$this->setTitle('Configura��o');
		$this->setSubtitle('Filtrar');

		//seta novos valores na sess�o
		if ( $this->_request->isPost() ) {
			$this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');
			$this->urlFilterNamespace->param = $this->_getAllParams();
		}

		//recupera valores da sess�o
		$arFilter = $this->_getAllParams();

		$rsRegistros = array();

		$obBusiness = new Fnde_Sice_Business_MesoRegiao();
		$rsRegistros = $obBusiness->search($arFilter);
		if ( !count($rsRegistros) ) {
			$this->addInstantMessage(Fnde_Message::MSG_INFO,
					'N�o foram encontrados registros para os filtros informados!');
		}

		//Chamando componente zend.grid dentro do helper
		if ( $rsRegistros ) {
			$arrHeader = array('Mesorregi�o', 'Munic�pio',);

			$grid = new Fnde_View_Helper_DataTables();
			$grid->setAutoCallJs(true);
			$this->view->grid = $grid->setData($rsRegistros)
				->setHeader($arrHeader)
                ->setTitle($rsRegistros[0]['NO_UF'])
                ->setId('CO_MESO_REGIAO')
                ->setColumnsHidden(array('CO_MESO_REGIAO', 'NO_UF', 'CO_UF', 'CO_MUNICIPIO_IBGE'))
                ->setTableAttribs(array('id' => 'edit'));
		}
	}

	/**
	 * M�todo acess�rio get de nameList.
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function getNameList() {
		return $this->_arList;
	}

	/**
	 * M�todo acess�rio set de nameList.
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function setNameList( $arList ) {
		$this->_arList = $arList;
	}

	/**
	 * M�todo acess�rio get de titles.
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function getTitles() {
		return $this->_arTitles;
	}

	/**
	 * M�todo acess�rio set de titles.
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function setTitles( $arTitles ) {
		$this->_arTitles = $arTitles;
	}

	/**
	 * M�todo acess�rio get de arTitlesList.
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function getArTitlesList() {
		return array('noMesoRegiao', 'coMunicipioIbge', 'noMunicipio', 'coMesoRegiao', 'noUf', 'coUf',);
	}

	/**
	 * M�todo que limpa a �ltima pesquisa realizada.
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function clearSearchAction() {

		//limpa sess�o
		Zend_Session::namespaceUnset('searchParam');

		//redireciona para pagina de listagem da ultima sess�o
		$this->_redirect($this->_getParam('module') . '/' . $this->_getParam('controller') . '/list');
	}

}
