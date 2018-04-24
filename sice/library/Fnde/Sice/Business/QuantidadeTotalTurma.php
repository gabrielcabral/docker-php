<?php

/**
 * Business do QuantidadeTurma
 * 
 * @author pedro.correia
 * @since 19/04/2016
 */
class Fnde_Sice_Business_QuantidadeTotalTurma {

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('NU_SEQ_CONFIGURACAO' => 'C.NU_SEQ_CONFIGURACAO','NU_SEQ_QUANTIDADE_TOTAL_TURMA' => 'C.NU_SEQ_QUANTIDADE_TOTAL_TURMA',
			'QT_TOTAL_TURMAS' => 'C.QT_TOTAL_TURMAS','SG_UF' => 'C.SG_UF',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author pedro.correia
	 * @since 19/04/2016
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_QUANTIDADE_TOTAL_TURMA']) ) {
			$select->where("C.NU_SEQ_QUANTIDADE_TOTAL_TURMA = {$arParams['id']} ");
		} else {
			if ( $arParams['NU_SEQ_CONFIGURACAO'] ) {
				$select->where("C.NU_SEQ_CONFIGURACAO = ?", $arParams['NU_SEQ_CONFIGURACAO']);
			}
			if ( $arParams['NU_SEQ_QUANTIDADE_TOTAL_TURMA'] ) {
				$select->where("C.NU_SEQ_QUANTIDADE_TOTAL_TURMA = ?", $arParams['NU_SEQ_QUANTIDADE_TOTAL_TURMA']);
			}
			if ( $arParams['QT_TOTAL_TURMAS'] ) {
				$select->where("C.QT_TOTAL_TURMAS = ?", $arParams['QT_TOTAL_TURMAS']);
			}
			if ( $arParams['SG_UF'] ) {
				$select->where("C.SG_UF = ?", $arParams['SG_UF']);
			}
		}
	}

	/**
	 * Recupera select para listagem
	 *
	 * @access public
	 * @return object - Objeto Select
	 *
	 * @author pedro.correia
	 * @since 19/04/2016
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_QuantidadeTotalTurmas();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author pedro.correia
	 * @since 19/04/2016
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_QuantidadeTotalTurmas();

		$logger->log('Tor!', Zend_Log::INFO);
		try {
			$where = "NU_SEQ_QUANTIDADE_TOTAL_TURMA = " . $id;
			$obModelo->delete($where);

			$logger->log("QuantidadeTurma removido com sucesso !", Zend_Log::INFO);

			$this->stMensagem = "QuantidadeTurma removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$logger->log($e->getMessage(), Zend_Log::WARN);
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona QuantidadeTotalTurma
	 *
	 * @author pedro.correia
	 * @since 19/04/2016
	 */
	public function searchQtTotalTurma( $arParams ) {
		$select = $this->getSelect($this->getColumnsSearch());
		$this->setFilter($select, $arParams);
		$this->setOrder($select);
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $result;
	}

	/**
	 * Obtem QuantidadeTurma por arquivo de configuração e UF
	 *
	 * @author pedro.correia
	 * @since 19/04/2016
	 */
	public function getQuantidadeTurmaByConfigUf( $config, $uf ) {

		$obModelo = new Fnde_Sice_Model_QuantidadeTotalTurmas();

		$select = $obModelo->select()->where(" NU_SEQ_CONFIGURACAO = ?", $config)->where(" SG_UF = ?",
				$uf);

		$stmt = $select->query();
		$result = $stmt->fetch();

		return $result;

	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author pedro.correia
	 * @since 19/04/2016
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_QUANTIDADE_TOTAL_TURMA');
	}

//	/**
//	 * Função para pesquisar mesorregiões.
//	 */
//	public function searchMesoRegiao() {
//
//		$select = $this->getSelect($this->getColumnsSearch());
//		$this->setFilter($select);
//		$this->setOrder($select);
//		$stmt = $select->query();
//		$result = $stmt->fetchAll();
//
//		return $result;
//	}
//
//	/**
//	 * Função para obter turmas por mesorregião.
//	 *
//	 * @param string $sgRegiao
//	 */
//	public function obterTurmasPorRegiao( $sgRegiao , $seqConfig = null) {
//		$obModelo = new Fnde_Sice_Model_QuantidadeTurmas();
//		//$arInfoModelo = $obModelo->info();
//
//		$arColumns = array('CO_MESO_REGIAO' => 'MR.CO_MESO_REGIAO', 'NO_MESO_REGIAO' => 'MR.NO_MESO_REGIAO',
//				'TOTAL_MUNICIPIOS' => 'COUNT(MR.CO_MUNICIPIO_IBGE)');
//
//		$select = $obModelo->select()->setIntegrityCheck(false)->from(array('MR' => 'CTE_FNDE.T_MESO_REGIAO'),
//				$arColumns);
//
//		$select->joinInner("CORP_FNDE.S_UF", "MR.CO_UF = S_UF.CO_UF_IBGE",
//				array('SG_UF' => 'S_UF.SG_UF', 'NO_UF' => 'S_UF.NO_UF',));
//
//		$select->joinInner("CORP_FNDE.S_REGIAO", "S_UF.SG_REGIAO = S_REGIAO.SG_REGIAO",
//				array('SG_REGIAO' => 'S_REGIAO.SG_REGIAO',));
//
//		$select->where('S_REGIAO.SG_REGIAO = ? ', $sgRegiao);
//
//		$select->group(
//				array('S_REGIAO.SG_REGIAO', 'S_UF.SG_UF', 'S_UF.NO_UF', 'MR.CO_MESO_REGIAO', 'MR.NO_MESO_REGIAO'));
//
//		$select->order(array('S_UF.NO_UF', 'MR.NO_MESO_REGIAO'));
//
//		$stmt = $select->query();
//		$result = $stmt->fetchAll();
//		$retorno = array();
//		$uf = '';
//		$i = 0;
//		foreach ( $result as $record ) {
//
//			if ( $record['SG_UF'] != $uf ) {
//				$uf = $record['SG_UF'];
//				$i = 0;
//				$retorno[$uf] = array();
//			}
//
//			//Pega a quantidade de turma cadastradas por mesoregiao
//			if(!is_null($seqConfig)){
//				$query = "SELECT count(NU_SEQ_TURMA) QTD, UF_TURMA, CO_MESORREGIAO
//								FROM SICE_FNDE.S_TURMA
//								WHERE NU_SEQ_CONFIGURACAO = ". $seqConfig ."
//								AND CO_MESORREGIAO = ". $record["CO_MESO_REGIAO"] ."
//								group by CO_MESORREGIAO,UF_TURMA
//								order by UF_TURMA";
//
//
//				$obModelo = new Fnde_Sice_Model_Turma();
//				$stm = $obModelo->getAdapter()->query($query);
//				$result = $stm->fetch();
//
//				$record['TURMAS_CADSATRADAS'] = ($result) ? $result["QTD"] : 0;
//			}
//
//			$retorno[$uf][$i++] = $record;
//		}
//
//		return $retorno;
//	}
//
//	/**
//	 * Recupera a quantidade de turma disponível na configuração por turma
//	 * E a quantidade de turma solicitado aprovação por mesorregiao
//	 *
//	 * @param int $codTurma
//	 * @param int $codMesoRegiao
//	 */
//	public function getQuantidadeTurmas( $codTurma ) {
//
//		$query = " SELECT QTD.QT_TURMAS AS QTD_CONFIGURACAO, ";
//		$query .= " (SELECT COUNT(*) FROM SICE_FNDE.S_TURMA T ";
//		$query .= " 	WHERE T.CO_MESORREGIAO = TUR.CO_MESORREGIAO ";
//		$query .= " 	AND (T.ST_TURMA = 4) ";
//		$query .= " ) AS QTD_SOLICITADO ";
//		$query .= " FROM SICE_FNDE.S_TURMA TUR, SICE_FNDE.S_CURSO CUR, SICE_FNDE.S_TIPO_CURSO TIP, ";
//		$query .= " SICE_FNDE.S_CONFIGURACAO CONF, SICE_FNDE.S_QUANTIDADE_TURMAS QTD ";
//		$query .= " WHERE TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO ";
//		$query .= " AND CUR.NU_SEQ_TIPO_CURSO = TIP.NU_SEQ_TIPO_CURSO ";
//		$query .= " AND CONF.NU_SEQ_TIPO_CURSO = TIP.NU_SEQ_TIPO_CURSO ";
//		$query .= " AND QTD.NU_SEQ_CONFIGURACAO = CONF.NU_SEQ_CONFIGURACAO ";
//		$query .= " AND TUR.CO_MESORREGIAO = QTD.CO_MESORREGIAO ";
//		$query .= " AND TUR.NU_SEQ_TURMA = $codTurma ";
//		$query .= " AND CONF.NU_SEQ_CONFIGURACAO = TUR.NU_SEQ_CONFIGURACAO ";
//
//		$obModelo = new Fnde_Sice_Model_QuantidadeTurmas();
//		$stm = $obModelo->getAdapter()->query($query);
//		$result = $stm->fetchAll();
//		return $result[0];
//	}
//
//	/**
//	 * Recupera a quantidade de turma por id da configuração
//	 *
//	 * @param int $idConfig
//	 */
//	public function getQtdTurmasPorConfig( $idConfig ) {
//
//		$obModelo = new Fnde_Sice_Model_QuantidadeTurmas();
//
//		$select = $obModelo->select()->where("NU_SEQ_CONFIGURACAO = ?", $idConfig);
//
//		$stmt = $select->query();
//		$result = $stmt->fetchAll();
//
//		return $result;
//	}

}
