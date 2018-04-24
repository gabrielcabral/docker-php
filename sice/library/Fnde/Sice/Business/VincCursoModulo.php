<?php

/**
 * Business do VincCursoModulo
 * 
 * @author vinicius.cancado
 * @since 18/04/2012
 */
class Fnde_Sice_Business_VincCursoModulo {

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('NU_SEQ_MODULO' => 'C.NU_SEQ_MODULO', 'NU_SEQ_CURSO' => 'C.NU_SEQ_CURSO',
				'NU_SEQ_VINC_CURSO_MODULO' => 'C.NU_SEQ_VINC_CURSO_MODULO',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_VINC_CURSO_MODULO']) ) {
			$select->where("C.NU_SEQ_VINC_CURSO_MODULO = {$arParams['id']} ");
		} else {
			if ( $arParams['NU_SEQ_MODULO'] ) {
				$select->where("C.NU_SEQ_MODULO = ?", $arParams['NU_SEQ_MODULO']);
			}
			if ( $arParams['NU_SEQ_CURSO'] ) {
				$select->where("C.NU_SEQ_CURSO = ?", $arParams['NU_SEQ_CURSO']);
			}
			if ( $arParams['NU_SEQ_VINC_CURSO_MODULO'] ) {
				$select->where("C.NU_SEQ_VINC_CURSO_MODULO = ?", $arParams['NU_SEQ_VINC_CURSO_MODULO']);
			}
		}
	}

	/**
	 * Recupera select para listagem
	 *
	 * @access public
	 * @return object - Objeto Select
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_VincCursoModulo();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_VincCursoModulo();
		$logger = Zend_Registry::get('logger');

		$logger->log('Tor!', Zend_Log::INFO);
		try {
			$where = "NU_SEQ_VINC_CURSO_MODULO = " . $id;
			$obModelo->delete($where);

			$logger->log("VincCursoModulo removido com sucesso !", Zend_Log::INFO);

			$this->stMensagem = "VincCursoModulo removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$logger->log($e->getMessage(), Zend_Log::WARN);
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona VincCursoModulo
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
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
	 * Obtem VincCursoModulo por Id
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function getVincCursoModuloById( $id ) {

		$logger = Zend_Registry::get('logger');
		try {
			$obModelo = new Fnde_Sice_Model_VincCursoModulo();

			$select = $obModelo->select()->where("NU_SEQ_VINC_CURSO_MODULO = ?", $id);

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
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_VINC_CURSO_MODULO');
	}

	/**
	 * Retorna o vinculo de curso e modulo de um determinado curso.
	 * @param string $id ID do curso.
	 */
	public function buscarVinculoPorCurso( $id ) {

		$select = $this->getSelect(array('NU_SEQ_CURSO', 'NU_SEQ_MODULO'));
		$select->where('NU_SEQ_CURSO = ?', $id);

		$stm = $select->query();
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Exclui vinculo de curso e modulo passando um determinado curso.
	 * @param string $nuSeqCurso ID do curso.
	 */
	public function excluirVinculoCursoModulo( $nuSeqCurso ) {
		$arrayParams = array("NU_SEQ_CURSO" => $nuSeqCurso);
		$obModelo = new Fnde_Sice_Model_VincCursoModulo();
		$where = "NU_SEQ_CURSO = " . $arrayParams['NU_SEQ_CURSO'];
		$obModelo->delete($where);
	}

}
