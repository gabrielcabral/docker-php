<?php

/**
 * Business do TipoPerfil
 * 
 * @author diego.matos
 * @since 10/04/2012
 */
class Fnde_Sice_Business_TipoPerfil {

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('DS_TIPO_PERFIL' => 'C.DS_TIPO_PERFIL', 'NU_SEQ_TIPO_PERFIL' => 'C.NU_SEQ_TIPO_PERFIL',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_TIPO_PERFIL']) ) {
			$select->where("C.NU_SEQ_TIPO_PERFIL = {$arParams['id']} ");
		} else {
			if ( $arParams['DS_TIPO_PERFIL'] ) {
				$select->where("C.DS_TIPO_PERFIL = ?", $arParams['DS_TIPO_PERFIL']);
			}
			if ( $arParams['NU_SEQ_TIPO_PERFIL'] ) {
				$select->where("C.NU_SEQ_TIPO_PERFIL = ?", $arParams['NU_SEQ_TIPO_PERFIL']);
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

		$obModelo = new Fnde_Sice_Model_TipoPerfil();
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
		$obModelo = new Fnde_Sice_Model_TipoPerfil();
		//$logger = Zend_Registry::get('logger');

		$logger->log('Tor!', Zend_Log::INFO);
		try {
			$where = "NU_SEQ_TIPO_PERFIL = " . $id;
			$obModelo->delete($where);

			$logger->log("TipoPerfil removido com sucesso !", Zend_Log::INFO);

			$this->stMensagem = "TipoPerfil removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$logger->log($e->getMessage(), Zend_Log::WARN);
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona TipoPerfil
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
	 * Obtem TipoPerfil por Id
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function getTipoPerfilById( $id ) {

		try {
			$obModelo = new Fnde_Sice_Model_TipoPerfil();

			$select = $obModelo->select()->where("NU_SEQ_TIPO_PERFIL = ?", $id);

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
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_TIPO_PERFIL');
	}

	/**
	 * Obtém o registro de um tipo de perfil pelo grupo SEGWEB do usuário Logado 
	 */
	public function getTipoPerfilByGrupoSEGWEB( $perfilSegweb ) {
		try {
			$obModelo = new Fnde_Sice_Model_TipoPerfil();

			$select = $obModelo->select()->where("DS_TIPO_PERFIL_SEGWEB = ?", $perfilSegweb);

			$stmt = $select->query();
			$result = $stmt->fetch();

			return $result;
		} catch ( Exception $e ) {
			throw $e;
		}
	}

}
