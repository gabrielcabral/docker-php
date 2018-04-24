<?php

/**
 * Controller do MesoRegiao
 * 
 * @author tiago.ramos
 * @since 03/04/2012
 */

class Manutencao_MesoregiaoController extends Fnde_Sice_Controller_Action {

	/**
	 * Ação de listagem
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function listAction() {
		$this->_helper->layout()->disableLayout();

		$this->setTitle('Configuração');
		$this->setSubtitle('Filtrar');

		//seta novos valores na sessão
		if ( $this->_request->isPost() ) {
			$this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');
			$this->urlFilterNamespace->param = $this->_getAllParams();
		}

		//recupera valores da sessão
		$arFilter = $this->_getAllParams();

		$rsRegistros = array();

		$obBusiness = new Fnde_Sice_Business_MesoRegiao();
		$rsRegistros = $obBusiness->search($arFilter);
		if ( !count($rsRegistros) ) {
			$this->addInstantMessage(Fnde_Message::MSG_INFO,
					'Não foram encontrados registros para os filtros informados!');
		}

		//Chamando componente zend.grid dentro do helper
		if ( $rsRegistros ) {
			$arrHeader = array('Mesorregião', 'Município',);

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
	 * Método acessório get de nameList.
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function getNameList() {
		return $this->_arList;
	}

	/**
	 * Método acessório set de nameList.
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function setNameList( $arList ) {
		$this->_arList = $arList;
	}

	/**
	 * Método acessório get de titles.
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function getTitles() {
		return $this->_arTitles;
	}

	/**
	 * Método acessório set de titles.
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function setTitles( $arTitles ) {
		$this->_arTitles = $arTitles;
	}

	/**
	 * Método acessório get de arTitlesList.
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function getArTitlesList() {
		return array('noMesoRegiao', 'coMunicipioIbge', 'noMunicipio', 'coMesoRegiao', 'noUf', 'coUf',);
	}

	/**
	 * Método que limpa a última pesquisa realizada.
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function clearSearchAction() {

		//limpa sessão
		Zend_Session::namespaceUnset('searchParam');

		//redireciona para pagina de listagem da ultima sessão
		$this->_redirect($this->_getParam('module') . '/' . $this->_getParam('controller') . '/list');
	}

}
