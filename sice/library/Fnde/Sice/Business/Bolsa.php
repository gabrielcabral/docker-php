<?php

/**
 * Business do Usuario
 *
 * @author diego.matos
 * @since 10/04/2012
 */
class Fnde_Sice_Business_Bolsa {

    /**
     * Recupera select para listagem
     *
     * @access public
     * @return object - Objeto Select
     *
     * @author diego.matos
     * @since 10/04/2012
     */
    public function getSelect($arColumns) {

        $obModelo = new Fnde_Sice_Model_Bolsa();
        $obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
        $arInfoModelo = $obModelo->info();

        $select = $obModelo->select()->setIntegrityCheck(false)->from(
                        array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
        return $select;
    }

    /**
     * Obtem Bolsa por Id
     *
     * @author diego.matos
     * @since 10/04/2012
     */
    public function getBolsaById($id, $boArray = true) {

        $obModelo = new Fnde_Sice_Model_Bolsa();
        $obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY'");

        $aDados = $obModelo->find($id)->current();
        if ($aDados) {
            return $boArray ? $aDados->toArray() : $aDados;
        }
        return $boArray ? $obModelo->createRow()->toArray() : $obModelo->createRow();
    }

    /**
     * Efetua a pesquisa de bolsas baseado nas condições da RN82.
     *
     * @param array $arParams
     * @param array $arUsuarioSelecionado Código dos usuários selecionados. Parâmetro usado na tela Avaliar Bolsas.
     */
    public function gerarBolsas($arParams, $arUsuarioSelecionado = null) {

        $businessUf = new Fnde_Sice_Business_Uf();

        if ($arParams['SG_REGIAO'] == 'T') {
            $resUF = $businessUf->getRegiaoByUf($arParams['SG_UF']);
            $arParams['SG_REGIAO'] = $resUF['SG_REGIAO'];
        }

        $modelBolsa = new Fnde_Sice_Model_Bolsa();
        $obModelo = new Fnde_Sice_Model_Bolsa();

        try {

            //QUERY PARA TRAZER RESULTADOS RELACIONADOS À COORDENADOR EXECUTIVO ESTADUAL
            $queryCoordExec = $modelBolsa->retornaQueryCoordenadorExecutivoEstadual($arParams);
            $stm = $obModelo->getAdapter()->query($queryCoordExec);
            $rsCoordExec = $stm->fetchAll();
            $this->inserirBolsa($rsCoordExec);

            //QUERY PARA TRAZER INFORMAÇÕES DE ARTICULADOR
            $queryArticulador = $modelBolsa->retornaQueryArticulador($arParams);
            $stm = $obModelo->getAdapter()->query($queryArticulador);
            $rsArticulador = $stm->fetchAll();
            $this->inserirBolsa($rsArticulador);

            //QUERY PARA TUTOR
            $query = $modelBolsa->retornaQueryTutor($arParams);
            $stm = $obModelo->getAdapter()->query($query);
            $rsTutor = $stm->fetchAll();
            $this->inserirBolsa($rsTutor);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Insere bolsas e vínculos de bolsas com turmas no banco de dados.
     * @param array $arParams
     */
    public function inserirBolsa($arParams) {

        $obModelo = new Fnde_Sice_Model_Bolsa();

        $idUsuario = null;
        for ($i = 0; $i < count($arParams); $i++) {
            if (
                $arParams[$i]["QT_NAO_FINALIZADA"] == 0 ||
                // Se for articulador e possuir pelo menos 2 turmas finalizadas, pode receber bolsa.
                ($arParams[$i]["NU_SEQ_TIPO_PERFIL"] == 5 && $arParams[$i]["DT_FINALIZACAO"] && $arParams[$i]["QT_FINALIZADA"] >= 2)
            ) {
                if (!$idUsuario || $idUsuario != $arParams[$i]["NU_SEQ_USUARIO"]) {

                    $idUsuario = $arParams[$i]["NU_SEQ_USUARIO"];

                    $obModelo->getAdapter()->beginTransaction();

                    $sequence = $obModelo->getAdapter()->query("SELECT SICE_FNDE.SBLS_NU_SEQ_BOLSA_SQ.NEXTVAL FROM DUAL")->fetch();
                    $idBolsa = $sequence['NEXTVAL'];

                    $insert = array(
                        "NU_SEQ_BOLSA" => $idBolsa,
                        "NU_SEQ_USUARIO" => $arParams[$i]["NU_SEQ_USUARIO"],
                        "ST_BOLSA" => "6",
                        "NU_SEQ_PERIODO_VINCULACAO" => $arParams[$i]['NU_SEQ_PERIODO_VINCULACAO'],
                        "DT_FINALIZACAO_TURMA" => $arParams[$i]['DT_FINALIZACAO'],
                        "NU_SEQ_CONFIGURACAO" => $arParams[$i]['NU_SEQ_CONFIGURACAO']
                    );
                    try {
                         $obModelo->getAdapter()->query("call SICE_FNDE.SBLS_I_PC($idBolsa, " . $insert['NU_SEQ_USUARIO'] . ",null,null,null,null,6, " . $insert['NU_SEQ_PERIODO_VINCULACAO'] . " , to_date('" . $insert['DT_FINALIZACAO_TURMA'] . "', 'DD/MM/YYYY')," . $insert['NU_SEQ_CONFIGURACAO'] . ")");

                        if ($arParams[$i]["NU_SEQ_TIPO_PERFIL"] == 6) {
                            $obModelo->getAdapter()->query("INSERT INTO SICE_FNDE.S_AVALIACAO_TURMA (NU_SEQ_AVALIACAO_TURMA,NU_SEQ_TURMA,NU_SEQ_BOLSA) VALUES (SICE_FNDE.SVLT_NU_SEQ_AVALIACAO_TUR_SQ.NEXTVAL,".$arParams[$i]['NU_SEQ_TURMA'].",".$idBolsa.")");
                        }
                        $obModelo->getAdapter()->commit();
                    } catch (Exception $e) {
                        $obModelo->getAdapter()->rollBack();
                        throw $e;
                    }
                }
            }
        }
    }

    /**
     * Função para pesquisar bolsas cadastradas no banco.
     * @param array $arParams
     */
    public function pesquisarBolsas($arParams) {

        $modelBolsa = new Fnde_Sice_Model_Bolsa();

        if ($this->isBolsaAntiga($arParams['NU_SEQ_PERIODO_VINCULACAO'])) {
            $businessPeriodoVinc = new Fnde_Sice_Business_PeriodoVinculacao();

            $periodo = $businessPeriodoVinc->getDatasPeriodoById(
                    array("NU_SEQ_PERIODO_VINCULACAO" => $arParams['NU_SEQ_PERIODO_VINCULACAO']));

            $arParams['DT_INICIAL'] = (string) $periodo['DT_INICIAL'];
            $arParams['DT_FINAL'] = (string) $periodo['DT_FINAL'];

            $query = $modelBolsa->retornaQueryPesquisaBolsaAntiga($arParams);
        } else {
            $query = $modelBolsa->retornaQueryPesquisaBolsa($arParams);
        }

        $stm = $modelBolsa->getAdapter()->query($query);
        $result = $stm->fetchAll();
        return $result;
    }

    /**
     * Função para pesquisar bolsas para a tela de avaliação de bolsas.
     * @param array $arBolsas
     */
    public function pesquisarBolsasAvaliacao($params) {

        $obModelo = new Fnde_Sice_Model_Bolsa();
        if (is_array($params['PERFIL_BOLSISTAS'])) {
            $strPerfis = implode(',', array_unique($params['PERFIL_BOLSISTAS']));
            $strPeriodo = implode(',', array_unique($params['PERIODO_BOLSISTAS']));
        } else {
            $strPerfis = $params['PERFIL_BOLSISTAS'];
            $strPeriodo = $params['PERIODO_BOLSISTAS'];
        }

        $query = $obModelo->retornaQueryPesquisarBolsasAvaliacao($strPerfis, $strPeriodo, $params);

        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetchAll();
        return $result;
    }

    /**
     * Função para pesquisar bolsas para a tela de avaliação de bolsas.
     * @param array $arBolsas
     */
    public function pesquisarBolsasAvaliacaoAntiga($arUfs, $arPerfis, $arPeriodo) {

        $obModelo = new Fnde_Sice_Model_Bolsa();

        if (is_array($arPerfis)) {
            $seqPerfis = implode(',', $arPerfis);
        } else {
            $seqPerfis = $arPerfis;
        }

        $query = $obModelo->retornaQueryPesquisarBolsasAvaliacaoAntiga("'" . $arUfs . "'", $seqPerfis, $arPeriodo);
        //die($query);
        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetchAll();
        return $result;
    }

    /**
     * Função para pesquisar bolsas cadastradas no banco para a tela de solicitar homologação.
     * @param array $arBolsas
     */
    public function pesquisarBolsasSolicitHomol($arUfs, $arPerfis, $periodo) {

        $obModelo = new Fnde_Sice_Model_Bolsa();

        if (is_array($arPerfis)) {
            $seqPerfis = implode(',', $arPerfis);
        } else {
            $seqPerfis = $arPerfis;
        }

        if ($arUfs && $seqPerfis) {
            $query = $obModelo->retornaQueryPesquisarBolsasSolicitHomol("'" . $arUfs . "'", $seqPerfis, $periodo);
            //die($query);
            $stm = $obModelo->getAdapter()->query($query);
            $result = $stm->fetchAll();
            return $result;
        }
        return false;
    }

    /**
     * Função para pesquisar bolsas cadastradas no banco para a tela de homologação.
     * @param array $arBolsas
     */
    public function pesquisarBolsasHomologacao($arUfs, $arPerfis, $periodo) {

        $obModelo = new Fnde_Sice_Model_Bolsa();

        if (is_array($arPerfis)) {
            $seqPerfis = implode(',', $arPerfis);
        } else {
            $seqPerfis = $arPerfis;
        }

        if ($arUfs && $seqPerfis) {
            $query = $obModelo->retornaQueryPesquisarBolsasHomologacao("'" . $arUfs . "'", $seqPerfis, $periodo);
            //die($query);
            $stm = $obModelo->getAdapter()->query($query);
            $result = $stm->fetchAll();
            return $result;
        }
        return false;
    }

    /**
     * Função para mudar o status das bolsas informadas como parâmetro
     * @param array $arParam
     * @author diego.matos
     * @since 13/07/2012
     */
    public function alterarStatusBolsa($arParam) {

        $seqBolsa = $arParam['NU_SEQ_BOLSA'];
        $strBolsas = "({$seqBolsa})";

        $obModel = new Fnde_Sice_Model_Bolsa();
        $arBolsa = array('ST_BOLSA' => $arParam['ST_BOLSA']);
        $where = 'NU_SEQ_BOLSA IN ' . $strBolsas;

        try {
            return $obModel->update($arBolsa, $where);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Gravar avaliação da bolsa
     * @param array $arBolsa
     */
    public function avaliarBolsa($arBolsa) {
        try {
            $obModelo = new Fnde_Sice_Model_Bolsa();
            $where = " NU_SEQ_BOLSA = " . $arBolsa['NU_SEQ_BOLSA'];
            return $obModelo->update($arBolsa, $where);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Função para pesquisar bolsas cadastradas no banco para a tela de solicitar homologação.
     * @param array $arBolsas
     */
    public function pesquisarBolsasEnviarSgb($arUfs, $arPerfis, $periodo) {
        $obModelo = new Fnde_Sice_Model_Bolsa();

        if (is_array($arPerfis)) {
            $seqPerfis = implode(',', $arPerfis);
        } else {
            $seqPerfis = $arPerfis;
        }

        $query = $obModelo->retornaQueryPesquisarBolsasEnviarSgb("'" . $arUfs . "'", $seqPerfis, $periodo);
        //die($query);
        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetchAll();
        return $result;
    }

    /**
     * Função para pesquisar bolsas cadastradas no banco para a tela de solicitar homologação.
     * @param array $arBolsas
     */
    public function pesquisarBolsasVerifPend($arUfs, $arPerfis, $stBolsa, $periodo) {

        $obModelo = new Fnde_Sice_Model_Bolsa();

        if (is_array($arPerfis)) {
            $seqPerfis = implode(',', $arPerfis);
        } else {
            $seqPerfis = $arPerfis;
        }

        $query = $obModelo->retornaQueryPesquisarBolsasVerifPend("'" . $arUfs . "'", $seqPerfis, $stBolsa, $periodo);
        //die($query);
        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetchAll();
        return $result;
    }

    /**
     * Salvar dados em histórico da bolsa
     * 
     * @param array $arParams
     */
    public function salvarHistoricoBolsa($arParams) {
        $obModeloHis = new Fnde_Sice_Model_HistoricoBolsa();
        $obModeloHis->fixDateToBr();

        return $obModeloHis->insert($arParams);
    }

    /**
     * Recuperar a bolsa pelo Id retornando os dados necessário para gerar o PDF do SGB
     * 
     * @param int $id
     */
    public function getBolsaPdfById($id) {
        $query = " SELECT ";
        $query .= " 	USU.SG_UF_ATUACAO_PERFIL, ";
        $query .= " 	USU.NU_SEQ_TIPO_PERFIL, ";
        $query .= " 	USU.CO_MUNICIPIO_PERFIL, ";
        $query .= " 	TPF.DS_TIPO_PERFIL, ";
        $query .= " 	USU.NO_USUARIO, ";
        $query .= " 	USU.NU_CPF, ";
        $query .= " 	TPFAVA.DS_TIPO_PERFIL DS_TIPO_PERFIL_AVALIADOR, ";
        $query .= " 	USUAVA.NO_USUARIO NO_USUARIO_AVALIADOR, ";
        $query .= " 	USUAVA.NU_CPF NU_CPF_AVALIADOR, ";
        $query .= " 	T.TOTAL_TURMA_TUTOR_FINALIZADA ";
        $query .= " FROM SICE_FNDE.S_BOLSA BLS ";
        $query .= " LEFT JOIN SICE_FNDE.S_USUARIO           USU ON    BLS.NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO ";
        $query .= " LEFT JOIN SICE_FNDE.S_TIPO_PERFIL       TPF ON    USU.NU_SEQ_TIPO_PERFIL = TPF.NU_SEQ_TIPO_PERFIL ";
        $query .= " LEFT JOIN SICE_FNDE.S_USUARIO           USUAVA ON BLS.NU_SEQ_USUARIO_AVALIADOR = USUAVA.NU_SEQ_USUARIO ";
        $query .= " LEFT JOIN SICE_FNDE.S_TIPO_PERFIL       TPFAVA ON USUAVA.NU_SEQ_TIPO_PERFIL = TPFAVA.NU_SEQ_TIPO_PERFIL ";
        $query .= " LEFT JOIN (
                                 select count(*) as total_turma_tutor_finalizada, t.nu_seq_usuario_tutor, b.nu_seq_bolsa
                                 from sice_fnde.s_turma t
                                 inner join sice_fnde.s_bolsa b ON b.nu_seq_periodo_vinculacao = t.nu_seq_periodo_vinculacao
                                 where st_turma = 11  group by t.nu_seq_usuario_tutor, b.nu_seq_bolsa
                            ) t  on t.nu_seq_usuario_tutor = usu.nu_seq_usuario AND t.nu_seq_bolsa = bls.nu_seq_bolsa";
        $query .= " WHERE BLS.NU_SEQ_BOLSA = $id";

        $obModelo = new Fnde_Sice_Model_Bolsa();
        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetch();
        return $result;
    }

    /**
     * Recupera o valor da bolsa pelo identificado de bolsa
     * 
     * @param int $idBolsa
     */
//	public function getValorBolsaById( $seqUf, $seqPerfil, $periodo, $idBolsa ) {
    public function getValorBolsaById($nu_seq_tipo_perfil, $qt_turma, $nu_seq_bolsa) {

        $obModelo = new Fnde_Sice_Model_Bolsa();

        $query = $obModelo->retornaQueryPesquisaValorBolsa($nu_seq_tipo_perfil, $qt_turma, $nu_seq_bolsa);

        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetch();

        return $result['VL_BOLSA'];
    }

    /**
     * Recupera o cnpj da entidade da bolsa através do sigla da UF
     * @param string $sgUf
     */
    public function getCnpjEntidade($sgUf) {

        $query = " SELECT ENT.NU_CGC_ENTIDADE ";
        $query .= " FROM CORP_FNDE.S_SEDUC SED, ";
        $query .= " CORP_FNDE.S_ENTIDADE ENT ";
        $query .= " WHERE SED.NU_SEQ_ENTIDADE = ENT.NU_SEQ_ENTIDADE ";
        $query .= " AND ENT.SG_UF = '$sgUf' 
					AND SED.CO_ST_ENTIDADE = '01'";

        $obModelo = new Fnde_Sice_Model_Bolsa();
        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetch();
        return $result['NU_CGC_ENTIDADE'];
    }

    public function isJustificado($arBolsa) {
        $result = $this->getBolsaById($arBolsa['NU_SEQ_BOLSA']);

        if (!$result['NU_SEQ_JUSTIF_INAPTIDAO'] && $arBolsa['ST_APTIDAO'] == "I") {
            return false;
        } else {
            return true;
        }
    }

    public function isBolsaAntiga($idPeriodoPesquisa) {
	/*
	SGD 29250
	alteração da regra para que se pague bolsas a partir de 01/07/2013
	*/
	
        $query = " SELECT 1 ANTIGA
					FROM SICE_FNDE.S_PERIODO_VINCULACAO
					WHERE NU_SEQ_PERIODO_VINCULACAO = $idPeriodoPesquisa
					AND DT_INICIAL < TO_DATE('01/07/2013','DD/MM/YYYY')";

        $obModelo = new Fnde_Sice_Model_Bolsa();
        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetch();

        if ($result['ANTIGA']) {
            return true;
        }

        return false;
    }

}
