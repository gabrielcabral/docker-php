<?php

/**
 * Created by PhpStorm.
 * User: 05922176633
 */
class Fnde_Sice_Business_Gerenciais
{
    public function getAvaliacoesInstitucionais($filtro)
    {
        $where  = "where 1 = 1 ";
        $where .= ($filtro['NU_ANO']) ? " and to_char(t.dt_fim, 'yyyy') = '{$filtro['NU_ANO']}' " : "";

        $ufs_in = "";
        foreach ($filtro['SG_UF'] as $uf) {
            $ufs_in .= "'{$uf}',";
        }
        $ufs_in = substr($ufs_in, 0, -1);

        $where .= " and mu.sg_uf in ({$ufs_in}) ";

        $query = "
            select mu.sg_uf, upper(mr.no_meso_regiao) as no_meso_regiao, mu.no_municipio, t.nu_seq_turma,

            count(distinct decode(ac.nu_questao_1, 1, nu_seq_usuario, null)) as q1_r1,
            count(distinct decode(ac.nu_questao_1, 2, nu_seq_usuario, null)) as q1_r2,
            count(distinct decode(ac.nu_questao_1, 3, nu_seq_usuario, null)) as q1_r3,
            count(distinct decode(ac.nu_questao_1, 4, nu_seq_usuario, null)) as q1_r4,

            count(distinct decode(ac.nu_questao_2, 1, nu_seq_usuario, null)) as q2_r1,
            count(distinct decode(ac.nu_questao_2, 2, nu_seq_usuario, null)) as q2_r2,
            count(distinct decode(ac.nu_questao_2, 3, nu_seq_usuario, null)) as q2_r3,
            count(distinct decode(ac.nu_questao_2, 4, nu_seq_usuario, null)) as q2_r4,

            count(distinct decode(ac.nu_questao_3, 1, nu_seq_usuario, null)) as q3_r1,
            count(distinct decode(ac.nu_questao_3, 2, nu_seq_usuario, null)) as q3_r2,
            count(distinct decode(ac.nu_questao_3, 3, nu_seq_usuario, null)) as q3_r3,
            count(distinct decode(ac.nu_questao_3, 4, nu_seq_usuario, null)) as q3_r4,

            count(distinct decode(ac.nu_questao_4, 1, nu_seq_usuario, null)) as q4_r1,
            count(distinct decode(ac.nu_questao_4, 2, nu_seq_usuario, null)) as q4_r2,
            count(distinct decode(ac.nu_questao_4, 3, nu_seq_usuario, null)) as q4_r3,
            count(distinct decode(ac.nu_questao_4, 4, nu_seq_usuario, null)) as q4_r4,

            count(distinct decode(ac.nu_questao_5, 1, nu_seq_usuario, null)) as q5_r1,
            count(distinct decode(ac.nu_questao_5, 2, nu_seq_usuario, null)) as q5_r2,
            count(distinct decode(ac.nu_questao_5, 3, nu_seq_usuario, null)) as q5_r3,
            count(distinct decode(ac.nu_questao_5, 4, nu_seq_usuario, null)) as q5_r4,

            count(distinct decode(ac.nu_questao_6, 1, nu_seq_usuario, null)) as q6_r1,
            count(distinct decode(ac.nu_questao_6, 2, nu_seq_usuario, null)) as q6_r2,
            count(distinct decode(ac.nu_questao_6, 3, nu_seq_usuario, null)) as q6_r3,
            count(distinct decode(ac.nu_questao_6, 4, nu_seq_usuario, null)) as q6_r4,

            count(distinct decode(ac.nu_questao_7, 1, nu_seq_usuario, null)) as q7_r1,
            count(distinct decode(ac.nu_questao_7, 2, nu_seq_usuario, null)) as q7_r2,
            count(distinct decode(ac.nu_questao_7, 3, nu_seq_usuario, null)) as q7_r3,
            count(distinct decode(ac.nu_questao_7, 4, nu_seq_usuario, null)) as q7_r4,

            count(distinct decode(ac.nu_questao_8, 1, nu_seq_usuario, null)) as q8_r1,
            count(distinct decode(ac.nu_questao_8, 2, nu_seq_usuario, null)) as q8_r2,
            count(distinct decode(ac.nu_questao_8, 3, nu_seq_usuario, null)) as q8_r3,
            count(distinct decode(ac.nu_questao_8, 4, nu_seq_usuario, null)) as q8_r4,

            count(distinct decode(ac.nu_questao_9, 1, nu_seq_usuario, null)) as q9_r1,
            count(distinct decode(ac.nu_questao_9, 2, nu_seq_usuario, null)) as q9_r2,
            count(distinct decode(ac.nu_questao_9, 3, nu_seq_usuario, null)) as q9_r3,
            count(distinct decode(ac.nu_questao_9, 4, nu_seq_usuario, null)) as q9_r4,

            count(distinct decode(ac.nu_questao_10, 1, nu_seq_usuario, null)) as q10_r1,
            count(distinct decode(ac.nu_questao_10, 2, nu_seq_usuario, null)) as q10_r2,
            count(distinct decode(ac.nu_questao_10, 3, nu_seq_usuario, null)) as q10_r3,
            count(distinct decode(ac.nu_questao_10, 4, nu_seq_usuario, null)) as q10_r4

            from sice_fnde.s_turma t
            inner join sice_fnde.s_avaliacao_curso ac on ac.nu_seq_turma = t.nu_seq_turma
            inner join corp_fnde.s_municipio mu on mu.co_municipio_ibge = t.co_municipio
            inner join cte_fnde.t_meso_regiao mr on mr.co_meso_regiao = t.co_mesorregiao

            {$where}

            group by mu.sg_uf, mr.no_meso_regiao, mu.no_municipio, t.nu_seq_turma
            order by mu.sg_uf, mr.no_meso_regiao, mu.no_municipio, t.nu_seq_turma
        ";

//        echo "<pre>".print_r($query, 1)."</pre>";
//        exit;

        $obModelo = new Fnde_Sice_Model_AvaliacaoCurso();

        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetchAll();

        foreach ($result as $i => $itens) {
            foreach ($itens as $col => $val) {
                $result[$i][$col] = utf8_encode($val);
            }
        }

        return $result;
    }

    public function getAvaliacoesInstitucionaisNova($filtro){
        $where  = "where 1 = 1 ";

        $where .= ($filtro["NU_SEQ_TURMA"]) ? " and ac.nu_seq_turma = " . $filtro["NU_SEQ_TURMA"]  : "";

        if($filtro['NU_ANO'] == '9999'){
            $objTurma = new Fnde_Sice_Business_Turma();
            $anos = $objTurma->getAnosTurmas();
            foreach ($anos as $y => $ano) {
                $arrAnos[$y] = $ano['ANO'];
            }
            $todos = implode(',',$arrAnos);

            $where .= ($filtro['NU_ANO']) ? "AND TO_CHAR(vt.dt_inicio, 'YYYY') IN ($todos) " : "";
        }else{
            $where .= ($filtro['NU_ANO']) ? "AND TO_CHAR(vt.dt_inicio, 'YYYY') = '{$filtro['NU_ANO']}' " : "";
        }

        if($filtro['DT_INICIO']){
            $dt_fim = ($filtro['DT_FIM']) ? $filtro['DT_FIM'] : date('d/m/Y');
            $where .= 'AND vt.DT_FINALIZACAO between \''.$filtro['DT_INICIO'] .'\' AND \''.$dt_fim.'\'';
        }

        if($filtro['SG_UF']){
            $ufs_in = "'";
            $ufs_in .= implode("','" , $filtro['SG_UF']);
            $ufs_in .= "'";
            $where .= ($ufs_in) ? " and vt.UF_TURMA in ({$ufs_in}) " : "";
        }

        $where .= ($filtro["CO_MESORREGIAO"]) ? " and vt.CO_MESORREGIAO = " . $filtro["CO_MESORREGIAO"]  : "";

        $where .= ($filtro["CO_MUNICIPIO"]) ? " and vt.CO_MUNICIPIO = " . $filtro["CO_MUNICIPIO"]  : "";

        $where .= (is_numeric($filtro["CO_REDE_ENSINO"])) ? " and de.CO_REDE_ENSINO = " . $filtro["CO_REDE_ENSINO"]  : "";

        $query = "SELECT ac.NU_QUESTAO_1 as q1,
                        ac.NU_QUESTAO_2 as q2,
                        ac.NU_QUESTAO_3 as q3,
                        ac.NU_QUESTAO_4 as q4,
                        ac.NU_QUESTAO_5 as q5,
                        ac.NU_QUESTAO_6 as q6,
                        ac.NU_QUESTAO_7 as q7,
                        ac.NU_QUESTAO_8 as q8,
                        ac.NU_QUESTAO_9 as q9,
                        ac.NU_QUESTAO_10 as q10,
                        to_char(vt.dt_inicio, 'yyyy') as ano,
                        vt.UF_TURMA as uf,
                        vt.DS_NOME_CURSO as curso,
                        vt.NU_SEQ_CURSO  as id
        FROM sice_fnde.s_avaliacao_curso ac
        inner join SICE_FNDE.V_SISREL_TURMAS vt
          on vt.NU_SEQ_TURMA = ac.NU_SEQ_TURMA
        inner join SICE_FNDE.S_DADOS_ESCOLARES_CURSISTA de
          on de.NU_SEQ_USUARIO_CURSISTA = ac.NU_SEQ_USUARIO
        {$where}
        order by curso, uf
        ";
        $obModelo = new Fnde_Sice_Model_AvaliacaoCurso();

        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetchAll();

        return $result;
    }

    public function getDadosCursistas($filtro){
        $where  = "where 1 = 1 ";

        $where .= ($filtro["NU_SEQ_TURMA"]) ? " and ac.nu_seq_turma = " . $filtro["NU_SEQ_TURMA"]  : "";

        $where .= ($filtro['NU_ANO']) ? "AND TO_CHAR(vt.dt_inicio, 'YYYY') = '{$filtro['NU_ANO']}' " : "";

        if($filtro['DT_INICIO']){
            $dt_fim = ($filtro['DT_FIM']) ? $filtro['DT_FIM'] : date('d/m/Y');
            $where .= 'AND vt.DT_FINALIZACAO between \''.$filtro['DT_INICIO'] .'\' AND \''.$dt_fim.'\'';
        }

        if($filtro['SG_UF']){
            $ufs_in = "";
            foreach ($filtro['SG_UF'] as $uf) {
                $ufs_in .= "'{$uf}',";
            }
            $ufs_in = substr($ufs_in, 0, -1);
            $where .= ($ufs_in) ? " and vt.UF_TURMA in ({$ufs_in}) " : "";
        }

        $where .= ($filtro["CO_MESORREGIAO"]) ? " and vt.CO_MESORREGIAO = " . $filtro["CO_MESORREGIAO"]  : "";

        $where .= ($filtro["CO_MUNICIPIO"]) ? " and vt.CO_MUNICIPIO = " . $filtro["CO_MUNICIPIO"]  : "";

        $where .= (is_numeric($filtro["CO_REDE_ENSINO"])) ? " and de.CO_REDE_ENSINO = " . $filtro["CO_REDE_ENSINO"]  : "";

        $query = "SELECT ac.NU_QUESTAO_1 as q1,
                        ac.NU_QUESTAO_2 as q2,
                        ac.NU_QUESTAO_3 as q3,
                        ac.NU_QUESTAO_4 as q4,
                        ac.NU_QUESTAO_5 as q5,
                        ac.NU_QUESTAO_6 as q6,
                        ac.NU_QUESTAO_7 as q7,
                        ac.NU_QUESTAO_8 as q8,
                        ac.NU_QUESTAO_9 as q9,
                        ac.NU_QUESTAO_10 as q10,
                        to_char(vt.dt_inicio, 'yyyy') as ano,
                        vt.UF_TURMA as uf,
                        vt.DS_NOME_CURSO as curso,
                        vt.NU_SEQ_CURSO  as id
        FROM sice_fnde.s_avaliacao_curso ac
        inner join SICE_FNDE.V_SISREL_TURMAS vt
          on vt.NU_SEQ_TURMA = ac.NU_SEQ_TURMA
        inner join SICE_FNDE.S_DADOS_ESCOLARES_CURSISTA de
          on de.NU_SEQ_USUARIO_CURSISTA = ac.NU_SEQ_USUARIO
        {$where}

        ";
        $obModelo = new Fnde_Sice_Model_AvaliacaoCurso();

        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetchAll();

        return $result;
    }
}