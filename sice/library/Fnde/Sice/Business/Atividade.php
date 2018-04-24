<?php

/**
 * Business do Atividade
 * 
 * @author diego.matos
 * @since 12/04/2012
 */
class Fnde_Sice_Business_Atividade extends Fnde_Base_Business {

	protected $_stModelo = 'Atividade';
	protected $_stSistema = 'sice';

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('NU_SEQ_ATIVIDADE' => 'C.NU_SEQ_ATIVIDADE', 'DS_ATIVIDADE' => 'C.DS_ATIVIDADE',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author diego.matos
	 * @since 12/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_ATIVIDADE']) ) {
			$select->where("C.NU_SEQ_ATIVIDADE = {$arParams['id']} ");
		} else {
			if ( $arParams['NU_SEQ_ATIVIDADE'] ) {
				$select->where("C.NU_SEQ_ATIVIDADE = ?", $arParams['NU_SEQ_ATIVIDADE']);
			}
			if ( $arParams['DS_ATIVIDADE'] ) {
				$select->where("C.DS_ATIVIDADE = ?", $arParams['DS_ATIVIDADE']);
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

		$obModelo = new Fnde_Sice_Model_Atividade();
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
		$obModelo = new Fnde_Sice_Model_Atividade();
		$logger = Zend_Registry::get('logger');

		$logger->log('Tor!', Zend_Log::INFO);
		try {
			$where = "NU_SEQ_ATIVIDADE = " . $id;
			$obModelo->delete($where);

			$logger->log("Atividade removido com sucesso !", Zend_Log::INFO);

			$this->stMensagem = "Atividade removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$logger->log($e->getMessage(), Zend_Log::WARN);
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona Atividade
	 *
	 * @author diego.matos
	 * @since 12/04/2012
	 */
	public function search( $arParams ) {

		$logger = Zend_Registry::get('logger');

		try {
			$select = $this->getSelect($this->getColumnsSearch());
			$this->setFilter($select, $arParams);
			$this->setOrder($select);
			$stmt = $select->query();
			$result = $stmt->fetchAll();
		} catch ( Exception $e ) {

			$logger->log($e->getMessage(), Zend_Log::WARN);
		}

		return $result;
	}

	/**
	 * Obtem Atividade por Id
	 *
	 * @author diego.matos
	 * @since 12/04/2012
	 */
	public function getAtividadeById( $id ) {

		$logger = Zend_Registry::get('logger');
		try {
			$obModelo = new Fnde_Sice_Model_Atividade();

			$select = $obModelo->select()->where("NU_SEQ_ATIVIDADE = ?", $id);

			$stmt = $select->query();
			$result = $stmt->fetch();

			return $result;
		} catch ( Exception $e ) {
			$logger->log($e->getMessage(), Zend_Log::WARN);
		}
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author diego.matos
	 * @since 12/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_ATIVIDADE');
	}

}
