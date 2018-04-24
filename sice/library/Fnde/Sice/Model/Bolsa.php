<?php

/**
 * Arquivo de classe de modelo do tipo: database
 * 
 * Gerado automaticamente pelo gerador: ZFnde Model.
 *
 * $Rev::                      $
 * $Date::                     $
 * $Author::                   $
 * 
 * @package Sice
 * @category Model
 * @name Bolsa
 */

/**
 * Classe de Modelo: Fnde_Sice_Model_Bolsa
 * @uses Fnde_Sice_Model_Database_Bolsa
 * @version $Id$
 */
class Fnde_Sice_Model_Bolsa extends Fnde_Sice_Model_Database_Bolsa {

  /**
   * Função para retornar a query de pesquisa de bolsas de tutores para a função gerarBolsas.
   * @author diego.matos
   * @since 07/11/2012
   * @param array $arParams
   */
  public function retornaQueryTutor($arParams) {
  $sgd26371 = "";
      if ($arParams['VL_EXERCICIO'] >= Fnde_Sice_Business_TermoCompromisso::DT_INICIO) {
          $sgd26371 = Fnde_Sice_Business_TermoCompromisso::innerJoinTermo('tur.dt_inicio', 'tur.dt_fim', $arParams['VL_EXERCICIO']);
      }

    //QUERY PARA TUTOR
    $query = "SELECT DISTINCT TUR.NU_SEQ_USUARIO_TUTOR AS NU_SEQ_USUARIO,
                TUR.NU_SEQ_TURMA,
                TUR.ST_TURMA,
                TUR.NU_SEQ_PERIODO_VINCULACAO,
                (SELECT COUNT(T.NU_SEQ_USUARIO_TUTOR) QT_NAO_FINALIZADA
                  FROM SICE_FNDE.S_TURMA T
                  WHERE T.NU_SEQ_USUARIO_TUTOR    = TUR.NU_SEQ_USUARIO_TUTOR
                  AND T.NU_SEQ_PERIODO_VINCULACAO = TUR.NU_SEQ_PERIODO_VINCULACAO
                  AND T.ST_TURMA NOT             IN(11, 9)
                ) QT_NAO_FINALIZADA,
                (SELECT COUNT(T.NU_SEQ_USUARIO_TUTOR) QT_FINALIZADA
                  FROM SICE_FNDE.S_TURMA T
                  WHERE T.NU_SEQ_USUARIO_TUTOR    = TUR.NU_SEQ_USUARIO_TUTOR
                  AND T.NU_SEQ_PERIODO_VINCULACAO = TUR.NU_SEQ_PERIODO_VINCULACAO
                  AND T.ST_TURMA                 IN(11, 9)
                ) QT_FINALIZADA,
                TO_CHAR(TUR.DT_FINALIZACAO, 'DD/MM/YYYY') DT_FINALIZACAO,
                TUR.NU_SEQ_CONFIGURACAO
                FROM SICE_FNDE.S_TURMA TUR
                INNER JOIN SICE_FNDE.S_USUARIO USU ON USU.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_TUTOR
                LEFT JOIN SICE_FNDE.S_BOLSA BOL ON BOL.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_TUTOR
                  AND BOL.NU_SEQ_PERIODO_VINCULACAO = TUR.NU_SEQ_PERIODO_VINCULACAO

                ". $sgd26371 ."

            WHERE
                  BOL.NU_SEQ_BOLSA IS NULL
                  AND USU.SG_UF_ATUACAO_PERFIL = '" . $arParams['SG_UF'] . "'
                  AND TUR.NU_SEQ_PERIODO_VINCULACAO = " . $arParams['NU_SEQ_PERIODO_VINCULACAO'];

    if ($arParams['CO_MESORREGIAO']) {
      $query .= "     AND USU.CO_MESORREGIAO = " . $arParams['CO_MESORREGIAO'];
    }

    return $query;
  }

  /**
   * Função para montar a query de articulador para a função gerarBolsas
   * @author diego.matos
   * @since 07/11/2012
   * @param array $arParams
   */
  public function retornaQueryArticulador($arParams) {
    //QUERY PARA TRAZER INFORMAÇÕES DE ARTICULADOR
    $queryArticulador = "SELECT DISTINCT TUR.NU_SEQ_USUARIO_ARTICULADOR AS NU_SEQ_USUARIO,
                            USU.NU_SEQ_TIPO_PERFIL,
                            TUR.NU_SEQ_TURMA,
                            TUR.ST_TURMA,
                            TUR.NU_SEQ_PERIODO_VINCULACAO,
                            (SELECT COUNT(T.NU_SEQ_USUARIO_ARTICULADOR) QT_NAO_FINALIZADA
                              FROM SICE_FNDE.S_TURMA T
                              WHERE T.NU_SEQ_USUARIO_ARTICULADOR    = TUR.NU_SEQ_USUARIO_ARTICULADOR
                              AND T.NU_SEQ_PERIODO_VINCULACAO = TUR.NU_SEQ_PERIODO_VINCULACAO
                              AND T.ST_TURMA NOT             IN(11, 9)
                            ) QT_NAO_FINALIZADA,
                            (SELECT COUNT(T.NU_SEQ_USUARIO_ARTICULADOR) QT_FINALIZADA
                              FROM SICE_FNDE.S_TURMA T
                              WHERE T.NU_SEQ_USUARIO_ARTICULADOR    = TUR.NU_SEQ_USUARIO_ARTICULADOR
                              AND T.NU_SEQ_PERIODO_VINCULACAO = TUR.NU_SEQ_PERIODO_VINCULACAO
                              AND T.ST_TURMA                 IN(11, 9)
                            ) QT_FINALIZADA,
                            TO_CHAR(TUR.DT_FINALIZACAO, 'DD/MM/YYYY') DT_FINALIZACAO,
                            TUR.NU_SEQ_CONFIGURACAO
                            FROM SICE_FNDE.S_TURMA TUR
                            INNER JOIN SICE_FNDE.S_USUARIO USU ON USU.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_ARTICULADOR
                            LEFT JOIN SICE_FNDE.S_BOLSA BOL ON BOL.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_ARTICULADOR
                              AND BOL.NU_SEQ_PERIODO_VINCULACAO = TUR.NU_SEQ_PERIODO_VINCULACAO

                            /* SGD 26371 */
                            ". Fnde_Sice_Business_TermoCompromisso::innerJoinTermo('tur.dt_inicio', 'tur.dt_fim', $arParams['VL_EXERCICIO']) ."
                    WHERE
                                BOL.NU_SEQ_BOLSA IS NULL AND
                                USU.SG_UF_ATUACAO_PERFIL = '" . $arParams['SG_UF'] . "' AND
                                TUR.NU_SEQ_PERIODO_VINCULACAO = " . $arParams['NU_SEQ_PERIODO_VINCULACAO'];

    if ($arParams['CO_MESORREGIAO']) {
      $queryArticulador .= " AND USU.CO_MESORREGIAO = " . $arParams['CO_MESORREGIAO'];
    }

    //die($queryArticulador);
    return $queryArticulador;
  }

  /**
   * Função para montar a query de pesquisa de bolsas de coordenador executivo estadual para a função gerarBolsas
   * @author diego.matos
   * @since 07/11/2012
   * @param array $arParams
   */
  public function retornaQueryCoordenadorExecutivoEstadual($arParams) {
    //QUERY PARA TRAZER RESULTADOS RELACIONADOS À COORDENADOR EXECUTIVO ESTADUAL
    $queryCoordEst = "SELECT HIP.NU_SEQ_USUARIO,
                      NULL NU_SEQ_TURMA,
                      NULL ST_TURMA,
                      USU.NU_SEQ_TIPO_PERFIL,
                      PVI.NU_SEQ_PERIODO_VINCULACAO,
                      0 QT_NAO_FINALIZADA,
                      NULL QT_FINALIZADA,
                      TO_CHAR((SELECT P2.DT_FINAL
                        FROM SICE_FNDE.S_PERIODO_VINCULACAO P2
                        WHERE P2.NU_SEQ_PERIODO_VINCULACAO = " . $arParams['NU_SEQ_PERIODO_VINCULACAO'] . "
                      ), 'DD/MM/YYYY') DT_FINALIZACAO,
                      (SELECT NU_SEQ_CONFIGURACAO FROM (
                        SELECT CFG.NU_SEQ_CONFIGURACAO, CFG.DT_TERMINO_VIGENCIA
                        FROM SICE_FNDE.S_CONFIGURACAO CFG 
                        JOIN SICE_FNDE.S_PERIODO_VINCULACAO PER
                        ON PER.NU_SEQ_PERIODO_VINCULACAO = " . $arParams['NU_SEQ_PERIODO_VINCULACAO'] . "
                        WHERE CFG.DT_INI_VIGENCIA <= PER.DT_INICIAL AND CFG.DT_INI_VIGENCIA <= PER.DT_FINAL 
                        AND NVL(CFG.DT_TERMINO_VIGENCIA, SYSDATE + 1) >= PER.DT_INICIAL AND NVL(CFG.DT_TERMINO_VIGENCIA, SYSDATE + 1) >= PER.DT_FINAL
                        ORDER BY NVL(CFG.DT_TERMINO_VIGENCIA, PER.DT_FINAL + 1) DESC
                      )WHERE ROWNUM = 1) NU_SEQ_CONFIGURACAO
                    FROM SICE_FNDE.H_PERFIL_USUARIO HIP
                    INNER JOIN SICE_FNDE.S_USUARIO USU ON HIP.NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO
                    INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO PVI ON PVI.NU_SEQ_PERIODO_VINCULACAO IN
                      (SELECT P.NU_SEQ_PERIODO_VINCULACAO
                        FROM SICE_FNDE.S_PERIODO_VINCULACAO P
                        WHERE P.VL_EXERCICIO             = '" . $arParams['VL_EXERCICIO'] . "'
                        AND P.NU_SEQ_TIPO_PERFIL         = 6
                        AND P.DT_INICIAL <= (SELECT P2.DT_INICIAL FROM SICE_FNDE.S_PERIODO_VINCULACAO P2 WHERE P2.NU_SEQ_PERIODO_VINCULACAO = " . $arParams['NU_SEQ_PERIODO_VINCULACAO'] . ")
                      )
                    LEFT JOIN SICE_FNDE.S_BOLSA BOL ON HIP.NU_SEQ_USUARIO = BOL.NU_SEQ_USUARIO AND PVI.NU_SEQ_PERIODO_VINCULACAO = BOL.NU_SEQ_PERIODO_VINCULACAO

                    /* SGD 26371 */
                    ". Fnde_Sice_Business_TermoCompromisso::innerJoinTermo('pvi.dt_inicial', 'pvi.dt_final', $arParams['VL_EXERCICIO']) ."

                    WHERE HIP.NU_SEQ_TIPO_PERFIL = 8 and PVI.NU_SEQ_PERIODO_VINCULACAO = " . $arParams['NU_SEQ_PERIODO_VINCULACAO'] . "
                    AND (HIP.DT_INICIO <= PVI.DT_FINAL AND NVL(HIP.DT_FIM, PVI.DT_FINAL + 1) > PVI.DT_FINAL)
                    AND BOL.NU_SEQ_BOLSA IS NULL
                    AND USU.SG_UF_ATUACAO_PERFIL = '" . $arParams['SG_UF'] . "'";


    if ($arParams['CO_MESORREGIAO']) {
      $queryCoordEst .= "  AND USU.CO_MESORREGIAO = " . $arParams['CO_MESORREGIAO'];
    }

    if ($arParams['NU_SEQ_TIPO_PERFIL']) {
      $queryCoordEst .= "  AND USU.NU_SEQ_TIPO_PERFIL = " . $arParams['NU_SEQ_TIPO_PERFIL'];
    }

    //die($queryCoordEst);
    return $queryCoordEst;
  }

  /**
   * Função para retornar parte da consulta fazendo a verificação da região selecionada pelo usuário
   * @author poliane.silva
   * @since 13/11/2012
   * @param array $arParams
   */
  public function retornaParametroSgRegiao($arParams) {

    if ($arParams['SG_REGIAO'] == 'T') {
      return "     AND SUF.SG_REGIAO IN ('N','SE','CO','NE','S') ";
    } else {
      return "     AND SUF.SG_REGIAO = '" . $arParams['SG_REGIAO'] . "'";
    }
  }

  /**
   * Função para montar a query de pesquisa das bolsas
   * @author poliane.silva
   * @since 13/11/2012
   * @param array $arParams
   * @return string
   */
  public function retornaQueryPesquisaBolsaAntiga($arParams) {
    $query = " SELECT " . "   SG_UF_ATUACAO_PERFIL || '-' || NU_SEQ_TIPO_PERFIL AS IDENTIFICADOR_LINHA, "
            . "   SG_UF_ATUACAO_PERFIL, " . "   NO_MESO_REGIAO, " . "   DS_TIPO_PERFIL, "
            . "   DS_SITUACAO_BOLSA, " . "  SUM(QT_BOLSA) AS QT_BOLSA, " . "  SUM(VL_BOLSA) AS VL_BOLSA, "
            . "   NU_SEQ_TIPO_PERFIL " . "  FROM ( " . "WITH FILTRO AS ( " . "  SELECT " . "    VCP1.*, "
            . "     VBP1.QT_TURMA " . "   FROM " . "    SICE_FNDE.S_VINCULA_CONF_PERFIL VCP1, "
            . "       SICE_FNDE.S_VALOR_BOLSA_PERFIL VBP1 " . "   WHERE "
            . "     VCP1.NU_SEQ_VINC_CONF_PERF = VBP1.NU_SEQ_VINC_CONF_PERF )" . " SELECT "
            . "   BLS.NU_SEQ_BOLSA," . "     USU.SG_UF_ATUACAO_PERFIL, " . "     MSR.NO_MESO_REGIAO, "
            . "     TPF.DS_TIPO_PERFIL, " . "     SBL.DS_SITUACAO_BOLSA, " . "     VCP.QT_BOLSA_PERIODO QT_BOLSA, "
            . "     MAX(VBP.VL_BOLSA) VL_BOLSA,  " . "     USU.NU_SEQ_TIPO_PERFIL " . " FROM "
            . "     SICE_FNDE.S_BOLSA                      BLS "
            . "     INNER JOIN SICE_FNDE.S_USUARIO             USU  ON BLS.NU_SEQ_USUARIO             = USU.NU_SEQ_USUARIO "
            . "     INNER JOIN SICE_FNDE.S_SITUACAO_BOLSA      SBL  ON BLS.ST_BOLSA                 = SBL.NU_SEQ_SITUACAO_BOLSA "
            . "     INNER JOIN SICE_FNDE.S_TIPO_PERFIL         TPF  ON USU.NU_SEQ_TIPO_PERFIL       = TPF.NU_SEQ_TIPO_PERFIL "
            . "     INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO  PVC  ON BLS.NU_SEQ_PERIODO_VINCULACAO    = PVC.NU_SEQ_PERIODO_VINCULACAO "
            . "   INNER JOIN (SELECT COUNT(1) AS QT_TURMAS, AVT.NU_SEQ_BOLSA, TUR.NU_SEQ_CONFIGURACAO FROM SICE_FNDE.S_AVALIACAO_TURMA AVT "
            . "           INNER JOIN SICE_FNDE.S_TURMA TUR ON AVT.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA "
            . "           GROUP BY AVT.NU_SEQ_BOLSA, TUR.NU_SEQ_CONFIGURACAO) TUR ON TUR.NU_SEQ_BOLSA = BLS.NU_SEQ_BOLSA"
            . "     INNER JOIN SICE_FNDE.S_CONFIGURACAO        CFG  ON TUR.NU_SEQ_CONFIGURACAO      = CFG.NU_SEQ_CONFIGURACAO "
            . "     INNER JOIN CTE_FNDE.T_MESO_REGIAO          MSR  ON USU.CO_MUNICIPIO_PERFIL      = MSR.CO_MUNICIPIO_IBGE "
            . "     INNER JOIN CORP_FNDE.S_UF                  SUF  ON USU.SG_UF_ATUACAO_PERFIL     = SUF.SG_UF "
            . "     INNER JOIN CORP_FNDE.S_REGIAO              RGO  ON SUF.SG_REGIAO                = RGO.SG_REGIAO "
            . "     INNER JOIN SICE_FNDE.S_VINCULA_CONF_PERFIL VCP  ON CFG.NU_SEQ_CONFIGURACAO      = VCP.NU_SEQ_CONFIGURACAO AND TPF.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL "
            . "     INNER JOIN SICE_FNDE.S_VALOR_BOLSA_PERFIL  VBP  ON VCP.NU_SEQ_VINC_CONF_PERF    = VBP.NU_SEQ_VINC_CONF_PERF "
            . " WHERE 1=1 " . "     AND (PVC.DT_INICIAL >= TO_DATE('" . $arParams['DT_INICIAL']
            . "', 'DD/MM/YYYY')" . "     AND PVC.DT_FINAL <= TO_DATE('" . $arParams['DT_FINAL'] . "','DD/MM/YYYY')"
            . ") " . "     AND BLS.ST_BOLSA = '" . $arParams['ST_BOLSA'] . "'";

    $query .= $this->retornaParametroSgRegiao($arParams);

    if ($arParams['SG_UF']) {
      $query .= "     AND USU.SG_UF_ATUACAO_PERFIL = '" . $arParams['SG_UF'] . "'";
    }
    if ($arParams['CO_MESORREGIAO']) {
      $query .= "     AND USU.CO_MESORREGIAO = " . $arParams['CO_MESORREGIAO'];
    }

    $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
    $perfisUsuarioLogado = $usuarioLogado->credentials;

    if ($arParams['NU_SEQ_TIPO_PERFIL']) {
      $query .= "     AND USU.NU_SEQ_TIPO_PERFIL = " . $arParams['NU_SEQ_TIPO_PERFIL'];
    } else {
      if (in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado)) {
        $query .= " AND USU.NU_SEQ_TIPO_PERFIL IN ('4','8','5','6')";
      } else if (in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado)) {
        $query .= " AND USU.NU_SEQ_TIPO_PERFIL IN ('5','6')";
      } else if (in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado)) {
        $query .= " AND USU.NU_SEQ_TIPO_PERFIL = '6' ";
      }
    }

    $query .= " AND VCP.NU_SEQ_VINC_CONF_PERF IN (" . " SELECT F.NU_SEQ_VINC_CONF_PERF " . " FROM FILTRO F "
            . " WHERE F.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO "
            . " AND F.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL " . " AND F.QT_TURMA = "
            . " (SELECT MAX(qt_turma) " . "   FROM FILTRO F2 "
            . "   WHERE F2.NU_SEQ_TIPO_PERFIL = F.NU_SEQ_TIPO_PERFIL "
            . "   AND F2.NU_SEQ_CONFIGURACAO = F.NU_SEQ_CONFIGURACAO "
            . "   AND F2.qt_turma <= TUR.QT_TURMAS ) " . "    ) "
            . " GROUP BY BLS.NU_SEQ_BOLSA, USU.SG_UF_ATUACAO_PERFIL, MSR.NO_MESO_REGIAO, TPF.DS_TIPO_PERFIL,"
            . " SBL.DS_SITUACAO_BOLSA, VCP.QT_BOLSA_PERIODO, USU.NU_SEQ_TIPO_PERFIL" . " ) " . " GROUP BY"
            . "   SG_UF_ATUACAO_PERFIL, " . "   NU_SEQ_TIPO_PERFIL, " . "   NO_MESO_REGIAO, "
            . "   DS_TIPO_PERFIL, " . "   DS_SITUACAO_BOLSA ";

    return $query;
  }

  /**
   * Função para montar a query de pesquisa das bolsas
   * @author poliane.silva
   * @since 14/04/2014
   * @param array $arParams
   * @return string
   */
  public function retornaQueryPesquisaBolsa($arParams) {

    $query = "SELECT USU.SG_UF_ATUACAO_PERFIL || '-' || HIP.NU_SEQ_TIPO_PERFIL || '-' || BOL.NU_SEQ_PERIODO_VINCULACAO AS IDENTIFICADOR_LINHA ,
                USU.SG_UF_ATUACAO_PERFIL,
                MSR.NO_MESO_REGIAO,
                PER.DS_TIPO_PERFIL,
                SBO.DS_SITUACAO_BOLSA,
                TO_CHAR(PVB.DT_INICIAL, 'DD/MM/YYYY') || ' à ' || TO_CHAR(PVB.DT_FINAL, 'DD/MM/YYYY') DT_PERIODO,
                COUNT(BOL.NU_SEQ_BOLSA) QTD_BOLSA,
                SUM(VBP.VL_BOLSA) VL_BOLSA,
                BOL.NU_SEQ_PERIODO_VINCULACAO
                FROM SICE_FNDE.S_BOLSA BOL 
                INNER JOIN SICE_FNDE.H_PERFIL_USUARIO HIP ON BOL.NU_SEQ_USUARIO = HIP.NU_SEQ_USUARIO
                INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO PVB ON BOL.NU_SEQ_PERIODO_VINCULACAO = PVB.NU_SEQ_PERIODO_VINCULACAO
                    --dt inicio da bolsa deve ser maior que a dt inicio do perfil
                    -- A regra não se aplica a coordenador executivo federal
                    AND (HIP.NU_SEQ_TIPO_PERFIL = 8 OR pvb.dt_inicial > HIP.dt_inicio)
                    --dt inicio da bolsa deve ser menor que a data fim do perfil
                    and pvb.dt_inicial < nvl(HIP.dt_fim, sysdate+1)
                INNER JOIN SICE_FNDE.S_VINCULA_CONF_PERFIL VCP ON BOL.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO AND HIP.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL
                INNER JOIN SICE_FNDE.S_VALOR_BOLSA_PERFIL VBP ON VCP.NU_SEQ_VINC_CONF_PERF = VBP.NU_SEQ_VINC_CONF_PERF AND VBP.QT_TURMA = 
                (SELECT DECODE(COUNT(T.NU_SEQ_USUARIO_TUTOR),0,1,COUNT(T.NU_SEQ_USUARIO_TUTOR)) QT_FINALIZADA
                  FROM SICE_FNDE.S_TURMA T
                  WHERE T.NU_SEQ_USUARIO_TUTOR    = HIP.NU_SEQ_USUARIO
                  AND T.NU_SEQ_PERIODO_VINCULACAO = BOL.NU_SEQ_PERIODO_VINCULACAO
                  AND T.ST_TURMA = 11)
                INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO PVI ON PVI.NU_SEQ_PERIODO_VINCULACAO = " . $arParams['NU_SEQ_PERIODO_VINCULACAO'] . "
                INNER JOIN SICE_FNDE.S_USUARIO USU ON HIP.NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO
                INNER JOIN CTE_FNDE.T_MESO_REGIAO MSR ON USU.CO_MUNICIPIO_PERFIL = MSR.CO_MUNICIPIO_IBGE
                INNER JOIN CORP_FNDE.S_UF SUF ON USU.SG_UF_ATUACAO_PERFIL = SUF.SG_UF
                INNER JOIN CORP_FNDE.S_REGIAO RGO ON SUF.SG_REGIAO = RGO.SG_REGIAO
                INNER JOIN SICE_FNDE.S_TIPO_PERFIL PER ON HIP.NU_SEQ_TIPO_PERFIL = PER.NU_SEQ_TIPO_PERFIL
                INNER JOIN SICE_FNDE.S_SITUACAO_BOLSA SBO ON BOL.ST_BOLSA = SBO.NU_SEQ_SITUACAO_BOLSA
                WHERE
                --(HIP.DT_INICIO <= PVB.DT_FINAL AND NVL(HIP.DT_FIM, PVB.DT_FINAL + 1) > PVB.DT_FINAL) AND
                ((BOL.DT_FINALIZACAO_TURMA >= PVI.DT_INICIAL AND BOL.DT_FINALIZACAO_TURMA <= PVI.DT_FINAL) OR (BOL.NU_SEQ_PERIODO_VINCULACAO = PVI.NU_SEQ_PERIODO_VINCULACAO))
                AND USU.SG_UF_ATUACAO_PERFIL = '" . $arParams['SG_UF'] . "'
                AND BOL.ST_BOLSA = " . $arParams['ST_BOLSA'] . "
                -- Nao mostrar bolsas inaptas
                AND (BOL.ST_APTIDAO <> 'I' OR BOL.ST_APTIDAO IS NULL) ";

    if ($arParams['CO_MESORREGIAO']) {
      $query .= "   AND USU.CO_MESORREGIAO = " . $arParams['CO_MESORREGIAO'];
    }

    $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
    $perfisUsuarioLogado = $usuarioLogado->credentials;

    if ($arParams['NU_SEQ_TIPO_PERFIL']) {
      $query .= "   AND HIP.NU_SEQ_TIPO_PERFIL = " . $arParams['NU_SEQ_TIPO_PERFIL'];
    } else {
      if (in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado)) {
        $query .= " AND (HIP.NU_SEQ_TIPO_PERFIL = 8 OR USU.NU_SEQ_TIPO_PERFIL IN (5,6))";
      } else if (in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado)) {
        $query .= " AND USU.NU_SEQ_TIPO_PERFIL IN (5,6)";
      } else if (in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado)) {
        $query .= " AND USU.NU_SEQ_TIPO_PERFIL = 6";
      }
    }

    $query .= " GROUP BY  USU.SG_UF_ATUACAO_PERFIL,
                MSR.NO_MESO_REGIAO,
                PER.DS_TIPO_PERFIL,
                SBO.DS_SITUACAO_BOLSA,
                TO_CHAR(PVB.DT_INICIAL, 'DD/MM/YYYY') || ' à ' || TO_CHAR(PVB.DT_FINAL, 'DD/MM/YYYY'),
                USU.SG_UF_ATUACAO_PERFIL || '-' || HIP.NU_SEQ_TIPO_PERFIL,
                BOL.NU_SEQ_PERIODO_VINCULACAO,
                PVB.DT_INICIAL
              ORDER BY PVB.DT_INICIAL";
    return $query;
  }

  /**
   * Função para montar a query de pesquisa de avaliação das bolsas
   * @author poliane.silva
   * @since 13/11/2012
   * @param string $seqUfs
   * @param string $seqPerfis
   * @return string
   */
  public function retornaQueryPesquisarBolsasAvaliacao($strPerfis, $strPeriodo, $params) {
    $seqUf = $params['UF_BOLSISTAS'];
    $idSituacaoBolsa = $params['SITUACAO_BOLSISTAS'];
    $histBolsa = false;
    if ($idSituacaoBolsa == 9 || $idSituacaoBolsa == 3) {
      $histBolsa = true;
    }
    $query = "SELECT PER.DS_TIPO_PERFIL,
                USU.SG_UF_ATUACAO_PERFIL,
                USU.NO_USUARIO,
                USU.NU_CPF,
                SUM(VBP.VL_BOLSA) VL_BOLSA,
                SBO.DS_SITUACAO_BOLSA,
                COUNT(BOL.NU_SEQ_BOLSA) QTD_BOLSA,
                BOL.NU_SEQ_PERIODO_VINCULACAO,
                BOL.NU_SEQ_BOLSA,
                HIP.NU_SEQ_TIPO_PERFIL,
                (SELECT COUNT(AVT.NU_SEQ_AVALIACAO_TURMA) QT_TURMAS_AVALIADAS
                FROM SICE_FNDE.S_AVALIACAO_TURMA AVT
                WHERE AVT.NU_SEQ_BOLSA    = BOL.NU_SEQ_BOLSA
                ) QT_TOTAL,
                (SELECT COUNT(AVT.NU_SEQ_AVALIACAO_TURMA) QT_TURMAS_AVALIADAS
                FROM SICE_FNDE.S_AVALIACAO_TURMA AVT
                WHERE AVT.ST_APROVACAO IS NOT NULL
                AND AVT.NU_SEQ_BOLSA    = BOL.NU_SEQ_BOLSA
                ) QT_TURMAS_AVALIADAS,
                (SELECT COUNT(AVT.NU_SEQ_AVALIACAO_TURMA) QT_TURMAS_AVALIADAS
                FROM SICE_FNDE.S_AVALIACAO_TURMA AVT
                WHERE AVT.ST_APROVACAO = 'N'
                AND AVT.NU_SEQ_BOLSA    = BOL.NU_SEQ_BOLSA
                ) QT_TURMAS_REPROVADAS,
                UAV.NO_USUARIO NO_USUARIO_AVALIADOR,
                PAV.DS_TIPO_PERFIL DS_TIPO_PERFIL_AVALIADOR " . ($histBolsa ? ",
                HBO.DS_OBSERVACAO " : "") . "
              FROM SICE_FNDE.S_BOLSA BOL
              INNER JOIN SICE_FNDE.H_PERFIL_USUARIO HIP ON BOL.NU_SEQ_USUARIO = HIP.NU_SEQ_USUARIO
              INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO PVB ON BOL.NU_SEQ_PERIODO_VINCULACAO = PVB.NU_SEQ_PERIODO_VINCULACAO
                --dt inicio da bolsa deve ser maior que a dt inicio do perfil
                    -- A regra não se aplica a coordenador executivo federal
                    AND (HIP.NU_SEQ_TIPO_PERFIL = 8 OR pvb.dt_inicial > HIP.dt_inicio)
                  --dt inicio da bolsa deve ser menor que a data fim do perfil
                  and pvb.dt_inicial < nvl(HIP.dt_fim, sysdate+1)
              INNER JOIN SICE_FNDE.S_VINCULA_CONF_PERFIL VCP ON BOL.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO AND HIP.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL
              INNER JOIN SICE_FNDE.S_VALOR_BOLSA_PERFIL VBP ON VCP.NU_SEQ_VINC_CONF_PERF = VBP.NU_SEQ_VINC_CONF_PERF
                AND VBP.QT_TURMA             =
                  (SELECT DECODE(COUNT(T.NU_SEQ_USUARIO_TUTOR),0,1,COUNT(T.NU_SEQ_USUARIO_TUTOR)) QT_FINALIZADA
                  FROM SICE_FNDE.S_TURMA T
                  WHERE T.NU_SEQ_USUARIO_TUTOR    = HIP.NU_SEQ_USUARIO
                  AND T.NU_SEQ_PERIODO_VINCULACAO = BOL.NU_SEQ_PERIODO_VINCULACAO
                  AND T.ST_TURMA                  = 11
                  )
              INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO PVI ON PVI.NU_SEQ_PERIODO_VINCULACAO IN($strPeriodo)
              INNER JOIN SICE_FNDE.S_USUARIO USU ON HIP.NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO
              INNER JOIN CTE_FNDE.T_MESO_REGIAO MSR ON USU.CO_MUNICIPIO_PERFIL = MSR.CO_MUNICIPIO_IBGE
              INNER JOIN CORP_FNDE.S_UF SUF ON USU.SG_UF_ATUACAO_PERFIL = SUF.SG_UF
              INNER JOIN CORP_FNDE.S_REGIAO RGO ON SUF.SG_REGIAO = RGO.SG_REGIAO
              INNER JOIN SICE_FNDE.S_TIPO_PERFIL PER ON HIP.NU_SEQ_TIPO_PERFIL = PER.NU_SEQ_TIPO_PERFIL
              INNER JOIN SICE_FNDE.S_SITUACAO_BOLSA SBO ON BOL.ST_BOLSA = SBO.NU_SEQ_SITUACAO_BOLSA
              LEFT JOIN SICE_FNDE.S_USUARIO UAV ON BOL.NU_SEQ_USUARIO_AVALIADOR = UAV.NU_SEQ_USUARIO
              LEFT JOIN SICE_FNDE.S_TIPO_PERFIL PAV ON UAV.NU_SEQ_TIPO_PERFIL = PAV.NU_SEQ_TIPO_PERFIL " .
              ($histBolsa ? "LEFT JOIN SICE_FNDE.S_HISTORICO_BOLSA HBO ON BOL.NU_SEQ_BOLSA = HBO.NU_SEQ_BOLSA" : "") . "
              WHERE
              --(HIP.DT_INICIO <= PVB.DT_FINAL AND NVL(HIP.DT_FIM, PVB.DT_FINAL + 1) > PVB.DT_FINAL) AND
              BOL.NU_SEQ_PERIODO_VINCULACAO     = PVI.NU_SEQ_PERIODO_VINCULACAO
              /*
              AND HIP.NU_SEQ_USUARIO IN
                (SELECT LSU.NU_SEQ_USUARIO
                FROM SICE_FNDE.H_SITUACAO_USUARIO LSU
                WHERE LSU.ST_USUARIO                  = 'A'
                AND (LSU.DT_INICIO                   <= PVB.DT_FINAL
                AND NVL(LSU.DT_FIM, PVI.DT_FINAL + 1) > PVB.DT_FINAL)
                )
                */
              AND USU.SG_UF_ATUACAO_PERFIL = '$seqUf'
              AND HIP.NU_SEQ_TIPO_PERFIL  IN($strPerfis)
              AND BOL.ST_BOLSA = $idSituacaoBolsa " .
              ($histBolsa ? " AND HBO.NU_SEQ_HISTORICO_BOLSA =
              (SELECT MAX(H.NU_SEQ_HISTORICO_BOLSA) NU_SEQ_HISTORICO_BOLSA
              FROM SICE_FNDE.S_HISTORICO_BOLSA H
              WHERE H.NU_SEQ_BOLSA = HBO.NU_SEQ_BOLSA
              AND H.ST_BOLSA  = $idSituacaoBolsa
              ) " : "") . " AND ( ";

    if (!is_array($params['PERFIL_BOLSISTAS'])) {
      $query .= " (HIP.NU_SEQ_TIPO_PERFIL = " . $params['PERFIL_BOLSISTAS'] . " AND PVB.NU_SEQ_PERIODO_VINCULACAO = " . $params['PERIODO_BOLSISTAS'] . ") ";
    } else {
      $query .= " (HIP.NU_SEQ_TIPO_PERFIL = " . $params['PERFIL_BOLSISTAS'][0] . " AND PVB.NU_SEQ_PERIODO_VINCULACAO = " . $params['PERIODO_BOLSISTAS'][0] . ") ";
      for ($i = 1; $i < count($params['PERFIL_BOLSISTAS']); $i++) {
        $query .= " OR (HIP.NU_SEQ_TIPO_PERFIL = " . $params['PERFIL_BOLSISTAS'][$i] . " AND PVB.NU_SEQ_PERIODO_VINCULACAO = " . $params['PERIODO_BOLSISTAS'][$i] . ") ";
      }
    }

    $query .= ")
        GROUP BY USU.SG_UF_ATUACAO_PERFIL,
          PER.DS_TIPO_PERFIL,
          SBO.DS_SITUACAO_BOLSA,
          BOL.NU_SEQ_PERIODO_VINCULACAO,
          USU.NO_USUARIO,
          USU.NU_CPF,
          HIP.NU_SEQ_TIPO_PERFIL,
          BOL.NU_SEQ_BOLSA,
          UAV.NO_USUARIO,
          PAV.DS_TIPO_PERFIL" . ($histBolsa ? ",
          HBO.DS_OBSERVACAO" : "");
//    die($query);
    return $query;
  }

  /**
   * Função para montar a query de pesquisa de avaliação das bolsas
   * @author poliane.silva
   * @since 13/11/2012
   * @param string $seqUfs
   * @param string $seqPerfis
   * @return string
   */
  public function retornaQueryPesquisarBolsasAvaliacaoAntiga($seqUfs, $seqPerfis, $arPeriodo) {
    $idPeriodoVinc = $arPeriodo['NU_SEQ_PERIODO_VINCULACAO'];
    $idSituacaoBolsa = $arPeriodo['ST_BOLSA'];
    $query = " SELECT NU_SEQ_BOLSA, " . "   DS_TIPO_PERFIL, " . " NU_SEQ_TIPO_PERFIL, "
            . "   SG_UF_ATUACAO_PERFIL, " . "   NO_USUARIO, " . "   NU_CPF, " . "   NO_MESO_REGIAO, "
            . "   DS_SITUACAO_BOLSA, " . "  COUNT (QT_BOLSA_PERIODO)  AS QTD_BOLSA, "
            . "   SUM(VL_BOLSA)             AS VL_BOLSA, " . "  QT_TURMAS_APROVADAS, "
            . "   QT_TURMAS_REPROVADAS, " . "   QT_TURMAS_AVALIADAS, " . "  ST_APTIDAO, "
            . " QT_TURMAS AS QT_TOTAL " . " FROM " . "  ( " . "   WITH FILTRO AS "
            . "   (SELECT VCP1.*, VBP1.QT_TURMA " . "          FROM SICE_FNDE.S_VINCULA_CONF_PERFIL VCP1, "
            . "               SICE_FNDE.S_VALOR_BOLSA_PERFIL  VBP1 "
            . "         WHERE VCP1.NU_SEQ_VINC_CONF_PERF = " . "               VBP1.NU_SEQ_VINC_CONF_PERF) "
            . " SELECT " . "  BLS.NU_SEQ_BOLSA, " . "   TPE.DS_TIPO_PERFIL, " . " USU.NU_SEQ_TIPO_PERFIL, "
            . "   USU.SG_UF_ATUACAO_PERFIL, " . "   USU.NO_USUARIO, " . "   USU.NU_CPF, "
            . "   USU.CO_MUNICIPIO_PERFIL, " . "  SBL.DS_SITUACAO_BOLSA, " . "  VCP.QT_BOLSA_PERIODO, "
            . "   MSR.NO_MESO_REGIAO, " . "   BLS.ST_APTIDAO, " . "   MAX(VBP.VL_BOLSA) VL_BOLSA, "
            . "   QT_TURMAS_APROVADAS, " . "  QT_TURMAS_REPROVADAS, " . "   QT_TURMAS_AVALIADAS, "
            . " QT_TURMAS " . " FROM SICE_FNDE.S_BOLSA BLS "
            . " INNER JOIN SICE_FNDE.S_USUARIO                        USU ON BLS.NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO "
            . " INNER JOIN CTE_FNDE.T_MESO_REGIAO                     MSR ON USU.CO_MUNICIPIO_PERFIL = MSR.CO_MUNICIPIO_IBGE "
            . " INNER JOIN SICE_FNDE.S_TIPO_PERFIL                    TPE ON USU.NU_SEQ_TIPO_PERFIL = TPE.NU_SEQ_TIPO_PERFIL "
            . " INNER JOIN SICE_FNDE.S_SITUACAO_BOLSA                 SBL ON BLS.ST_BOLSA = SBL.NU_SEQ_SITUACAO_BOLSA "
            . " INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO             PVC ON BLS.NU_SEQ_PERIODO_VINCULACAO = PVC.NU_SEQ_PERIODO_VINCULACAO "
            . " INNER JOIN (SELECT  SUM(DECODE (AVT.ST_APROVACAO, 'S', 1, 0)) AS QT_TURMAS_APROVADAS, "
            . "         SUM(DECODE ( AVT.ST_APROVACAO ,'N', 1,0)) AS  QT_TURMAS_REPROVADAS, "
            . "         SUM(NVL2(AVT.ST_APROVACAO, 1,0)) AS  QT_TURMAS_AVALIADAS, "
            . "         COUNT(1) as QT_TURMAS, " . "        NU_SEQ_BOLSA , TUR.NU_SEQ_CONFIGURACAO "
            . "       FROM SICE_FNDE.S_AVALIACAO_TURMA AVT "
            . "       INNER join SICE_FNDE.S_TURMA TUR ON AVT.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA "
            . "       GROUP BY AVT.NU_SEQ_BOLSA, TUR.NU_SEQ_CONFIGURACAO) TUR ON BLS.NU_SEQ_BOLSA = TUR.NU_SEQ_BOLSA  "
            . " INNER JOIN SICE_FNDE.S_CONFIGURACAO                   CFG ON TUR.NU_SEQ_CONFIGURACAO = CFG.NU_SEQ_CONFIGURACAO "
            . " INNER JOIN SICE_FNDE.S_VINCULA_CONF_PERFIL            VCP ON CFG.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO "
            . "                             AND TPE.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL "
            . " INNER JOIN SICE_FNDE.S_VALOR_BOLSA_PERFIL             VBP ON VCP.NU_SEQ_VINC_CONF_PERF = VBP.NU_SEQ_VINC_CONF_PERF "
            . " WHERE USU.NU_SEQ_TIPO_PERFIL IN($seqPerfis)  AND USU.SG_UF_ATUACAO_PERFIL IN ($seqUfs) "
            . " AND PVC.NU_SEQ_PERIODO_VINCULACAO = $idPeriodoVinc " . " AND BLS.ST_BOLSA = $idSituacaoBolsa "
            . " AND VCP.NU_SEQ_VINC_CONF_PERF IN ( " . "       SELECT F.NU_SEQ_VINC_CONF_PERF "
            . "               FROM FILTRO F "
            . "              WHERE F.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO "
            . "                AND F.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL "
            . "                AND F.QT_TURMA = " . "                    (SELECT MAX(QT_TURMA) "
            . "                       FROM FILTRO F2 "
            . "                      WHERE F2.NU_SEQ_TIPO_PERFIL = F.NU_SEQ_TIPO_PERFIL "
            . "                        AND F2.NU_SEQ_CONFIGURACAO = F.NU_SEQ_CONFIGURACAO "
            . "                        AND F2.QT_TURMA <= TUR.QT_TURMAS)) "
            . " GROUP BY BLS.NU_SEQ_BOLSA, TPE.DS_TIPO_PERFIL, USU.NU_SEQ_TIPO_PERFIL,USU.SG_UF_ATUACAO_PERFIL, USU.NO_USUARIO, "
            . " USU.NU_CPF, USU.CO_MUNICIPIO_PERFIL, SBL.DS_SITUACAO_BOLSA, VCP.QT_BOLSA_PERIODO, MSR.NO_MESO_REGIAO, "
            . " BLS.ST_APTIDAO, QT_TURMAS_APROVADAS, QT_TURMAS_REPROVADAS, QT_TURMAS_AVALIADAS, QT_TURMAS ) "
            . " GROUP BY NU_SEQ_BOLSA, " . "  DS_TIPO_PERFIL, " . "   NU_SEQ_TIPO_PERFIL, "
            . "   SG_UF_ATUACAO_PERFIL, " . "   NO_USUARIO, " . "   NU_CPF, " . "   DS_SITUACAO_BOLSA, "
            . "   NO_MESO_REGIAO, " . "   ST_APTIDAO, " . "   QT_TURMAS_APROVADAS, "
            . "   QT_TURMAS_REPROVADAS, " . "   QT_TURMAS_AVALIADAS , QT_TURMAS ";
    //die($query);
    return $query;
  }

  /**
   * Função para montar a query de pesquisa de solicitar homologação
   * @author poliane.silva
   * @since 13/11/2012
   * @param string $seqUfs
   * @param string $seqPerfis
   * @return string
   */
  public function retornaQueryPesquisarBolsasSolicitHomol($seqUfs, $seqPerfis, $periodo) {
    $query = "SELECT NU_SEQ_BOLSA, " . "    DS_TIPO_PERFIL, " . "   SG_UF_ATUACAO_PERFIL, "
            . "   NO_USUARIO,  " . "  NU_CPF, " . "   NO_MESO_REGIAO, " . "   DS_SITUACAO_BOLSA, "
            . "   COUNT (QT_BOLSA_PERIODO)  AS QTD_BOLSA, " . "   SUM(VL_BOLSA)             AS VL_BOLSA, "
            . "   QT_TURMAS_APROVADAS, " . "  QT_TURMAS_REPROVADAS, " . "   QT_TURMAS_AVALIADAS, "
            . "   ST_APTIDAO " . " FROM " . "   ( " . "   WITH FILTRO AS "
            . "   (SELECT VCP1.*, VBP1.QT_TURMA " . "          FROM SICE_FNDE.S_VINCULA_CONF_PERFIL VCP1, "
            . "               SICE_FNDE.S_VALOR_BOLSA_PERFIL  VBP1 "
            . "         WHERE VCP1.NU_SEQ_VINC_CONF_PERF = " . "               VBP1.NU_SEQ_VINC_CONF_PERF) "
            . "   SELECT  " . "     BLS.NU_SEQ_BOLSA, " . "       TPE.DS_TIPO_PERFIL, "
            . "       USU.SG_UF_ATUACAO_PERFIL, " . "       USU.NO_USUARIO, " . "       USU.NU_CPF, "
            . "       USU.CO_MUNICIPIO_PERFIL, " . "      SBL.DS_SITUACAO_BOLSA, "
            . "       VCP.QT_BOLSA_PERIODO, " . "       MSR.NO_MESO_REGIAO, " . "       BLS.ST_APTIDAO, "
            . "       MAX(VBP.VL_BOLSA) VL_BOLSA, " . "       QT_TURMAS_APROVADAS, "
            . "       QT_TURMAS_REPROVADAS, " . "       QT_TURMAS_AVALIADAS " . "   FROM SICE_FNDE.S_BOLSA BLS "
            . "   INNER JOIN SICE_FNDE.S_USUARIO                        USU ON BLS.NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO "
            . "   INNER JOIN CTE_FNDE.T_MESO_REGIAO                     MSR ON USU.CO_MUNICIPIO_PERFIL = MSR.CO_MUNICIPIO_IBGE "
            . "   INNER JOIN SICE_FNDE.S_TIPO_PERFIL                    TPE ON USU.NU_SEQ_TIPO_PERFIL = TPE.NU_SEQ_TIPO_PERFIL "
            . "   INNER JOIN SICE_FNDE.S_SITUACAO_BOLSA                 SBL ON BLS.ST_BOLSA = SBL.NU_SEQ_SITUACAO_BOLSA "
            . "   INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO             PVC ON BLS.NU_SEQ_PERIODO_VINCULACAO = PVC.NU_SEQ_PERIODO_VINCULACAO "
            . "   INNER JOIN (SELECT  SUM(DECODE (AVT.ST_APROVACAO, 'S', 1, 0)) AS QT_TURMAS_APROVADAS, "
            . "           SUM(DECODE ( AVT.ST_APROVACAO ,'N', 1,0)) AS  QT_TURMAS_REPROVADAS, "
            . "                   SUM(NVL2(AVT.ST_APROVACAO, 1,0)) AS  QT_TURMAS_AVALIADAS, "
            . "                   COUNT(1) AS QT_TURMAS, "
            . "                   NU_SEQ_BOLSA , TUR.NU_SEQ_CONFIGURACAO "
            . "               FROM SICE_FNDE.S_AVALIACAO_TURMA AVT "
            . "               INNER JOIN SICE_FNDE.S_TURMA TUR ON AVT.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA  "
            . "               GROUP BY AVT.NU_SEQ_BOLSA, TUR.NU_SEQ_CONFIGURACAO) TUR ON BLS.NU_SEQ_BOLSA = TUR.NU_SEQ_BOLSA  "
            . "   INNER JOIN SICE_FNDE.S_CONFIGURACAO                   CFG ON TUR.NU_SEQ_CONFIGURACAO = CFG.NU_SEQ_CONFIGURACAO "
            . "   INNER JOIN SICE_FNDE.S_VINCULA_CONF_PERFIL            VCP ON CFG.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO "
            . "                                                              AND TPE.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL "
            . "   INNER JOIN SICE_FNDE.S_VALOR_BOLSA_PERFIL             VBP ON VCP.NU_SEQ_VINC_CONF_PERF = VBP.NU_SEQ_VINC_CONF_PERF "
            . "   WHERE USU.NU_SEQ_TIPO_PERFIL IN($seqPerfis)  AND USU.SG_UF_ATUACAO_PERFIL IN ($seqUfs) AND BLS.ST_BOLSA = 5 "
            . "     AND BLS.NU_SEQ_PERIODO_VINCULACAO = $periodo "
            . "   AND VCP.NU_SEQ_VINC_CONF_PERF IN (SELECT F.NU_SEQ_VINC_CONF_PERF " . "        FROM FILTRO F "
            . "       WHERE F.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO "
            . "         AND F.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL " . "         AND F.QT_TURMA = "
            . "             (SELECT MAX(QT_TURMA) " . "                FROM FILTRO F2 "
            . "               WHERE F2.NU_SEQ_TIPO_PERFIL = F.NU_SEQ_TIPO_PERFIL "
            . "                 AND F2.NU_SEQ_CONFIGURACAO = F.NU_SEQ_CONFIGURACAO "
            . "                 AND F2.QT_TURMA <= TUR.QT_TURMAS)" . "    ) GROUP BY BLS.NU_SEQ_BOLSA, "
            . "    TPE.DS_TIPO_PERFIL, " . "    USU.SG_UF_ATUACAO_PERFIL, " . "    USU.NO_USUARIO, "
            . "    USU.NU_CPF, " . "    USU.CO_MUNICIPIO_PERFIL, " . "    SBL.DS_SITUACAO_BOLSA, "
            . "    VCP.QT_BOLSA_PERIODO, " . "    MSR.NO_MESO_REGIAO, " . "    BLS.ST_APTIDAO, "
            . "    QT_TURMAS_APROVADAS, " . "    QT_TURMAS_REPROVADAS, " . "    QT_TURMAS_AVALIADAS) "
            . " GROUP BY NU_SEQ_BOLSA, " . "   DS_TIPO_PERFIL, " . "   SG_UF_ATUACAO_PERFIL, " . "   NO_USUARIO, "
            . "   NU_CPF, " . "   DS_SITUACAO_BOLSA," . "   NO_MESO_REGIAO, " . "   ST_APTIDAO, "
            . "   QT_TURMAS_APROVADAS, " . "   QT_TURMAS_REPROVADAS, " . "   QT_TURMAS_AVALIADAS ";
    //die($query);
    return $query;
  }

  /**
   * Função para montar a query de pesquisa de homologar bolsas
   * @author poliane.silva
   * @since 13/11/2012
   * @param string $seqUfs
   * @param string $seqPerfis
   * @return string
   */
  public function retornaQueryPesquisarBolsasHomologacao($seqUfs, $seqPerfis, $periodo) {

    $query = " SELECT NU_SEQ_BOLSA, " . "   DS_TIPO_PERFIL, " . "   SG_UF_ATUACAO_PERFIL, "
            . "   NO_USUARIO, " . "   NU_CPF, " . "   NO_MESO_REGIAO, " . "   DS_SITUACAO_BOLSA, "
            . "   COUNT (QT_BOLSA_PERIODO)  AS QTD_BOLSA, " . "   SUM(VL_BOLSA)             AS VL_BOLSA, "
            . "   QT_TURMAS_APROVADAS, " . "  QT_TURMAS_REPROVADAS, " . "   QT_TURMAS_AVALIADAS, "
            . "   ST_APTIDAO " . " FROM " . " ( " . "   WITH FILTRO AS " . "    (SELECT VCP1.*, VBP1.QT_TURMA "
            . "       FROM SICE_FNDE.S_VINCULA_CONF_PERFIL VCP1, "
            . "            SICE_FNDE.S_VALOR_BOLSA_PERFIL  VBP1 " . "      WHERE VCP1.NU_SEQ_VINC_CONF_PERF = "
            . "            VBP1.NU_SEQ_VINC_CONF_PERF) " . "  SELECT  " . "     BLS.NU_SEQ_BOLSA, "
            . "     TPE.DS_TIPO_PERFIL, " . "     USU.SG_UF_ATUACAO_PERFIL, " . "     USU.NO_USUARIO, "
            . "     USU.NU_CPF, " . "     USU.CO_MUNICIPIO_PERFIL, " . "    SBL.DS_SITUACAO_BOLSA, "
            . "     VCP.QT_BOLSA_PERIODO, " . "     MSR.NO_MESO_REGIAO, " . "     BLS.ST_APTIDAO, "
            . "     MAX(VBP.VL_BOLSA) VL_BOLSA, " . "     QT_TURMAS_APROVADAS, "
            . "     QT_TURMAS_REPROVADAS, " . "     QT_TURMAS_AVALIADAS " . "   FROM SICE_FNDE.S_BOLSA BLS "
            . "   INNER JOIN SICE_FNDE.S_USUARIO                        USU ON BLS.NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO "
            . "   INNER JOIN CTE_FNDE.T_MESO_REGIAO                     MSR ON USU.CO_MUNICIPIO_PERFIL = MSR.CO_MUNICIPIO_IBGE "
            . "   INNER JOIN SICE_FNDE.S_TIPO_PERFIL                    TPE ON USU.NU_SEQ_TIPO_PERFIL = TPE.NU_SEQ_TIPO_PERFIL "
            . "   INNER JOIN SICE_FNDE.S_SITUACAO_BOLSA                 SBL ON BLS.ST_BOLSA = SBL.NU_SEQ_SITUACAO_BOLSA "
            . "   INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO             PVC ON BLS.NU_SEQ_PERIODO_VINCULACAO = PVC.NU_SEQ_PERIODO_VINCULACAO "
            . "   INNER JOIN (SELECT  SUM(DECODE (AVT.ST_APROVACAO, 'S', 1, 0)) AS QT_TURMAS_APROVADAS, "
            . "                       SUM(DECODE ( AVT.ST_APROVACAO ,'N', 1,0)) AS  QT_TURMAS_REPROVADAS, "
            . "                       SUM(NVL2(AVT.ST_APROVACAO, 1,0)) AS  QT_TURMAS_AVALIADAS, "
            . "                       COUNT(1) AS QT_TURMAS, "
            . "                       NU_SEQ_BOLSA , TUR.NU_SEQ_CONFIGURACAO "
            . "               FROM SICE_FNDE.S_AVALIACAO_TURMA AVT "
            . "             INNER JOIN SICE_FNDE.S_TURMA TUR ON AVT.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA "
            . "             GROUP BY AVT.NU_SEQ_BOLSA, TUR.NU_SEQ_CONFIGURACAO) TUR ON BLS.NU_SEQ_BOLSA = TUR.NU_SEQ_BOLSA "
            . "   INNER JOIN SICE_FNDE.S_CONFIGURACAO                   CFG ON TUR.NU_SEQ_CONFIGURACAO = CFG.NU_SEQ_CONFIGURACAO "
            . "   INNER JOIN SICE_FNDE.S_VINCULA_CONF_PERFIL            VCP ON CFG.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO "
            . "                                                         AND TPE.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL "
            . "   INNER JOIN SICE_FNDE.S_VALOR_BOLSA_PERFIL             VBP ON VCP.NU_SEQ_VINC_CONF_PERF = VBP.NU_SEQ_VINC_CONF_PERF "
            . "   WHERE USU.NU_SEQ_TIPO_PERFIL IN($seqPerfis)  AND USU.SG_UF_ATUACAO_PERFIL IN ($seqUfs) AND BLS.ST_BOLSA = 8 "
            . "     AND BLS.NU_SEQ_PERIODO_VINCULACAO = $periodo " . "  AND VCP.NU_SEQ_VINC_CONF_PERF IN ( "
            . " SELECT F.NU_SEQ_VINC_CONF_PERF " . "        FROM FILTRO F "
            . "       WHERE F.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO "
            . "         AND F.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL " . "         AND F.QT_TURMA = "
            . "           (SELECT MAX(QT_TURMA) " . "             FROM FILTRO F2 "
            . "              WHERE F2.NU_SEQ_TIPO_PERFIL = F.NU_SEQ_TIPO_PERFIL "
            . "              AND F2.NU_SEQ_CONFIGURACAO = F.NU_SEQ_CONFIGURACAO "
            . "              AND F2.QT_TURMA <= TUR.QT_TURMAS ) " . "             ) "
            . " GROUP BY BLS.NU_SEQ_BOLSA, " . "    TPE.DS_TIPO_PERFIL, " . "    USU.SG_UF_ATUACAO_PERFIL, "
            . "    USU.NO_USUARIO, " . "    USU.NU_CPF, " . "    USU.CO_MUNICIPIO_PERFIL, "
            . "    SBL.DS_SITUACAO_BOLSA, " . "    VCP.QT_BOLSA_PERIODO, " . "    MSR.NO_MESO_REGIAO, "
            . "    BLS.ST_APTIDAO, " . "    QT_TURMAS_APROVADAS, " . "    QT_TURMAS_REPROVADAS, "
            . "    QT_TURMAS_AVALIADAS" . " ) " . " GROUP BY NU_SEQ_BOLSA, " . "  DS_TIPO_PERFIL, "
            . "   SG_UF_ATUACAO_PERFIL, " . "   NO_USUARIO, " . "   NU_CPF, " . "   DS_SITUACAO_BOLSA, "
            . "   NO_MESO_REGIAO, " . "   ST_APTIDAO, " . "   QT_TURMAS_APROVADAS, "
            . "   QT_TURMAS_REPROVADAS, " . "   QT_TURMAS_AVALIADAS  ";

    return $query;
  }

  /**
   * Função para montar a query de pesquisa de homologar bolsas
   * @author poliane.silva
   * @since 13/11/2012
   * @param string $seqUfs
   * @param string $seqPerfis
   * @return string
   */
  public function retornaQueryPesquisarBolsasEnviarSgb($seqUfs, $seqPerfis, $periodo, $nuSeqBolsa = null) {
    $query = " SELECT NU_SEQ_BOLSA, " . "   DS_TIPO_PERFIL, " . "   SG_UF_ATUACAO_PERFIL, "
            . "   NO_USUARIO, " . "   NU_CPF, " . "   NO_MESO_REGIAO, " . "   DS_SITUACAO_BOLSA, "
            . "   COUNT (QT_BOLSA_PERIODO)  AS QTD_BOLSA, " . "   SUM(VL_BOLSA)             AS VL_BOLSA, "
            . "   QT_TURMAS_APROVADAS, " . "  QT_TURMAS_REPROVADAS, " . "   QT_TURMAS_AVALIADAS, "
            . "   ST_APTIDAO, " . "   NO_USUARIO_AVALIADOR, " . "   DS_TIPO_PERFIL_AVALIADOR " . " FROM "
            . " ( " . "   WITH FILTRO AS " . "    (SELECT VCP1.*, VBP1.QT_TURMA "
            . "      FROM SICE_FNDE.S_VINCULA_CONF_PERFIL VCP1, "
            . "            SICE_FNDE.S_VALOR_BOLSA_PERFIL  VBP1 " . "      WHERE VCP1.NU_SEQ_VINC_CONF_PERF = "
            . "            VBP1.NU_SEQ_VINC_CONF_PERF) " . "  SELECT  " . "     BLS.NU_SEQ_BOLSA, "
            . "     TPE.DS_TIPO_PERFIL, " . "     USU.SG_UF_ATUACAO_PERFIL, " . "     USU.NO_USUARIO, "
            . "     USU.NU_CPF, " . "     USU.CO_MUNICIPIO_PERFIL, " . "    SBL.DS_SITUACAO_BOLSA, "
            . "     VCP.QT_BOLSA_PERIODO, " . "     MSR.NO_MESO_REGIAO, " . "     BLS.ST_APTIDAO, "
            . "     MAX(VBP.VL_BOLSA) VL_BOLSA, " . "     QT_TURMAS_APROVADAS, "
            . "     QT_TURMAS_REPROVADAS, " . "     QT_TURMAS_AVALIADAS, "
            . "     USUAVA.NO_USUARIO NO_USUARIO_AVALIADOR, "
            . "     TPEAVA.DS_TIPO_PERFIL DS_TIPO_PERFIL_AVALIADOR " . "  FROM SICE_FNDE.S_BOLSA BLS "
            . "   INNER JOIN SICE_FNDE.S_USUARIO                        USU ON BLS.NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO "
            . "   INNER JOIN CTE_FNDE.T_MESO_REGIAO                     MSR ON USU.CO_MUNICIPIO_PERFIL = MSR.CO_MUNICIPIO_IBGE "
            . "   INNER JOIN SICE_FNDE.S_TIPO_PERFIL                    TPE ON USU.NU_SEQ_TIPO_PERFIL = TPE.NU_SEQ_TIPO_PERFIL "
            . "   INNER JOIN SICE_FNDE.S_SITUACAO_BOLSA                 SBL ON BLS.ST_BOLSA = SBL.NU_SEQ_SITUACAO_BOLSA "
            . "   INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO             PVC ON BLS.NU_SEQ_PERIODO_VINCULACAO = PVC.NU_SEQ_PERIODO_VINCULACAO "
            . "   INNER JOIN (SELECT  SUM(DECODE (AVT.ST_APROVACAO, 'S', 1, 0)) AS QT_TURMAS_APROVADAS, "
            . "                       SUM(DECODE ( AVT.ST_APROVACAO ,'N', 1,0)) AS  QT_TURMAS_REPROVADAS, "
            . "                       SUM(NVL2(AVT.ST_APROVACAO, 1,0)) AS  QT_TURMAS_AVALIADAS, "
            . "                       COUNT(1) AS QT_TURMAS, "
            . "                       NU_SEQ_BOLSA , TUR.NU_SEQ_CONFIGURACAO "
            . "         FROM SICE_FNDE.S_AVALIACAO_TURMA AVT "
            . "             INNER join SICE_FNDE.S_TURMA TUR ON AVT.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA  "
            . "             GROUP BY AVT.NU_SEQ_BOLSA, TUR.NU_SEQ_CONFIGURACAO) TUR ON BLS.NU_SEQ_BOLSA = TUR.NU_SEQ_BOLSA "
            . "   INNER JOIN SICE_FNDE.S_CONFIGURACAO                   CFG ON TUR.NU_SEQ_CONFIGURACAO = CFG.NU_SEQ_CONFIGURACAO "
            . "   INNER JOIN SICE_FNDE.S_VINCULA_CONF_PERFIL            VCP ON CFG.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO "
            . "                                                         AND TPE.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL "
            . "   INNER JOIN SICE_FNDE.S_VALOR_BOLSA_PERFIL             VBP ON VCP.NU_SEQ_VINC_CONF_PERF = VBP.NU_SEQ_VINC_CONF_PERF "
            . "   LEFT JOIN SICE_FNDE.S_USUARIO USUAVA ON BLS.NU_SEQ_USUARIO_AVALIADOR = USUAVA.NU_SEQ_USUARIO "
            . "   LEFT JOIN SICE_FNDE.S_TIPO_PERFIL TPEAVA ON USUAVA.NU_SEQ_TIPO_PERFIL = TPEAVA.NU_SEQ_TIPO_PERFIL "
            . "   WHERE USU.NU_SEQ_TIPO_PERFIL IN($seqPerfis)  AND USU.SG_UF_ATUACAO_PERFIL IN ($seqUfs) "
            . "     AND BLS.NU_SEQ_PERIODO_VINCULACAO = $periodo ";

    if (!$nuSeqBolsa) {
      $query .= " AND BLS.ST_BOLSA = 2 ";
    }

    $query .= "   AND VCP.NU_SEQ_VINC_CONF_PERF IN ( " . "    SELECT F.NU_SEQ_VINC_CONF_PERF "
            . "            FROM FILTRO F " . "           WHERE F.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO "
            . "             AND F.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL " . "             AND F.QT_TURMA = "
            . "                 (SELECT MAX(QT_TURMA) " . "                    FROM FILTRO F2 "
            . "                   WHERE F2.NU_SEQ_TIPO_PERFIL = F.NU_SEQ_TIPO_PERFIL "
            . "                     AND F2.NU_SEQ_CONFIGURACAO = F.NU_SEQ_CONFIGURACAO "
            . "                     AND F2.QT_TURMA <= TUR.QT_TURMAS) ) ";

    if ($nuSeqBolsa) {

      $query .= " AND BLS.NU_SEQ_BOLSA = $nuSeqBolsa ";
    }

    $query .= " GROUP BY ( " . " BLS.NU_SEQ_BOLSA, " . " TPE.DS_TIPO_PERFIL, " . " USU.SG_UF_ATUACAO_PERFIL, "
            . " USU.NO_USUARIO, " . " USU.NU_CPF, " . " USU.CO_MUNICIPIO_PERFIL, " . " SBL.DS_SITUACAO_BOLSA, "
            . " VCP.QT_BOLSA_PERIODO, " . " MSR.NO_MESO_REGIAO, " . " BLS.ST_APTIDAO, " . " QT_TURMAS_APROVADAS, "
            . " QT_TURMAS_REPROVADAS, " . " QT_TURMAS_AVALIADAS, " . " USUAVA.NO_USUARIO, "
            . " TPEAVA.DS_TIPO_PERFIL )" . " ) " . " GROUP BY NU_SEQ_BOLSA, " . "   DS_TIPO_PERFIL, "
            . "   SG_UF_ATUACAO_PERFIL, " . "   NO_USUARIO, " . "   NU_CPF, " . "   DS_SITUACAO_BOLSA, "
            . "   NO_MESO_REGIAO, " . "   ST_APTIDAO, " . "   QT_TURMAS_APROVADAS, "
            . "   QT_TURMAS_REPROVADAS, " . "   QT_TURMAS_AVALIADAS, " . "  NO_USUARIO_AVALIADOR, "
            . "   DS_TIPO_PERFIL_AVALIADOR  ";

    return $query;
  }

  public function retornaQueryPesquisarBolsasVerifPend($seqUfs, $seqPerfis, $stBolsa, $periodo) {
    $query = " SELECT NU_SEQ_BOLSA, " . "   DS_TIPO_PERFIL, " . "   SG_UF_ATUACAO_PERFIL, "
            . "   NO_USUARIO, " . "   NU_CPF, " . "   SUM(VL_BOLSA)             AS VL_BOLSA, "
            . "   DS_SITUACAO_BOLSA, " . "  DS_OBSERVACAO " . " FROM " . " ( " . "  WITH FILTRO AS "
            . "     (SELECT VCP1.*, VBP1.QT_TURMA " . "       FROM SICE_FNDE.S_VINCULA_CONF_PERFIL VCP1, "
            . "            SICE_FNDE.S_VALOR_BOLSA_PERFIL  VBP1 " . "      WHERE VCP1.NU_SEQ_VINC_CONF_PERF = "
            . "            VBP1.NU_SEQ_VINC_CONF_PERF) " . "  SELECT " . "    BLS.NU_SEQ_BOLSA, "
            . "     TPE.DS_TIPO_PERFIL, " . "     USU.SG_UF_ATUACAO_PERFIL, " . "     USU.NO_USUARIO, "
            . "     USU.NU_CPF, " . "     USU.CO_MUNICIPIO_PERFIL, " . "    SBL.DS_SITUACAO_BOLSA, "
            . "     VCP.QT_BOLSA_PERIODO, " . "     BLS.ST_APTIDAO, " . "     MAX(VBP.VL_BOLSA) VL_BOLSA, "
            . "     HBO.DS_OBSERVACAO " . "   FROM SICE_FNDE.S_BOLSA BLS "
            . "   INNER JOIN SICE_FNDE.S_USUARIO                        USU ON BLS.NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO "
            . "   INNER JOIN CTE_FNDE.T_MESO_REGIAO                     MSR ON USU.CO_MUNICIPIO_PERFIL = MSR.CO_MUNICIPIO_IBGE "
            . "   INNER JOIN SICE_FNDE.S_TIPO_PERFIL                    TPE ON USU.NU_SEQ_TIPO_PERFIL = TPE.NU_SEQ_TIPO_PERFIL "
            . "   INNER JOIN SICE_FNDE.S_SITUACAO_BOLSA                 SBL ON BLS.ST_BOLSA = SBL.NU_SEQ_SITUACAO_BOLSA "
            . "   INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO             PVC ON BLS.NU_SEQ_PERIODO_VINCULACAO = PVC.NU_SEQ_PERIODO_VINCULACAO "
            . "   INNER JOIN (SELECT  SUM(DECODE (AVT.ST_APROVACAO, 'S', 1, 0)) AS QT_TURMAS_APROVADAS, "
            . "                     SUM(DECODE ( AVT.ST_APROVACAO ,'N', 1,0)) AS  QT_TURMAS_REPROVADAS, "
            . "                     SUM(NVL2(AVT.ST_APROVACAO, 1,0)) AS  QT_TURMAS_AVALIADAS, "
            . "                     COUNT(1) AS QT_TURMAS, "
            . "                     NU_SEQ_BOLSA , TUR.NU_SEQ_CONFIGURACAO "
            . "             FROM SICE_FNDE.S_AVALIACAO_TURMA AVT "
            . "             INNER JOIN SICE_FNDE.S_TURMA TUR ON AVT.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA "
            . "             GROUP BY AVT.NU_SEQ_BOLSA, TUR.NU_SEQ_CONFIGURACAO) TUR ON BLS.NU_SEQ_BOLSA = TUR.NU_SEQ_BOLSA "
            . "   INNER JOIN SICE_FNDE.S_CONFIGURACAO                   CFG ON TUR.NU_SEQ_CONFIGURACAO = CFG.NU_SEQ_CONFIGURACAO "
            . "   INNER JOIN SICE_FNDE.S_VINCULA_CONF_PERFIL            VCP ON CFG.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO "
            . "                                                       AND TPE.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL "
            . "   INNER JOIN SICE_FNDE.S_VALOR_BOLSA_PERFIL             VBP ON VCP.NU_SEQ_VINC_CONF_PERF = VBP.NU_SEQ_VINC_CONF_PERF "
            . "   INNER JOIN SICE_FNDE.S_HISTORICO_BOLSA        HBO ON BLS.NU_SEQ_BOLSA = HBO.NU_SEQ_BOLSA "
            . "   WHERE USU.NU_SEQ_TIPO_PERFIL IN($seqPerfis)  AND USU.SG_UF_ATUACAO_PERFIL IN ($seqUfs) AND BLS.ST_BOLSA IN ($stBolsa) "
            . "     AND BLS.NU_SEQ_PERIODO_VINCULACAO = $periodo " . "  AND HBO.NU_SEQ_HISTORICO_BOLSA = "
            . "     (SELECT MAX(H.NU_SEQ_HISTORICO_BOLSA) NU_SEQ_HISTORICO_BOLSA "
            . "     FROM SICE_FNDE.S_HISTORICO_BOLSA H " . "      WHERE H.NU_SEQ_BOLSA = HBO.NU_SEQ_BOLSA "
            . "     AND H.ST_BOLSA IN ($stBolsa)) " . "   AND VCP.NU_SEQ_VINC_CONF_PERF IN ( "
            . " SELECT F.NU_SEQ_VINC_CONF_PERF " . "    FROM FILTRO F "
            . "   WHERE F.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO "
            . "     AND F.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL " . "     AND F.QT_TURMA = "
            . "       (SELECT MAX(QT_TURMA) " . "         FROM FILTRO F2 "
            . "          WHERE F2.NU_SEQ_TIPO_PERFIL = F.NU_SEQ_TIPO_PERFIL "
            . "          AND F2.NU_SEQ_CONFIGURACAO = F.NU_SEQ_CONFIGURACAO "
            . "          AND F2.QT_TURMA <= TUR.QT_TURMAS )) " . " GROUP BY BLS.NU_SEQ_BOLSA, "
            . " TPE.DS_TIPO_PERFIL, " . " USU.SG_UF_ATUACAO_PERFIL, " . " USU.NO_USUARIO, " . " USU.NU_CPF, "
            . " USU.CO_MUNICIPIO_PERFIL, " . " SBL.DS_SITUACAO_BOLSA, " . " VCP.QT_BOLSA_PERIODO, "
            . " BLS.ST_APTIDAO, " . " HBO.DS_OBSERVACAO" . " ) " . "  GROUP BY  " . "   NU_SEQ_BOLSA, "
            . "   DS_TIPO_PERFIL, " . "   SG_UF_ATUACAO_PERFIL, " . "   NO_USUARIO, " . "   NU_CPF, "
            . "   DS_SITUACAO_BOLSA, " . "  DS_OBSERVACAO ";
    //die($query);
    return $query;
  }

  public function retornaQueryPesquisaValorBolsa($nu_seq_tipo_perfil, $qt_turma, $nu_seq_bolsa) {
    $query = "
      select nvl(vbp.vl_bolsa, 0) as vl_bolsa
                from sice_fnde.s_bolsa bol 
                inner join sice_fnde.h_perfil_usuario hip on bol.nu_seq_usuario = hip.nu_seq_usuario
                inner join sice_fnde.s_periodo_vinculacao pvb on bol.nu_seq_periodo_vinculacao = pvb.nu_seq_periodo_vinculacao
                inner join sice_fnde.s_vincula_conf_perfil vcp on bol.nu_seq_configuracao = vcp.nu_seq_configuracao and hip.nu_seq_tipo_perfil = vcp.nu_seq_tipo_perfil
                inner join sice_fnde.s_valor_bolsa_perfil vbp on vcp.nu_seq_vinc_conf_perf = vbp.nu_seq_vinc_conf_perf 
                where vcp.nu_seq_tipo_perfil = $nu_seq_tipo_perfil and vbp.qt_turma = $qt_turma and bol.nu_seq_bolsa = $nu_seq_bolsa
    ";

    return $query;
  }

    function retornaQuerySituacaoBolsa($param = array()){
        $sql = "
          SELECT
          --B.NU_SEQ_BOLSA,
          RGO.NO_REGIAO,
          MSR.NO_MESO_REGIAO,
          SUF.SG_UF,
          M.NO_MUNICIPIO,
          TO_CHAR(PV.DT_FINAL, 'YYYY') AS NU_ANO,
          TO_CHAR(PV.DT_FINAL, 'MM') AS NU_MES,
          TC.DS_TIPO_CURSO,
          PER.DS_TIPO_PERFIL,
          U.NU_CPF,
          U.NO_USUARIO,
          count(DISTINCT(B.NU_SEQ_BOLSA)) as QT_BOLSAS,
          VBP.VL_BOLSA,
          SB.DS_SITUACAO_BOLSA,
          SB.NU_SEQ_SITUACAO_BOLSA
        FROM SICE_FNDE.S_BOLSA B
        JOIN SICE_FNDE.S_SITUACAO_BOLSA SB ON B.ST_BOLSA = SB.NU_SEQ_SITUACAO_BOLSA
        JOIN SICE_FNDE.S_USUARIO U ON B.NU_SEQ_USUARIO = U.NU_SEQ_USUARIO
        JOIN SICE_FNDE.H_PERFIL_USUARIO HIP ON B.NU_SEQ_USUARIO = HIP.NU_SEQ_USUARIO
        JOIN SICE_FNDE.S_TIPO_PERFIL PER ON HIP.NU_SEQ_TIPO_PERFIL = PER.NU_SEQ_TIPO_PERFIL
        JOIN SICE_FNDE.S_PERIODO_VINCULACAO PV ON PV.NU_SEQ_PERIODO_VINCULACAO = B.NU_SEQ_PERIODO_VINCULACAO
        JOIN SICE_FNDE.S_VINCULA_CONF_PERFIL VCP ON B.NU_SEQ_CONFIGURACAO = VCP.NU_SEQ_CONFIGURACAO AND HIP.NU_SEQ_TIPO_PERFIL = VCP.NU_SEQ_TIPO_PERFIL
        JOIN SICE_FNDE.S_VALOR_BOLSA_PERFIL VBP ON VCP.NU_SEQ_VINC_CONF_PERF = VBP.NU_SEQ_VINC_CONF_PERF AND VBP.QT_TURMA = (
          SELECT COUNT(T.NU_SEQ_USUARIO_TUTOR) QT_FINALIZADA
          FROM SICE_FNDE.S_TURMA T
          WHERE T.NU_SEQ_USUARIO_TUTOR    = HIP.NU_SEQ_USUARIO
          AND T.NU_SEQ_PERIODO_VINCULACAO = B.NU_SEQ_PERIODO_VINCULACAO
          AND T.ST_TURMA = 11
        )
        JOIN CTE_FNDE.T_MESO_REGIAO MSR ON U.CO_MUNICIPIO_PERFIL = MSR.CO_MUNICIPIO_IBGE
        JOIN CORP_FNDE.S_UF SUF ON U.SG_UF_ATUACAO_PERFIL = SUF.SG_UF
        JOIN CORP_FNDE.S_REGIAO RGO ON SUF.SG_REGIAO = RGO.SG_REGIAO
        JOIN CORP_FNDE.S_MUNICIPIO M ON M.CO_MUNICIPIO_IBGE = U.CO_MUNICIPIO_PERFIL
        JOIN SICE_FNDE.S_TURMA T ON T.NU_SEQ_USUARIO_TUTOR = U.NU_SEQ_USUARIO AND T.NU_SEQ_PERIODO_VINCULACAO = B.NU_SEQ_PERIODO_VINCULACAO
        JOIN SICE_FNDE.S_CURSO C ON T.NU_SEQ_CURSO = C.NU_SEQ_CURSO
        JOIN SICE_FNDE.S_TIPO_CURSO TC ON C.NU_SEQ_TIPO_CURSO = TC.NU_SEQ_TIPO_CURSO";

        $bind = array();
        if($param['NU_ANO']){
            $sql .= " AND TO_CHAR(PV.DT_FINAL, 'YYYY') = :NU_ANO ";
            $bind[':NU_ANO'] = $param['NU_ANO'];
        }

        if($param['NU_MES']){
            $sql .= " AND TO_NUMBER(TO_CHAR(PV.DT_FINAL, 'MM')) = :NU_MES ";
            $bind[':NU_MES'] = (int) $param['NU_MES'];
        }

        if($param['UF_TURMA']){
            $sql .= " AND SUF.SG_UF = :UF_TURMA ";
            $bind[':UF_TURMA'] = $param['UF_TURMA'];
        }

        if($param['CO_MESORREGIAO']){
            $sql .= " AND MSR.CO_MESO_REGIAO = :CO_MESORREGIAO ";
            $bind[':CO_MESORREGIAO'] = $param['CO_MESORREGIAO'];
        }

        if($param['CO_MUNICIPIO']){
            $sql .= " AND M.CO_MUNICIPIO_IBGE = :CO_MUNICIPIO ";
            $bind[':CO_MUNICIPIO'] = $param['CO_MUNICIPIO'];
        }

        if($param['NU_CPF']){
            $sql .= " AND U.NU_CPF = :NU_CPF ";
            $bind[':NU_CPF'] = str_replace("-", "", str_replace(".", "", $param['NU_CPF']));
        }

        if($param['NO_CURSISTA']){
            $sql .= " AND U.NO_USUARIO LIKE :NO_CURSISTA ";
            $bind[':NO_CURSISTA'] = '%' . trim($param['NO_CURSISTA']) . '%';
        }

        if($param['NU_SEQ_TIPO_PERFIL']){
            $sql .= " AND HIP.NU_SEQ_TIPO_PERFIL = :NU_SEQ_TIPO_PERFIL ";
            $bind[':NU_SEQ_TIPO_PERFIL'] = $param['NU_SEQ_TIPO_PERFIL'];
        }

        if($param['SG_REGIAO']){
            $sql .= " AND RGO.SG_REGIAO = :SG_REGIAO ";
            $bind[':SG_REGIAO'] = $param['SG_REGIAO'];
        }

        $sql .= " GROUP BY
        --B.NU_SEQ_BOLSA,
        RGO.NO_REGIAO,
        MSR.NO_MESO_REGIAO,
        SUF.SG_UF,
        M.NO_MUNICIPIO,
        TO_CHAR(PV.DT_FINAL, 'YYYY'),
        TO_CHAR(PV.DT_FINAL, 'MM'),
        TC.DS_TIPO_CURSO,
        VBP.VL_BOLSA,
        PER.DS_TIPO_PERFIL,
        U.NU_CPF,
        U.NO_USUARIO,
        SB.DS_SITUACAO_BOLSA,
        SB.NU_SEQ_SITUACAO_BOLSA ";

        $obModelo = new Fnde_Sice_Model_Bolsa();
        $stm = $obModelo->getAdapter()->query($sql, $bind);

        return $stm->fetchAll();
    }
}
