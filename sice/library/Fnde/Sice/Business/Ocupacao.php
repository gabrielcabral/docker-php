<?php

/**
 * Business do Ocupacao
 * 
 * @author diego.matos
 * @since 12/04/2012
 */
class Fnde_Sice_Business_Ocupacao {

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('DS_OCUPACAO' => 'C.DS_OCUPACAO', 'NU_SEQ_OCUPACAO' => 'C.NU_SEQ_OCUPACAO',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author diego.matos
	 * @since 12/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_OCUPACAO']) ) {
			$select->where("C.NU_SEQ_OCUPACAO = {$arParams['id']} ");
		} else {
			if ( $arParams['DS_OCUPACAO'] ) {
				$select->where("C.DS_OCUPACAO = ?", $arParams['DS_OCUPACAO']);
			}
			if ( $arParams['NU_SEQ_OCUPACAO'] ) {
				$select->where("C.NU_SEQ_OCUPACAO = ?", $arParams['NU_SEQ_OCUPACAO']);
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

		$obModelo = new Fnde_Sice_Model_Ocupacao();
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
		$obModelo = new Fnde_Sice_Model_Ocupacao();
		$where = "NU_SEQ_OCUPACAO = " . $id;
		$obModelo->delete($where);

		$this->stMensagem = "Ocupacao removido com sucesso !";
		return $this->stMensagem;
	}

	/**
	 * Seleciona Ocupacao
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
	 * Obtem Ocupacao por Id
	 *
	 * @author diego.matos
	 * @since 12/04/2012
	 */
	public function getOcupacaoById( $id ) {

		$obModelo = new Fnde_Sice_Model_Ocupacao();

		$select = $obModelo->select()->where("NU_SEQ_OCUPACAO = ?", $id);

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
		$select->order('C.DS_OCUPACAO');
	}

}
