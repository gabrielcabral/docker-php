<?php

/**
 * Business do PeriodoVinculacao
 *
 * @author vinicius.cancado
 * @since 18/04/2012
 */
class Fnde_Sice_Business_PeriodoVinculacao {

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('DT_INCLUSAO' => 'C.DT_INCLUSAO', 'NU_SEQ_PERIODO_VINCULACAO' => 'C.NU_SEQ_PERIODO_VINCULACAO',
				'DT_FINAL' => 'C.DT_FINAL', 'VL_EXERCICIO' => 'C.VL_EXERCICIO', 'DT_INICIAL' => 'C.DT_INICIAL',
				'NU_SEQ_TIPO_PERFIL' => 'C.NU_SEQ_TIPO_PERFIL',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_PERIODO_VINCULACAO']) ) {
			$select->where("C.NU_SEQ_PERIODO_VINCULACAO = {$arParams['id']} ");
		} else {
			if ( $arParams['DT_INCLUSAO'] ) {
				$select->where("C.DT_INCLUSAO = ?", $arParams['DT_INCLUSAO']);
			}
			if ( $arParams['NU_SEQ_PERIODO_VINCULACAO'] ) {
				$select->where("C.NU_SEQ_PERIODO_VINCULACAO = ?", $arParams['NU_SEQ_PERIODO_VINCULACAO']);
			}
			if ( $arParams['DT_FINAL'] ) {
				$select->where("C.DT_FINAL = ?", $arParams['DT_FINAL']);
			}
			if ( $arParams['VL_EXERCICIO'] ) {
				$select->where("C.VL_EXERCICIO = ?", $arParams['VL_EXERCICIO']);
			}
			if ( $arParams['DT_INICIAL'] ) {
				$select->where("C.DT_INICIAL = ?", $arParams['DT_INICIAL']);
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
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function getSelect( $arColumns ) {
		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Função para obter uma lista dos períodos de vinculação.
	 */
	public function getListPeriodoVincBolsa( $id ) {

		$query = "  SELECT * ";
		$query .= "	  FROM SICE_FNDE.S_BOLSA BLS ";
		$query .= "	 INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO PVC ON BLS.NU_SEQ_PERIODO_VINCULACAO = PVC.NU_SEQ_PERIODO_VINCULACAO ";
		$query .= "	 WHERE  ";
		$query .= "		(PVC.DT_INICIAL = (SELECT DT_INICIAL FROM SICE_FNDE.S_PERIODO_VINCULACAO WHERE NU_SEQ_PERIODO_VINCULACAO = $id)) ";
		$query .= "	   AND ";
		$query .= "		(PVC.DT_FINAL =(SELECT DT_FINAL FROM SICE_FNDE.S_PERIODO_VINCULACAO WHERE NU_SEQ_PERIODO_VINCULACAO = $id)) ";
		$query .= "	   AND BLS.ST_BOLSA NOT IN (6) ";

		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;

	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function del( $id ) {

		// refazer essa validação após termos o cenário 5 disponível para avaliação da bolsa.
		/* if($this->validaExclusao($arParams) > 0){
		    return false;
		} */

		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		try {
			$where = "NU_SEQ_PERIODO_VINCULACAO = " . $id;
			return $obModelo->delete($where);
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Seleciona PeriodoVinculacao
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
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
	 * Obtem PeriodoVinculacao por Id
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function getPeriodoVinculacaoById( $id ) {
		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		$select = $obModelo->select()->where("NU_SEQ_PERIODO_VINCULACAO = ?", $id);
		$stmt = $select->query();
		$result = $stmt->fetch();
		return $result;
	}

	/**
	 * Obtem PeriodoVinculacao por Id
	 *
	 * @author diego.matos
	 * @since 12/06/2012
	 * @param array $arParam
	 */
	public function getPeriodoVinculacaoByAno( $arParam ) {
		$query = " SELECT * FROM SICE_FNDE.S_PERIODO_VINCULACAO WHERE VL_EXERCICIO = :VL_EXERCICIO ";
		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		$stm = $obModelo->getAdapter()->query($query, $arParam);
		$result = $stm->fetchAll();
		return $result[0];
	}

	/**
	 * Obtem vários registros de PeriodoVinculacao pelo ano de exercício
	 *
	 * @author diego.matos
	 * @since 25/06/2012
	 * @param array $arParam
	 */
	public function getListPeriodoVinculacaoByAno( $arParam ) {
		$query = " SELECT DISTINCT TO_CHAR(DT_INICIAL, 'DD/MM/YYYY') AS DT_INICIAL, TO_CHAR(DT_FINAL, 'DD/MM/YYYY') AS DT_FINAL, NU_SEQ_PERIODO_VINCULACAO ";
		$query .= " FROM SICE_FNDE.S_PERIODO_VINCULACAO WHERE VL_EXERCICIO = :VL_EXERCICIO AND NU_SEQ_TIPO_PERFIL = 6 ORDER BY DT_INICIAL";
		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		$stm = $obModelo->getAdapter()->query($query, $arParam);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Efetua a pesquisa dos módulos cadastrado de acordo com o filtro informado.
	 * @param array $arParams
	 */
	public function pesquisarPeriodoVinculacaoPorPeril( $nuSeqPerfil ) {

		$arrayParametros;
		$query = " SELECT ";
		$query .= " PERIODOVINCULACAO.NU_SEQ_PERIODO_VINCULACAO, ";
		$query .= " PERFIL.DS_TIPO_PERFIL, ";
		$query .= " PERIODOVINCULACAO.VL_EXERCICIO, ";
		$query .= " (TO_CHAR(PERIODOVINCULACAO.DT_INICIAL, 'DD/MM/YYYY')), ";
		$query .= " (TO_CHAR(PERIODOVINCULACAO.DT_FINAL, 'DD/MM/YYYY')), ";
		$query .= " (TO_CHAR(PERIODOVINCULACAO.DT_INCLUSAO, 'DD/MM/YYYY')) ";
		$query .= " FROM ";
		$query .= " SICE_FNDE.S_PERIODO_VINCULACAO PERIODOVINCULACAO, SICE_FNDE.S_TIPO_PERFIL PERFIL ";
		$query .= " WHERE ";
		$query .= " PERIODOVINCULACAO.NU_SEQ_TIPO_PERFIL = PERFIL.NU_SEQ_TIPO_PERFIL ";
		$query .= " AND ";
		$query .= " PERIODOVINCULACAO.NU_SEQ_TIPO_PERFIL = :NU_SEQ_TIPO_PERFIL ";

		$arrayParametros['NU_SEQ_TIPO_PERFIL'] = $nuSeqPerfil;

		$query .= " ORDER BY PERIODOVINCULACAO.DT_INICIAL";

		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		// 		$obModelo->fixDateToBr();

		$stm = $obModelo->getAdapter()->query($query, $arrayParametros);
		$result = $stm->fetchAll();
		return $result;

	}

	/**
	 * Função para gravar períodos de vinculação no banco de dados.
	 * @param array $arrayPeriodoVinculacao
	 */
	public function salvarPeriodoVinculacao( $arrayPeriodoVinculacao ) {

		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		$obModelo->getAdapter()->beginTransaction();
		$obModelo->fixDateToBr();

		for ( $j = 0; $j < count($arrayPeriodoVinculacao); $j++ ) {

			$existe = $this->verificaExistenciaRegistroPeriodoVinculacao($arrayPeriodoVinculacao[$j]['VL_EXERCICIO'],
					$arrayPeriodoVinculacao[$j]['DT_INICIAL'], $arrayPeriodoVinculacao[$j]['DT_FINAL'],
					$arrayPeriodoVinculacao[$j]['NU_SEQ_TIPO_PERFIL']);
			if ( !$existe ) {
				$obModelo->insert($arrayPeriodoVinculacao[$j]);
			}
		}
		$obModelo->getAdapter()->commit();
	}

	/**
	 * Função para verificar a existência de períodos de vinculação.
	 */
	public function verificaExistenciaPeriodoVinculacao() {
		$query = " SELECT ";
		$query .= "PERIODOVINCULACAO.NU_SEQ_PERIODO_VINCULACAO ";
		$query .= "FROM SICE_FNDE.S_PERIODO_VINCULACAO PERIODOVINCULACAO";

		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Função para verificar a existência de registros pelo período de vinculação.
	 * 
	 * @param int $anoExercicio
	 * @param date $dtInicial
	 * @param date $dtFinal
	 * @param int $tipoPerfil
	 */
	public function verificaExistenciaRegistroPeriodoVinculacao( $anoExercicio, $dtInicial, $dtFinal, $tipoPerfil ) {

		$select = $this->getSelect(array('NU_SEQ_PERIODO_VINCULACAO'));
		$select->where("VL_EXERCICIO = ?", $anoExercicio);
		$select->where("DT_INICIAL = ?", $dtInicial);
		$select->where("DT_FINAL = ?", $dtFinal);
		$select->where("NU_SEQ_TIPO_PERFIL = ?", $tipoPerfil);

		$stm = $select->query();
		$result = $stm->fetchAll();
		return $result;

	}

	/**
	 * Função para verificar a existrência de período de vinculação pelo ano anterior.
	 * @param int $anoAnterior
	 */
	public function verificaExistenciaPeriodoVinculacaoAnoAnterior( $anoAnterior ) {

		$select = $this->getSelect(array('NU_SEQ_PERIODO_VINCULACAO'));
		$select->where("VL_EXERCICIO = ?", $anoAnterior);

		$stm = $select->query();
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_PERIODO_VINCULACAO');
	}

	/**
	 * Valida se um período de vinculação pode ser removido de acordo com a RN013.
	 *
	 * @author diego.matos
	 * @since 23/05/2012
	 * @param $arParams array
	 */
	public function validaExclusao( $arParams ) {
		$query = " SELECT ";
		$query .= " COUNT(VBP.NU_SEQ_VAL_BOLSA_PERF) AS RESULT ";
		$query .= " FROM SICE_FNDE.S_PERIODO_VINCULACAO PV ";
		$query .= " INNER JOIN SICE_FNDE.S_TIPO_PERFIL PERF ON PV.NU_SEQ_TIPO_PERFIL = PERF.NU_SEQ_TIPO_PERFIL ";
		$query .= " INNER JOIN SICE_FNDE.S_VINCULA_CONF_PERFIL VCP ON PERF.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL ";
		$query .= " INNER JOIN SICE_FNDE.S_VALOR_BOLSA_PERFIL VBP ON VCP.NU_SEQ_VINC_CONF_PERF = VBP.NU_SEQ_VINC_CONF_PERF ";
		$query .= " INNER JOIN SICE_FNDE.S_CONFIGURACAO CONF ON VCP.NU_SEQ_CONFIGURACAO = CONF.NU_SEQ_CONFIGURACAO ";
		$query .= " WHERE ";
		$query .= " PV.VL_EXERCICIO = :VL_EXERCICIO AND PV.NU_SEQ_TIPO_PERFIL = :NU_SEQ_TIPO_PERFIL AND CONF.ST_CONFIGURACAO = 'A' ";

		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		;
		$stm = $obModelo->getAdapter()->query($query, $arParams);
		$result = $stm->fetch();
		return $result['RESULT'];
	}

	/**
	 * Recupera os anos já inseridos via período de vinculação
	 *
	 * @author diego.matos
	 * @since 25/06/2012
	 */
	public function obterAnosPeriodoVinculacao() {
		$query = "SELECT DISTINCT VL_EXERCICIO FROM SICE_FNDE.S_PERIODO_VINCULACAO ORDER BY VL_EXERCICIO";
		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		;
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Obtem as datas de um período de vinculação específico
	 *
	 * @author diego.matos
	 * @since 13/07/2012
	 * @param array $arParam
	 */
	public function getDatasPeriodoById( $arParam ) {
		$query = " SELECT TO_CHAR(DT_INICIAL, 'DD/MM/YYYY') AS DT_INICIAL, TO_CHAR(DT_FINAL, 'DD/MM/YYYY') AS DT_FINAL ";
		$query .= " FROM SICE_FNDE.S_PERIODO_VINCULACAO WHERE NU_SEQ_PERIODO_VINCULACAO = :NU_SEQ_PERIODO_VINCULACAO ";
		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		$stm = $obModelo->getAdapter()->query($query, $arParam);
		$result = $stm->fetch();
		return $result;
	}

	/**
	 * Obtem o período de vinculação pelo identificador de bolsa
	 *
	 * @param INT $idBolsa
	 */
	public function getPeriodoVinculacaoByIdBolsa( $idBolsa ) {

		$query = " SELECT ";
		$query .= "		PVI.NU_SEQ_PERIODO_VINCULACAO, ";
		$query .= "		PVI.VL_EXERCICIO, ";
		$query .= "		TO_CHAR(PVI.DT_INICIAL, 'DD/MM/YYYY') DT_INICIAL, ";
		$query .= "		TO_CHAR(PVI.DT_FINAL, 'DD/MM/YYYY') DT_FINAL, ";
		$query .= "		TO_CHAR(PVI.DT_INCLUSAO, 'DD/MM/YYYY') DT_INCLUSAO, ";
		$query .= "		PVI.NU_SEQ_TIPO_PERFIL ";
		$query .= "	FROM SICE_FNDE.S_PERIODO_VINCULACAO PVI ";
		$query .= "	INNER JOIN SICE_FNDE.S_BOLSA BLS ON PVI.NU_SEQ_PERIODO_VINCULACAO = BLS.NU_SEQ_PERIODO_VINCULACAO ";
		$query .= "	WHERE BLS.NU_SEQ_BOLSA = $idBolsa";

		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result;
	}

	/**
	 * Obtem o período de vinculação pelo identificador de bolsa
	 *
	 * @param INT $idBolsa
	 */
	public function getPeriodoVinculacaoByDtFim( $dtFim ) {
		$query = "SELECT NU_SEQ_PERIODO_VINCULACAO ";
		$query .= "FROM SICE_FNDE.S_PERIODO_VINCULACAO ";
		$query .= "WHERE (TO_DATE('" . $dtFim . "', 'DD/MM/YYYY') >= DT_INICIAL AND TO_DATE('" . $dtFim
				. "', 'DD/MM/YYYY') <= DT_FINAL) ";
		$query .= "AND NU_SEQ_TIPO_PERFIL = 6 ";

		$obModelo = new Fnde_Sice_Model_PeriodoVinculacao();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result['NU_SEQ_PERIODO_VINCULACAO'];
	}
}
