<?php

/**
 * Business do TipoCurso
 * 
 * @author diego.matos
 * @since 30/03/2012
 */
class Fnde_Sice_Business_TipoCurso {

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('NU_SEQ_TIPO_CURSO' => 'C.NU_SEQ_TIPO_CURSO', 'DS_TIPO_CURSO' => 'C.DS_TIPO_CURSO',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_TIPO_CURSO']) ) {
			$select->where("C.NU_SEQ_TIPO_CURSO = {$arParams['id']} ");
		} else {
			if ( $arParams['NU_SEQ_TIPO_CURSO'] ) {
				$select->where("C.NU_SEQ_TIPO_CURSO = ?", $arParams['NU_SEQ_TIPO_CURSO']);
			}
			if ( $arParams['DS_TIPO_CURSO'] ) {
				$select->where("C.DS_TIPO_CURSO = ?", $arParams['DS_TIPO_CURSO']);
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
	 * @since 30/03/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_TipoCurso();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_TipoCurso();
		try {
			$where = "NU_SEQ_TIPO_CURSO = " . $id;
			$obModelo->delete($where);

			$this->stMensagem = "TipoCurso removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona TipoCurso
	 *
	 * @author diego.matos
	 * @since 30/03/2012
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
	 * Obtem TipoCurso por Id
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function getTipoCursoById( $id ) {
		$obModelo = new Fnde_Sice_Model_TipoCurso();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");

		$aDados = $obModelo->find($id)->current();
		if ( $aDados ) {
			return $boArray ? $aDados->toArray() : $aDados;
		}
		return $boArray ? $obModelo->createRow()->toArray() : $obModelo->createRow();
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_TIPO_CURSO');
	}

}
