<?php

/**
 * Business de Historico Bolsa
 *
 * @author fabiana.rose
 * @since 30/08/2012
 */

class Fnde_Sice_Business_VisualizaHistBolsa {

	/**
	 * Retorna array das colunas do termo de referencia para a grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */

	public function getColumnsSearch() {
		return array('DS_OBSERVACAO' => 'C.DS_OBSERVACAO', 'DT_HISTORICO' => 'C.DT_HISTORICO',
				'NU_SEQ_BOLSA' => 'C.NU_SEQ_BOLSA', 'NU_SEQ_HISTORICO_BOLSA' => 'C.NU_SEQ_HISTORICO_BOLSA',
				'NU_SEQ_JUSTIF_CANCELAMENTO' => 'C.NU_SEQ_JUSTIF_CANCELAMENTO',
				'NU_SEQ_JUSTIF_DEV_BOLSA' => 'C.NU_SEQ_JUSTIF_DEV_BOLSA',
				'NU_SEQ_JUSTIF_INAPTIDAO' => 'C.NU_SEQ_JUSTIF_INAPTIDAO', 'NU_SEQ_USUARIO' => 'C.NU_SEQ_USUARIO',
				'ST_APTIDAO' => 'C.ST_APTIDAO', 'ST_BOLSA' => 'C.ST_BOLSA',);
	}

	/**
	 * Monta o select para consulta TOR
	 *
	 * @author fabiana.rose
	 * @since 30/08/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_HISTORICO_BOLSA']) ) {
			$select->where("C.NU_SEQ_HISTORICO_BOLSA = {$arParams['id']} ");
		}
	}

	/**
	 * Recupera o select para listagem
	 *
	 * @author fabiana.rose
	 * @since 30/08/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_HistoricoBolsa();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * seleciona historico bolsa
	 *
	 * @author fabiana.rose
	 * @since 30/08/2012
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
	 *  Pesquisa os dados do bolsista e a quantidade de turmas finalizadas
	 *  @param string $idUsuario;
	 *  @param string $arParams;
	 */
	public function getDadosBolsista( $idUsuario ) {
        
        $query = "SELECT USU.NU_SEQ_USUARIO,
                  USU.NO_USUARIO AS NOME,
                  USU.NU_CPF,
                  PER.DS_TIPO_PERFIL AS PERFIL,
                  USU.SG_UF_ATUACAO_PERFIL,
                  MES.NO_MESO_REGIAO,
                  MES.NO_MUNICIPIO,
                  COUNT(TUR.ST_TURMA) AS QTD_TURMAS_BOLSA
                FROM SICE_FNDE.S_USUARIO USU
                INNER JOIN SICE_FNDE.S_TIPO_PERFIL PER
                  ON PER.NU_SEQ_TIPO_PERFIL = USU.NU_SEQ_TIPO_PERFIL
                INNER JOIN CTE_FNDE.T_MESO_REGIAO MES
                  ON USU.CO_MUNICIPIO_PERFIL = MES.CO_MUNICIPIO_IBGE
                LEFT JOIN SICE_FNDE.S_BOLSA BOL
                  ON USU.NU_SEQ_USUARIO = BOL.NU_SEQ_USUARIO
                LEFT JOIN SICE_FNDE.S_AVALIACAO_TURMA AVA
                  ON AVA.NU_SEQ_BOLSA = BOL.NU_SEQ_BOLSA
                LEFT JOIN SICE_FNDE.S_TURMA TUR
                  ON TUR.NU_SEQ_TURMA = AVA.NU_SEQ_TURMA
                WHERE USU.NU_SEQ_USUARIO   = {$idUsuario}
                GROUP BY USU.NU_SEQ_USUARIO,
                  USU.NO_USUARIO,
                  USU.NU_CPF,
                  PER.DS_TIPO_PERFIL,
                  USU.SG_UF_ATUACAO_PERFIL,
                  MES.NO_MESO_REGIAO,
                  MES.NO_MUNICIPIO";

        $obModelo = new Fnde_Sice_Model_Bolsa();

		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result;

	}

	/**
	 * pesquisa o periodo de vinculação e os valores das bolsas do usuario.
	 * @param string $idUsuario
	 * @param string $idBolsa
	 */
	public function getPeriodoVinculacaoBolsa( $idUsuario ) {
		$query = "SELECT 
			DT_INICIAL,
			DT_FINAL,
			NU_SEQ_USUARIO,
			NU_SEQ_BOLSA,
			  SUM(VL_BOLSA)            AS VL_BOLSA
			FROM
			  ( WITH filtro AS
			  (SELECT vcp1.*,
			    vbp1.qt_turma
			  FROM sice_fnde.s_vincula_conf_perfil vcp1,
			    sice_fnde.s_valor_bolsa_perfil vbp1
			  WHERE vcp1.nu_seq_vinc_conf_perf = vbp1.nu_seq_vinc_conf_perf
			  )
			SELECT 
			  TO_CHAR(PVC.DT_INICIAL, 'DD/MM/YYYY') AS DT_INICIAL,
			  TO_CHAR(PVC.DT_FINAL, 'DD/MM/YYYY') AS DT_FINAL,
			  BLS.NU_SEQ_BOLSA,
			  TPE.DS_TIPO_PERFIL,
			  USU.SG_UF_ATUACAO_PERFIL,
			  USU.NU_SEQ_USUARIO,
			  USU.NU_CPF,
			  USU.CO_MUNICIPIO_PERFIL,
			  SBO.DS_SITUACAO_BOLSA,
			  VCP.QT_BOLSA_PERIODO,
			  MSR.NO_MESO_REGIAO,
			  BLS.ST_APTIDAO,
			  VBP.VL_BOLSA,
			  QT_TURMAS_APROVADAS,
			  QT_TURMAS_REPROVADAS,
			  QT_TURMAS_AVALIADAS,
			  qt_total
			FROM SICE_FNDE.S_BOLSA BLS
			INNER JOIN SICE_FNDE.S_USUARIO USU ON BLS.NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO
			INNER JOIN CTE_FNDE.T_MESO_REGIAO MSR ON USU.CO_MUNICIPIO_PERFIL = MSR.CO_MUNICIPIO_IBGE
			INNER JOIN SICE_FNDE.S_TIPO_PERFIL TPE ON USU.NU_SEQ_TIPO_PERFIL = TPE.NU_SEQ_TIPO_PERFIL
			INNER JOIN SICE_FNDE.S_SITUACAO_BOLSA SBO ON BLS.ST_BOLSA = SBO.NU_SEQ_SITUACAO_BOLSA
			INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO PVC ON BLS.NU_SEQ_PERIODO_VINCULACAO = PVC.NU_SEQ_PERIODO_VINCULACAO
			INNER JOIN
			  (SELECT SUM(DECODE (AVT.ST_APROVACAO, 'S', 1, 0)) AS QT_TURMAS_APROVADAS,
			    SUM(DECODE ( AVT.ST_APROVACAO ,'N', 1,0))       AS QT_TURMAS_REPROVADAS,
			    SUM(NVL2(AVT.ST_APROVACAO, 1,0))                AS QT_TURMAS_AVALIADAS,
			    COUNT(1)                                        AS qt_total,
			    NU_SEQ_BOLSA ,
			    TUR.NU_SEQ_CONFIGURACAO
			  FROM SICE_FNDE.S_AVALIACAO_TURMA AVT
			  INNER JOIN SICE_FNDE.S_TURMA TUR ON avt.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA
			  GROUP BY AVT.NU_SEQ_BOLSA,
			    TUR.NU_SEQ_CONFIGURACAO
			  ) TUR ON BLS.NU_SEQ_BOLSA = TUR.NU_SEQ_BOLSA
			INNER JOIN SICE_FNDE.S_CONFIGURACAO CFG ON TUR.NU_SEQ_CONFIGURACAO = CFG.NU_SEQ_CONFIGURACAO
			INNER JOIN SICE_FNDE.S_VINCULA_CONF_PERFIL VCP ON CFG.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO
												AND TPE.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL
			INNER JOIN SICE_FNDE.S_VALOR_BOLSA_PERFIL VBP ON VCP.NU_SEQ_VINC_CONF_PERF   = VBP.NU_SEQ_VINC_CONF_PERF
			WHERE USU.NU_SEQ_USUARIO = '{$idUsuario}'
			AND vcp.nu_seq_vinc_conf_perf IN
			  (SELECT f.nu_seq_vinc_conf_perf
			  FROM filtro f
			  WHERE f.nu_seq_configuracao      = vcp.nu_seq_configuracao
			  AND f.nu_seq_tipo_perfil         = vcp.nu_seq_tipo_perfil
			  AND tur.qt_total                >= f.qt_turma
			  AND f.nu_seq_vinc_conf_perf NOT IN
			    (SELECT f2.nu_seq_vinc_conf_perf
			    FROM filtro f2
			    WHERE f.nu_seq_configuracao = f2.nu_seq_configuracao
			    AND f.nu_seq_tipo_perfil    = f2.nu_seq_tipo_perfil
			    AND f.qt_turma              < tur.qt_total
			    AND EXISTS
			      (SELECT 1
			      FROM filtro f3
			      WHERE f3.nu_seq_configuracao = f2.nu_seq_configuracao
			      AND f3.nu_seq_tipo_perfil    = f2.nu_seq_tipo_perfil
			      AND f3.qt_turma             >= tur.qt_total
			      )
			    )
			  )
			  )
			GROUP BY 
			  DT_INICIAL,
			  DT_FINAL,
			  NU_SEQ_USUARIO,
			  NU_SEQ_BOLSA";

		$obModelo = new Fnde_Sice_Model_Bolsa();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * pesquisa o historico das bolsas daquele bolsista em especifico
	 * @param string $idUsuario
	 * @param string $idBolsa
	 */
	public function getHistoricoBolsista( $idUsuario, $idBolsa ) {

		$query = " SELECT DISTINCT HBS.NU_SEQ_HISTORICO_BOLSA, BOL.NU_SEQ_USUARIO, HBS.NU_SEQ_BOLSA, SIT.DS_SITUACAO_BOLSA ";
		$query .= "                  ,TO_CHAR(HBS.DT_HISTORICO, 'DD/MM/YYYY HH24:MI') AS DT_HISTORICO ";
		$query .= "                  ,USU.NO_USUARIO AS AUTOR ";
		$query .= "                  ,PER.DS_TIPO_PERFIL AS PERFIL ";
		$query .= "                  ,HBS.DS_OBSERVACAO ";
		$query .= "  FROM SICE_FNDE.S_HISTORICO_BOLSA HBS ";
		$query .= " INNER JOIN SICE_FNDE.S_USUARIO USU ON USU.NU_SEQ_USUARIO = HBS.NU_SEQ_USUARIO ";
		$query .= " INNER JOIN SICE_FNDE.S_BOLSA BOL   ON BOL.NU_SEQ_BOLSA = HBS.NU_SEQ_BOLSA ";
		$query .= " INNER JOIN SICE_FNDE.S_TIPO_PERFIL PER       ON PER.NU_SEQ_TIPO_PERFIL = USU.NU_SEQ_TIPO_PERFIL ";
		$query .= " INNER JOIN SICE_FNDE.S_SITUACAO_BOLSA SIT ON BOL.ST_BOLSA = SIT.NU_SEQ_SITUACAO_BOLSA ";
		$query .= " WHERE BOL.NU_SEQ_USUARIO = {$idUsuario} ";
		$query .= "   AND HBS.NU_SEQ_BOLSA = {$idBolsa} ";
		$query .= "   AND HBS.ST_BOLSA NOT IN(1,6,7,9) "; //REGRA DE NEGÓCIOS E PROTÓTIPO QUAIS STATUS DE BOLSA SERÃO EXIBIDOS NO HISTORICO.
		$query .= " ORDER BY HBS.NU_SEQ_HISTORICO_BOLSA ";

		$obModelo = new Fnde_Sice_Model_Bolsa();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

}
