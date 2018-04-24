<?php

/**
 * Business do DadosEscolaresCursista
 * 
 * @author diego.matos
 * @since 25/04/2012
 */
class Fnde_Sice_Business_DadosEscolaresCursista {

	protected $_stModelo = 'DadosEscolaresCursista';
	protected $_stSistema = 'sice';

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('NU_SEQ_USUARIO_CURSISTA' => 'C.NU_SEQ_USUARIO_CURSISTA', 'CO_MESORREGIAO' => 'C.CO_MESORREGIAO',
				'CO_SEGMENTO' => 'C.CO_SEGMENTO', 'CO_REDE_ENSINO' => 'C.CO_REDE_ENSINO',
				'CO_MUNICIPIO_ESCOLA' => 'C.CO_MUNICIPIO_ESCOLA', 'CO_ESCOLA' => 'C.CO_ESCOLA',
				'CO_UF_ESCOLA' => 'C.CO_UF_ESCOLA',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_USUARIO_CURSISTA']) ) {
			$select->where("C.NU_SEQ_USUARIO_CURSISTA = {$arParams['id']} ");
		} else {
			if ( $arParams['NU_SEQ_USUARIO_CURSISTA'] ) {
				$select->where("C.NU_SEQ_USUARIO_CURSISTA = ?", $arParams['NU_SEQ_USUARIO_CURSISTA']);
			}
			if ( $arParams['CO_MESORREGIAO'] ) {
				$select->where("C.CO_MESORREGIAO = ?", $arParams['CO_MESORREGIAO']);
			}
			if ( $arParams['CO_SEGMENTO'] ) {
				$select->where("C.CO_SEGMENTO = ?", $arParams['CO_SEGMENTO']);
			}
			if ( $arParams['CO_REDE_ENSINO'] ) {
				$select->where("C.CO_REDE_ENSINO = ?", $arParams['CO_REDE_ENSINO']);
			}
			if ( $arParams['CO_MUNICIPIO_ESCOLA'] ) {
				$select->where("C.CO_MUNICIPIO_ESCOLA = ?", $arParams['CO_MUNICIPIO_ESCOLA']);
			}
			if ( $arParams['CO_ESCOLA'] ) {
				$select->where("C.CO_ESCOLA = ?", $arParams['CO_ESCOLA']);
			}
			if ( $arParams['CO_UF_ESCOLA'] ) {
				$select->where("C.CO_UF_ESCOLA = ?", $arParams['CO_UF_ESCOLA']);
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
	 * @since 25/04/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_DadosEscolaresCursista();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_DadosEscolaresCursista();
		$logger = Zend_Registry::get('logger');

		$logger->log('Tor!', Zend_Log::INFO);
		try {
			$where = "NU_SEQ_USUARIO_CURSISTA = " . $id;
			$obModelo->delete($where);

			$logger->log("DadosEscolaresCursista removido com sucesso !", Zend_Log::INFO);

			$this->stMensagem = "DadosEscolaresCursista removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$logger->log($e->getMessage(), Zend_Log::WARN);
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona DadosEscolaresCursista
	 *
	 * @author diego.matos
	 * @since 25/04/2012
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
	 * Obtem DadosEscolaresCursista por Id
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function getDadosEscolaresCursistaById( $id ) {

		$obModelo = new Fnde_Sice_Model_DadosEscolaresCursista();

		$select = $obModelo->select()->where("NU_SEQ_USUARIO_CURSISTA = ?", $id);

		$stmt = $select->query();
		$result = $stmt->fetch();

		return $result;
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_USUARIO_CURSISTA');
	}

	/**
	 * Busca dados da escoal pelo id do cursista
	 *
	 * @autor pedro.correia
	 * @sice 04/05/2016
	 */

	public function getDadosEscolaresById($id){
		$query = "SELECT *
					FROM SICE_FNDE.S_DADOS_ESCOLARES_CURSISTA de
					WHERE de.NU_SEQ_USUARIO_CURSISTA = {$id}";

		$obModelo = new Fnde_Sice_Model_DadosEscolaresCursista();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();

		return $result;
	}

}
