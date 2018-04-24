<?php

/**
 * Business do CriterioAvaliacao
 * 
 * @author diego.matos
 * @since 10/04/2012
 */
class Fnde_Sice_Business_CriterioAvaliacao {

	protected $_stModelo = 'CriterioAvaliacao';
	protected $_stSistema = 'sice';

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('DS_SITUACAO' => 'C.DS_SITUACAO', 'DS_CRITERIO_AVALIACAO' => 'C.DS_CRITERIO_AVALIACAO',
				'NU_SEQ_CRITERIO_AVAL' => 'C.NU_SEQ_CRITERIO_AVAL',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_CRITERIO_AVAL']) ) {
			$select->where("C.NU_SEQ_CRITERIO_AVAL = {$arParams['id']} ");
		} else {
			if ( $arParams['DS_SITUACAO'] ) {
				$select->where("C.DS_SITUACAO = ?", $arParams['DS_SITUACAO']);
			}
			if ( $arParams['DS_CRITERIO_AVALIACAO'] ) {
				$select->where("C.DS_CRITERIO_AVALIACAO = ?", $arParams['DS_CRITERIO_AVALIACAO']);
			}
			if ( $arParams['NU_SEQ_CRITERIO_AVAL'] ) {
				$select->where("C.NU_SEQ_CRITERIO_AVAL = ?", $arParams['NU_SEQ_CRITERIO_AVAL']);
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
	 * @since 10/04/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_CriterioAvaliacao();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_CriterioAvaliacao();
		//$logger = Zend_Registry::get('logger');

		$logger->log('Tor!', Zend_Log::INFO);
		try {
			$where = "NU_SEQ_AVAL_PEDAGOGICA = " . $id;
			$obModelo->delete($where);

			$logger->log("CriterioAvaliacao removido com sucesso !", Zend_Log::INFO);

			$this->stMensagem = "CriterioAvaliacao removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$logger->log($e->getMessage(), Zend_Log::WARN);
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona CriterioAvaliacao
	 *
	 * @author diego.matos
	 * @since 10/04/2012
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
	 * Obtem CriterioAvaliacao por Id
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function getCriterioAvaliacaoById( $id ) {

		try {
			$obModelo = new Fnde_Sice_Model_CriterioAvaliacao();
			//$arInfoModelo = $obModelo->info();

			$select = $obModelo->select()->where("NU_SEQ_CRITERIO_AVAL = ?", $id);

			$stmt = $select->query();
			$result = $stmt->fetch();

			return $result;
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Obtem CriterioAvaliacao por Id
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function getCriterioAvaliacaoByIdConfiguracao( $id ) {
		$obModelo = new Fnde_Sice_Model_CriterioAvaliacao();

		$select = $obModelo->select()->where("NU_SEQ_CONFIGURACAO = $id")->order("NU_SEQ_CRITERIO_AVAL");

		$stm = $select->query();
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_CRITERIO_AVAL');
	}

}
