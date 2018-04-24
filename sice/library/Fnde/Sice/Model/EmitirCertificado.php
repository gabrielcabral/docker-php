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
 * @name Curso
 */

/**
 * Classe de Modelo: Fnde_Sice_Model_Curso
 * @uses Fnde_Sice_Model_Database_Curso
 * @version $Id$
 */
class Fnde_Sice_Model_EmitirCertificado extends Fnde_Sice_Model_Database_Curso
{

    public function getDadosResumidosTutor($idTutor)
    {
        $select = $this->select()
            ->distinct()
            ->setIntegrityCheck(false)
            ->from(array('TUR' => 'SICE_FNDE.S_TURMA'), array(
                'USUARIO_CURSO' => "(USU.NU_SEQ_USUARIO || '/' || CUR.NU_SEQ_CURSO)",
                'USU.NU_SEQ_USUARIO',
                'CUR.NU_SEQ_CURSO',
                'USU.SG_UF_ATUACAO_PERFIL',
                'MESO_REG.NO_MUNICIPIO',
                'USU.NO_USUARIO',
                'NU_CPF' => "(
                        SUBSTR(USU.NU_CPF,1,3)||'.'||
					    SUBSTR(USU.NU_CPF,4,3)||'.'||
					    SUBSTR(USU.NU_CPF,7,3)||'-'||
					    SUBSTR(USU.NU_CPF,10,2)
					  )",
                'CUR.DS_NOME_CURSO'
            ))
            ->join(array('USU' => 'SICE_FNDE.S_USUARIO'), 'USU.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_TUTOR', null)
            ->join(array('CUR' => 'SICE_FNDE.S_CURSO'), 'TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO', null)
            ->join(array('MESO_REG' => 'CTE_FNDE.T_MESO_REGIAO'), 'USU.CO_MUNICIPIO_PERFIL = MESO_REG.CO_MUNICIPIO_IBGE', null)
            ->where('USU.NU_SEQ_USUARIO = ?', $idTutor)
            ->order('USU.NO_USUARIO ASC');

        $result = $this->fetchRow($select);

        return $result ? $result->toArray() : array();
    }

    public function getDadosResumidosArticulador($idArticulador)
    {
        $select = $this->select()
            ->distinct()
            ->setIntegrityCheck(false)
            ->from(array('TUR' => 'SICE_FNDE.S_TURMA'), array(
                'USUARIO_CURSO' => "(USU.NU_SEQ_USUARIO || '/' || CUR.NU_SEQ_CURSO)",
                'USU.NU_SEQ_USUARIO',
                'CUR.NU_SEQ_CURSO',
                'USU.SG_UF_ATUACAO_PERFIL',
                'MESO_REG.NO_MUNICIPIO',
                'USU.NO_USUARIO',
                'NU_CPF' => "(
                        SUBSTR(USU.NU_CPF,1,3)||'.'||
					    SUBSTR(USU.NU_CPF,4,3)||'.'||
					    SUBSTR(USU.NU_CPF,7,3)||'-'||
					    SUBSTR(USU.NU_CPF,10,2)
					  )",
                'CUR.DS_NOME_CURSO'
            ))
            ->join(array('USU' => 'SICE_FNDE.S_USUARIO'), 'USU.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_ARTICULADOR', null)
            ->join(array('CUR' => 'SICE_FNDE.S_CURSO'), 'TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO', null)
            ->join(array('MESO_REG' => 'CTE_FNDE.T_MESO_REGIAO'), 'USU.CO_MUNICIPIO_PERFIL = MESO_REG.CO_MUNICIPIO_IBGE', null)
            ->where('USU.NU_SEQ_USUARIO = ?', $idArticulador)
            ->order('USU.NO_USUARIO ASC');

        $result = $this->fetchRow($select);

        return $result ? $result->toArray() : array();
    }

    public function getTurmasTutorCurso($idTutor, $idCurso)
    {
        $select = $this->select()
            ->distinct()
            ->setIntegrityCheck(false)
            ->from(array('TUR' => 'SICE_FNDE.S_TURMA'), array(
                'TUR.NU_SEQ_TURMA',
                'USU.NU_SEQ_USUARIO',
                'NU_SEQ_USUARIO_NU_SEQ_TURMA' => "(USU.NU_SEQ_USUARIO || '/' || TUR.NU_SEQ_TURMA)",
                'CUR.NU_SEQ_CURSO',
                'DT_INICIO' => "TO_CHAR(TUR.DT_INICIO, 'DD/MM/YYYY')",
                'DT_FIM' => "TO_CHAR(TUR.DT_FIM, 'DD/MM/YYYY')",
                'DT_FINALIZACAO' => "TO_CHAR(TUR.DT_FINALIZACAO, 'DD/MM/YYYY')",
            ))
            ->join(array('USU' => 'SICE_FNDE.S_USUARIO'), 'USU.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_TUTOR', null)
            ->join(array('CUR' => 'SICE_FNDE.S_CURSO'), 'TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO', null)
            ->where('USU.NU_SEQ_USUARIO = ?', $idTutor)
            ->where('CUR.NU_SEQ_CURSO = ?', $idCurso)
            ->order('TUR.NU_SEQ_TURMA ASC');

        $result = $this->fetchAll($select);

        return $result->toArray();
    }

    public function getPeriodosArticulador($idArticulador, $idCurso)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('TUR' => 'SICE_FNDE.S_TURMA'), array(
                'USU.NU_SEQ_USUARIO',
                'PER_VINC.NU_SEQ_PERIODO_VINCULACAO',
                'DT_INICIAL' => "TO_CHAR(PER_VINC.DT_INICIAL, 'DD/MM/YYYY')",
                'DT_FINAL' => "TO_CHAR(PER_VINC.DT_FINAL, 'DD/MM/YYYY')",
            ))
            ->join(array('USU' => 'SICE_FNDE.S_USUARIO'), 'USU.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_ARTICULADOR', null)
            ->join(array('CUR' => 'SICE_FNDE.S_CURSO'), 'TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO', null)
            ->join(array('PER_VINC' => 'SICE_FNDE.S_PERIODO_VINCULACAO'),
                'CASE
                  WHEN TUR.DT_FIM > TUR.DT_FINALIZACAO
                    THEN TUR.DT_FIM
                  ELSE
                    TUR.DT_FINALIZACAO
                  END
                  BETWEEN PER_VINC.DT_INICIAL AND PER_VINC.DT_FINAL
                  AND PER_VINC.NU_SEQ_TIPO_PERFIL = 6', null)
            ->where('USU.NU_SEQ_USUARIO = ?', $idArticulador)
            ->where('CUR.NU_SEQ_CURSO = ?', $idCurso)
            ->group(array('USU.NU_SEQ_USUARIO', 'PER_VINC.NU_SEQ_PERIODO_VINCULACAO', 'PER_VINC.DT_INICIAL', 'PER_VINC.DT_FINAL'))
            ->order('PER_VINC.DT_INICIAL DESC');

        $result = $this->fetchAll($select);

        return $result->toArray();
    }

    public function listaCursistas($filtro)
    {

        $select = $this->select()
            ->distinct()
            ->setIntegrityCheck(false)
            ->from(array('CUR_TUR' => "SICE_FNDE.S_VINC_CURSISTA_TURMA"), array(
                'NU_SEQ_USUARIO_NU_SEQ_TURMA' => "(USU.NU_SEQ_USUARIO || '/' || TUR.NU_SEQ_TURMA)",
                'TUR.NU_SEQ_TURMA',
                'CUR.NU_SEQ_CURSO',
                'CUR_TUR.NU_MATRICULA',
                'USU.NU_SEQ_USUARIO',
                'USU.SG_UF_ATUACAO_PERFIL',
                'MESO_REG.NO_MUNICIPIO',
                'USU.NO_USUARIO',
                'NU_CPF' => "(
                        SUBSTR(USU.NU_CPF,1,3)||'.'||
					    SUBSTR(USU.NU_CPF,4,3)||'.'||
					    SUBSTR(USU.NU_CPF,7,3)||'-'||
					    SUBSTR(USU.NU_CPF,10,2)
					  )",
                'CUR.DS_NOME_CURSO',
                'ST_CURSO_AVAL' => new Zend_Db_Expr('(SELECT 1
                                     FROM SICE_FNDE.S_AVALIACAO_CURSO
                                     WHERE NU_SEQ_TURMA = TUR.NU_SEQ_TURMA
                                           AND NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO
				                    )'),
                'CA.DS_SITUACAO',
                'TUR.ST_TURMA',
                'DT_FINALIZACAO' => "TO_CHAR(TUR.DT_FINALIZACAO, 'DD/MM/YYYY')",
            ))
            ->join(array('USU' => 'SICE_FNDE.S_USUARIO'), 'USU.NU_SEQ_USUARIO = CUR_TUR.NU_SEQ_USUARIO_CURSISTA', null)
            ->join(array('TUR' => 'SICE_FNDE.S_TURMA'), 'CUR_TUR.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA', null)
            ->join(array('CUR' => 'SICE_FNDE.S_CURSO'), 'TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO', null)
            ->join(array('CA' => 'SICE_FNDE.S_CRITERIO_AVALIACAO'), ' CUR_TUR.NU_SEQ_CRITERIO_AVAL = CA.NU_SEQ_CRITERIO_AVAL', null)
            ->join(array('MESO_REG' => 'CTE_FNDE.T_MESO_REGIAO'), 'USU.CO_MUNICIPIO_PERFIL = MESO_REG.CO_MUNICIPIO_IBGE', null)
            ->order('USU.NO_USUARIO ASC');

        $this->getFiltro($select, $filtro);

        $result = $this->fetchAll($select);

        return $result->toArray();
    }

    public function listaTutores($filtro)
    {
        $select = $this->select()
            ->distinct()
            ->setIntegrityCheck(false)
            ->from(array('TUR' => 'SICE_FNDE.S_TURMA'), array(
                'USUARIO_CURSO' => "(USU.NU_SEQ_USUARIO || '/' || CUR.NU_SEQ_CURSO)",
                'USU.NU_SEQ_USUARIO',
                'CUR.NU_SEQ_CURSO',
                'USU.SG_UF_ATUACAO_PERFIL',
                'MESO_REG.NO_MUNICIPIO',
                'USU.NO_USUARIO',
                'NU_CPF' => "(
                        SUBSTR(USU.NU_CPF,1,3)||'.'||
					    SUBSTR(USU.NU_CPF,4,3)||'.'||
					    SUBSTR(USU.NU_CPF,7,3)||'-'||
					    SUBSTR(USU.NU_CPF,10,2)
					  )",
                'CUR.DS_NOME_CURSO'
            ))
            ->join(array('USU' => 'SICE_FNDE.S_USUARIO'), 'USU.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_TUTOR', null)
            ->join(array('CUR' => 'SICE_FNDE.S_CURSO'), 'TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO', null)
            ->join(array('MESO_REG' => 'CTE_FNDE.T_MESO_REGIAO'), 'USU.CO_MUNICIPIO_PERFIL = MESO_REG.CO_MUNICIPIO_IBGE', null)
            ->order('USU.NO_USUARIO ASC');

        $this->getFiltro($select, $filtro);

        $result = $this->fetchAll($select);

        return $result->toArray();
    }

    public function listaArticuladores($filtro)
    {
        $select = $this->select()
            ->distinct()
            ->setIntegrityCheck(false)
            ->from(array('TUR' => 'SICE_FNDE.S_TURMA'), array(
                'USUARIO_CURSO' => "(USU.NU_SEQ_USUARIO || '/' || CUR.NU_SEQ_CURSO)",
                'USU.NU_SEQ_USUARIO',
                'CUR.NU_SEQ_CURSO',
                'USU.SG_UF_ATUACAO_PERFIL',
                'MESO_REG.NO_MUNICIPIO',
                'USU.NO_USUARIO',
                'NU_CPF' => "(
                        SUBSTR(USU.NU_CPF,1,3)||'.'||
					    SUBSTR(USU.NU_CPF,4,3)||'.'||
					    SUBSTR(USU.NU_CPF,7,3)||'-'||
					    SUBSTR(USU.NU_CPF,10,2)
					  )",
                'CUR.DS_NOME_CURSO'
            ))
            ->join(array('USU' => 'SICE_FNDE.S_USUARIO'), 'USU.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_ARTICULADOR', null)
            ->join(array('CUR' => 'SICE_FNDE.S_CURSO'), 'TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO', null)
            ->join(array('MESO_REG' => 'CTE_FNDE.T_MESO_REGIAO'), 'USU.CO_MUNICIPIO_PERFIL = MESO_REG.CO_MUNICIPIO_IBGE', null)
            ->order('USU.NO_USUARIO ASC');

        $this->getFiltro($select, $filtro);

        $result = $this->fetchAll($select);

        return $result->toArray();
    }

    private function getFiltro(Zend_Db_Table_Select $select, $filtro)
    {

        if (!empty($filtro['SG_UF'])) {
            $select->where('USU.SG_UF_ATUACAO_PERFIL = ?', $filtro['SG_UF']);
        }
        if (!empty($filtro['CO_MESORREGIAO'])) {
            $select->where('USU.CO_MESORREGIAO = ?', $filtro['CO_MESORREGIAO']);
        }
        if (!empty($filtro['CO_MUNICIPIO'])) {
            $select->where('MESO_REG.CO_MUNICIPIO_IBGE = ?', $filtro['CO_MUNICIPIO']);
        }
        if (!empty($filtro['NO_USUARIO'])) {
            $filtro['NO_USUARIO'] = strtoupper($filtro['NO_USUARIO']);
            $select->where('UPPER(USU.NO_USUARIO) LIKE ?', "%{$filtro['NO_USUARIO']}%");
        }
        if (!empty($filtro['NU_CPF'])) {
            $filtro['NU_CPF'] = preg_replace("/\D/", '', $filtro['NU_CPF']);;

            $select->where('USU.NU_CPF = ?', $filtro['NU_CPF']);
        }
        if (!empty($filtro['NU_SEQ_CURSO'])) {
            $select->where('CUR.NU_SEQ_CURSO = ?', $filtro['NU_SEQ_CURSO']);
        }
        if (!empty($filtro['articulador'])) {
            $select->where('TUR.NU_SEQ_USUARIO_ARTICULADOR = ?', $filtro['articulador']);
        }
        if (!empty($filtro['tutor'])) {
            $select->where('TUR.NU_SEQ_USUARIO_TUTOR = ?', $filtro['tutor']);
        }
        if (!empty($filtro['cursista'])) {
            $select->where('CUR_TUR.NU_SEQ_USUARIO_CURSISTA = ?', $filtro['cursista']);
        }
        if (!empty($filtro['aprovado_ou_aprovado_destaque'])) {
            $select->where("CA.DS_SITUACAO = 'Aprovado' OR CA.DS_SITUACAO = 'Aprovado com destaque'");
        }
        if (!empty($filtro['ST_TURMA'])) {
            $select->where("TUR.ST_TURMA = ?", $filtro['ST_TURMA'] );
        }

    }

    public function dadosParaCursista($usuario, $turma)
    {

        $select = $this->select()
            ->distinct()
            ->setIntegrityCheck(false)
            ->from(array('CUR_TUR' => "SICE_FNDE.S_VINC_CURSISTA_TURMA"), array(
                'NU_SEQ_USUARIO_NU_SEQ_TURMA' => "(USU.NU_SEQ_USUARIO || '/' || TUR.NU_SEQ_TURMA)",
                'CUR_TUR.NU_MATRICULA',

                'TUR.NU_SEQ_TURMA',
                'DT_INICIO' => "(TO_CHAR(TUR.DT_INICIO, 'DD/MM/YYYY'))",
                'DT_FIM' => "(TO_CHAR(TUR.DT_FIM, 'DD/MM/YYYY'))",
                'DT_FINALIZACAO_REAL' => new Zend_Db_Expr("TO_CHAR(CASE WHEN TUR.DT_FIM > TUR.DT_FINALIZACAO THEN TUR.DT_FIM ELSE TUR.DT_FINALIZACAO END, 'DD/MM/YYYY')"),
                'NO_USUARIO_TUTOR' => 'TUTOR.NO_USUARIO',

                'CUR.NU_SEQ_CURSO',
                'CUR.DS_NOME_CURSO',
                'CUR.VL_CARGA_HORARIA',

                'USU.NU_SEQ_USUARIO',
                'USU.SG_UF_ATUACAO_PERFIL',
                'USU.NO_USUARIO',
                'NU_CPF' => "(
                        SUBSTR(USU.NU_CPF,1,3)||'.'||
					    SUBSTR(USU.NU_CPF,4,3)||'.'||
					    SUBSTR(USU.NU_CPF,7,3)||'-'||
					    SUBSTR(USU.NU_CPF,10,2)
					  )",

                'MUNI.NO_MUNICIPIO',
                'MUNI.SG_UF',

                'ST_CURSO_AVAL' => new Zend_Db_Expr('(SELECT 1
                                     FROM SICE_FNDE.S_AVALIACAO_CURSO
                                     WHERE NU_SEQ_TURMA = TUR.NU_SEQ_TURMA
                                           AND NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO
				                    )'),
                'CA.DS_SITUACAO',
                'TUR.ST_TURMA'
            ))
            ->join(array('USU' => 'SICE_FNDE.S_USUARIO'), 'USU.NU_SEQ_USUARIO = CUR_TUR.NU_SEQ_USUARIO_CURSISTA', null)
            ->join(array('TUR' => 'SICE_FNDE.S_TURMA'), 'CUR_TUR.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA', null)
            ->join(array('TUTOR' => 'SICE_FNDE.S_USUARIO'), 'TUTOR.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_TUTOR', null)
            ->join(array('CUR' => 'SICE_FNDE.S_CURSO'), 'TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO', null)
            ->join(array('CA' => 'SICE_FNDE.S_CRITERIO_AVALIACAO'), ' CUR_TUR.NU_SEQ_CRITERIO_AVAL = CA.NU_SEQ_CRITERIO_AVAL', null)
            ->join(array('MUNI' => 'CORP_FNDE.S_MUNICIPIO'), 'CAST(USU.CO_MUNICIPIO_PERFIL AS VARCHAR2(12)) = MUNI.CO_MUNICIPIO_IBGE', null)
            ->where('USU.NU_SEQ_USUARIO = ?', $usuario)
            ->where('TUR.NU_SEQ_TURMA = ?', $turma)
            ->order('USU.NO_USUARIO ASC');

        $result = $this->fetchRow($select);

        return $result ? $result->toArray() : array();
    }

    public function dadosParaTutor($usuario, $turma)
    {

        $select = $this->select()
            ->distinct()
            ->setIntegrityCheck(false)
            ->from(array('TUR' => 'SICE_FNDE.S_TURMA'), array(

                'USU.NU_SEQ_USUARIO',
                'USU.SG_UF_ATUACAO_PERFIL',
                'USU.NO_USUARIO',
                'NU_CPF' => "(
                        SUBSTR(USU.NU_CPF,1,3)||'.'||
					    SUBSTR(USU.NU_CPF,4,3)||'.'||
					    SUBSTR(USU.NU_CPF,7,3)||'-'||
					    SUBSTR(USU.NU_CPF,10,2)
					  )",

                'TUR.NU_SEQ_TURMA',
                'DT_INICIO' => "(TO_CHAR(TUR.DT_INICIO, 'DD/MM/YYYY'))",
                'DT_FIM' => "(TO_CHAR(TUR.DT_FIM, 'DD/MM/YYYY'))",
                'DT_FINALIZACAO' => "(TO_CHAR(TUR.DT_FINALIZACAO, 'DD/MM/YYYY'))",
                'DT_FINALIZACAO_REAL' => new Zend_Db_Expr("TO_CHAR(CASE WHEN TUR.DT_FIM > TUR.DT_FINALIZACAO THEN TUR.DT_FIM ELSE TUR.DT_FINALIZACAO END, 'DD/MM/YYYY')"),

                'CUR.NU_SEQ_CURSO',
                'CUR.DS_NOME_CURSO',
                'CUR.VL_CARGA_HORARIA',

                'MUNI.NO_MUNICIPIO',
                'MUNI.SG_UF',
            ))
            ->join(array('USU' => 'SICE_FNDE.S_USUARIO'), 'USU.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_TUTOR', null)
            ->join(array('CUR' => 'SICE_FNDE.S_CURSO'), 'TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO', null)
            ->join(array('MUNI' => 'CORP_FNDE.S_MUNICIPIO'), 'CAST(USU.CO_MUNICIPIO_PERFIL AS VARCHAR2(12)) = MUNI.CO_MUNICIPIO_IBGE', null)
            ->where('USU.NU_SEQ_USUARIO = ?', $usuario)
            ->where('TUR.NU_SEQ_TURMA = ?', $turma)
            ->order('TUR.DT_INICIO DESC');

        $result = $this->fetchRow($select);

        return $result ? $result->toArray() : array();
    }

    public function dadosParaDeclaracaoArticulador($idArticulador, $idPeriodo)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('TUR' => 'SICE_FNDE.S_TURMA'), array(
                'USU.NU_SEQ_USUARIO',
                'USU.SG_UF_ATUACAO_PERFIL',
                'USU.NO_USUARIO',
                'NU_CPF' => "(
                        SUBSTR(USU.NU_CPF,1,3)||'.'||
					    SUBSTR(USU.NU_CPF,4,3)||'.'||
					    SUBSTR(USU.NU_CPF,7,3)||'-'||
					    SUBSTR(USU.NU_CPF,10,2)
					  )",


                'PER_VINC.NU_SEQ_PERIODO_VINCULACAO',
                'DT_INICIAL' => "TO_CHAR(PER_VINC.DT_INICIAL, 'DD/MM/YYYY')",
                'DT_FINAL' => "TO_CHAR(PER_VINC.DT_FINAL, 'DD/MM/YYYY')",

                'CUR.NU_SEQ_CURSO',
                'CUR.DS_NOME_CURSO',
                'CUR.VL_CARGA_HORARIA',
            ))
            ->join(array('USU' => 'SICE_FNDE.S_USUARIO'), 'USU.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_ARTICULADOR', null)
            ->join(array('CUR' => 'SICE_FNDE.S_CURSO'), 'TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO', null)
            ->join(array('PER_VINC' => 'SICE_FNDE.S_PERIODO_VINCULACAO'),
                'CASE
                  WHEN TUR.DT_FIM > TUR.DT_FINALIZACAO
                    THEN TUR.DT_FIM
                  ELSE
                    TUR.DT_FINALIZACAO
                  END
                  BETWEEN PER_VINC.DT_INICIAL AND PER_VINC.DT_FINAL
                  AND PER_VINC.NU_SEQ_TIPO_PERFIL = 6', null)
            ->where('USU.NU_SEQ_USUARIO = ?', $idArticulador)
            ->where('PER_VINC.NU_SEQ_PERIODO_VINCULACAO = ?', $idPeriodo)
            ->group(array(
                'USU.NU_SEQ_USUARIO',
                'USU.SG_UF_ATUACAO_PERFIL',
                'USU.NO_USUARIO',
                'USU.NU_CPF',
                'PER_VINC.NU_SEQ_PERIODO_VINCULACAO',
                'PER_VINC.DT_INICIAL',
                'PER_VINC.DT_FINAL',
                'CUR.NU_SEQ_CURSO',
                'CUR.DS_NOME_CURSO',
                'CUR.VL_CARGA_HORARIA',
            ));
        $result = $this->fetchRow($select);

        return $result ? $result->toArray() : array();
    }
}
