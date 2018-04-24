<?php

/**
 * Business do Regiao
 *
 * @author tiago.ramos
 * @since 03/04/2012
 */
class Fnde_Sice_Business_Regiao {

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('SG_REGIAO' => 'C.SG_REGIAO', 'NO_REGIAO' => 'C.NO_REGIAO',
				'CO_REGIAO_SIAFI' => 'C.CO_REGIAO_SIAFI', 'CO_REGIAO_IBGE' => 'C.CO_REGIAO_IBGE',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['SG_REGIAO']) ) {
			$select->where("C.SG_REGIAO = {$arParams['id']} ");
		} else {
			if ( $arParams['SG_REGIAO'] ) {
				$select->where("C.SG_REGIAO = ?", $arParams['SG_REGIAO']);
			}
			if ( $arParams['NO_REGIAO'] ) {
				$select->where("C.NO_REGIAO = ?", $arParams['NO_REGIAO']);
			}
			if ( $arParams['CO_REGIAO_SIAFI'] ) {
				$select->where("C.CO_REGIAO_SIAFI = ?", $arParams['CO_REGIAO_SIAFI']);
			}
			if ( $arParams['CO_REGIAO_IBGE'] ) {
				$select->where("C.CO_REGIAO_IBGE = ?", $arParams['CO_REGIAO_IBGE']);
			}
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

		$obModelo = new Fnde_Sice_Model_Regiao();
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
		$obModelo = new Fnde_Sice_Model_Regiao();
		//$logger = Zend_Registry::get('logger');

		$logger->log('Tor!', Zend_Log::INFO);
		try {
			$where = "SG_REGIAO = " . $id;
			$obModelo->delete($where);

			$logger->log("Regiao removido com sucesso !", Zend_Log::INFO);

			$this->stMensagem = "Regiao removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$logger->log($e->getMessage(), Zend_Log::WARN);
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona Regiao
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
	 * Obtem Regiao por Id
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function getRegiaoById( $id ) {

		try {
			$obModelo = new Fnde_Sice_Model_Regiao();
			//$arInfoModelo = $obModelo->info();

			$select = $obModelo->select()->where("SG_REGIAO = ?", $id);

			$stmt = $select->query();
			$result = $stmt->fetch();

			return $result;
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NO_REGIAO');
	}

	/**
	 * Obtém a região baseada na UF
	 *
	 * @author diego.matos
	 * @since 25/06/2012
	 * @param array $uf
	 */
	public function obterRegiaoPorUF( $arParam ) {
		$query = "SELECT ";
		$query .= " SUF.SG_REGIAO, ";
		$query .= " SRG.NO_REGIAO ";
		$query .= " FROM CORP_FNDE.S_UF SUF ";
		$query .= " INNER JOIN CORP_FNDE.S_REGIAO SRG ";
		$query .= " ON SUF.SG_REGIAO = SRG.SG_REGIAO ";
		$query .= " WHERE SUF.SG_UF = :SG_UF ";

		$obModelo = new Fnde_Sice_Model_Regiao();
		$stm = $obModelo->getAdapter()->query($query, $arParam);
		$result = $stm->fetchAll();
		return $result[0];
	}
}
