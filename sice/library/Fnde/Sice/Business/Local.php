<?php

/**
 * Business do Local
 * 
 * @author diego.matos
 * @since 12/04/2012
 */
class Fnde_Sice_Business_Local {

	protected $_stModelo = 'Local';
	protected $_stSistema = 'sice';

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('NU_SEQ_LOCAL' => 'C.NU_SEQ_LOCAL', 'DS_LOCAL' => 'C.DS_LOCAL',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author diego.matos
	 * @since 12/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_LOCAL']) ) {
			$select->where("C.NU_SEQ_LOCAL = {$arParams['id']} ");
		} else {
			if ( $arParams['NU_SEQ_LOCAL'] ) {
				$select->where("C.NU_SEQ_LOCAL = ?", $arParams['NU_SEQ_LOCAL']);
			}
			if ( $arParams['DS_LOCAL'] ) {
				$select->where("C.DS_LOCAL = ?", $arParams['DS_LOCAL']);
			}
		}
	}

	/**
	 * Recupera select para listagem
	 *
	 * @access public
	 * @return object - Objeto Select
	 *
	 * @author diego.matos
	 * @since 12/04/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_Local();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author diego.matos
	 * @since 12/04/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_Local();

		try {
			$where = "NU_SEQ_LOCAL = " . $id;
			$obModelo->delete($where);

			$this->stMensagem = "Local removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona Local
	 *
	 * @author diego.matos
	 * @since 12/04/2012
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
	 * Obtem Local por Id
	 *
	 * @author diego.matos
	 * @since 12/04/2012
	 */
	public function getLocalById( $id ) {

		$obModelo = new Fnde_Sice_Model_Local();

		$select = $obModelo->select()->where("NU_SEQ_LOCAL = ?", $id);

		$stmt = $select->query();
		$result = $stmt->fetch();

		return $result;
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author diego.matos
	 * @since 12/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.DS_LOCAL');
	}

}
