<?php

/**
 * Business do Uf
 * 
 * @author tiago.ramos
 * @since 03/04/2012
 */
class Fnde_Sice_Business_Uf {

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('CO_UF_SIAFI_BB' => 'C.CO_UF_SIAFI_BB', 'SG_UF' => 'C.SG_UF', 'NO_UF' => 'C.NO_UF',
				'SG_REGIAO' => 'C.SG_REGIAO', 'CO_UF_SIAFI' => 'C.CO_UF_SIAFI', 'DS_TRATAMENTO' => 'C.DS_TRATAMENTO',
				'CO_UF_INSS' => 'C.CO_UF_INSS', 'CO_UF_IBGE' => 'C.CO_UF_IBGE',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['SG_UF']) && isset($arParams['id']) ) {
			$select->where("C.SG_UF = {$arParams['id']} ");
		} else {
			$this->setMonFiltroUf($select, $arParams, 'CO_UF_SIAFI_BB');
			$this->setMonFiltroUf($select, $arParams, 'SG_UF');
			$this->setMonFiltroUf($select, $arParams, 'NO_UF');
			$this->setMonFiltroUf($select, $arParams, 'SG_REGIAO');
			$this->setMonFiltroUf($select, $arParams, 'CO_UF_SIAFI');
			$this->setMonFiltroUf($select, $arParams, 'DS_TRATAMENTO');
			$this->setMonFiltroUf($select, $arParams, 'CO_UF_INSS');
			$this->setMonFiltroUf($select, $arParams, 'CO_UF_IBGE');
		}
	}

	/**
	 * Método que auxilia a montagem dos filtros
	 * @param $select
	 * @param array $arParams
	 * @param string $descricao
	 */
	private function setMonFiltroUf( $select, $arParams, $descricao ) {
		if ( $arParams[$descricao] ) {
			$select->where("C." . $descricao . " = ?", $arParams[$descricao]);
		}
	}

	/**
	 * Recupera select para listagem
	 *
	 * @access public
	 * @return object - Objeto Select
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_Uf();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_Uf();
		$logger = Zend_Registry::get('logger');

		$where = "SG_UF = " . $id;
		$obModelo->delete($where);

		$logger->log("Uf removido com sucesso !", Zend_Log::INFO);

		$this->stMensagem = "Uf removido com sucesso !";
		return $this->stMensagem;
	}

	/**
	 * Seleciona Uf
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function search( $arParams ) {
		$select = $this->getSelect($this->getColumnsSearch());
		$this->setFilter($select, $arParams);
		$this->setOrder($select);
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $result;
	}

	/**
	 * Obtem Uf por Id
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function getUfById( $id ) {

		$obModelo = new Fnde_Sice_Model_Uf();

		$select = $obModelo->select()->where("SG_UF = ?", $id);

		$stmt = $select->query();
		$result = $stmt->fetch();

		return $result;
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.SG_UF');
	}

	/**
	 * Obtem municipios pela UF.
	 * 
	 * @author rafael.paiva
	 * @since 11/04/2012
	 */
	public function getMunicipioPorUf( $siglaUF ) {

		$query = " SELECT ";
		$query .= " MESOREGIAO.CO_MUNICIPIO_IBGE, ";
		$query .= " MESOREGIAO.NO_MUNICIPIO ";
		$query .= " FROM ";
		$query .= " CTE_FNDE.T_MESO_REGIAO MESOREGIAO, ";
		$query .= " CORP_FNDE.S_UF UF ";
		$query .= " WHERE ";
		$query .= " UF.CO_UF_IBGE = MESOREGIAO.CO_UF AND ";
		$query .= " UF.SG_UF = :SG_UF";

		$arrayParametros["SG_UF"] = $siglaUF;

		$query .= " ORDER BY MESOREGIAO.NO_MUNICIPIO";

		$obModelo = new Fnde_Sice_Model_Uf();
		$stm = $obModelo->getAdapter()->query($query, $arrayParametros);
		$result = $stm->fetchAll();
		return $result;

	}

	/**
	 * Obtem municipios da tabela CORP_FNDE.S_MUNICIPIO pela UF.
	 *
	 * @author diego.matos
	 * @since 18/05/2012
	 */
	public function getMunicipioCorpPorUf( $siglaUF ) {

		$arrayParametros = array();

		$query = " SELECT ";
		$query .= " MUNICIPIO.CO_MUNICIPIO_FNDE CO_MUNICIPIO_FNDE, ";
		$query .= " MUNICIPIO.NO_MUNICIPIO NO_MUNICIPIO ";
		$query .= " FROM ";
		$query .= " CORP_FNDE.S_MUNICIPIO MUNICIPIO, ";
		$query .= " CORP_FNDE.S_UF UF ";
		$query .= " WHERE ";
		$query .= " MUNICIPIO.SG_UF = UF.SG_UF ";
		$query .= " AND UF.SG_UF = :SG_UF";

		$arrayParametros["SG_UF"] = $siglaUF;

		$query .= " ORDER BY MUNICIPIO.NO_MUNICIPIO";

		$obModelo = new Fnde_Sice_Model_Uf();
		$stm = $obModelo->getAdapter()->query($query, $arrayParametros);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Obtém as UF's pela Região
	 * @author diego.matos
	 * @since 03/07/2012
	 * @param string $siglaRegiao
	 */
	public function getUfByRegiao( $siglaRegiao ) {
		$obModelo = new Fnde_Sice_Model_Uf();
		$select = $obModelo->select()->where("SG_REGIAO = ?", $siglaRegiao);
		$select->order("SG_UF");
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $result;
	}
	
	/**
	 * Obtém a Região pela UF
	 * @author poliane.silva
	 * @since 13/12/2012
	 * @param string $siglauF
	 */
	public function getRegiaoByUf( $siglauF ) {
		$obModelo = new Fnde_Sice_Model_Uf();
		$select = $obModelo->select()->where("SG_UF = ?", $siglauF);
		$stmt = $select->query();
		$result = $stmt->fetch();
		return $result;
	}
}
