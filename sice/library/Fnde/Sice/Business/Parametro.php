<?php

/**
 * Business do Parametro
 * 
 * @author diego.matos
 * @since 23/04/2012
 */
class Fnde_Sice_Business_Parametro {

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('SIGLA_PARAMETRO' => 'C.SIGLA_PARAMETRO', 'DS_PARAMETRO' => 'C.DS_PARAMETRO',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author diego.matos
	 * @since 23/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['SIGLA_PARAMETRO']) ) {
			$select->where("C.SIGLA_PARAMETRO = {$arParams['id']} ");
		} else {
			if ( $arParams['SIGLA_PARAMETRO'] ) {
				$select->where("C.SIGLA_PARAMETRO = ?", $arParams['SIGLA_PARAMETRO']);
			}
			if ( $arParams['DS_PARAMETRO'] ) {
				$select->where("C.DS_PARAMETRO = ?", $arParams['DS_PARAMETRO']);
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
	 * @since 23/04/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_Parametro();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author diego.matos
	 * @since 23/04/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_Parametro();

		try {
			$where = "SIGLA_PARAMETRO = " . $id;
			$obModelo->delete($where);

			$this->stMensagem = "Parametro removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona Parametro
	 *
	 * @author diego.matos
	 * @since 23/04/2012
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
	 * Obtem Parametro por Id
	 *
	 * @author diego.matos
	 * @since 23/04/2012
	 */
	public function getParametroById( $id ) {
		$obModelo = new Fnde_Sice_Model_Parametro();

		$select = $obModelo->select()->where("SG_PARAMETRO = ?", $id);

		$stmt = $select->query();
		$result = $stmt->fetch();

		return $result;
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author diego.matos
	 * @since 23/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.SIGLA_PARAMETRO');
	}

}
