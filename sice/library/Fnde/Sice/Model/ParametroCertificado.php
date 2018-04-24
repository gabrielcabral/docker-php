<?php
/**
 * Arquivo de classe de modelo do tipo: database
 *
 * Gerado automaticamente pelo gerador: ZFnde Model.
 *
 * $Rev::                      $
 * $Date::  12/04/2013         $
 * $Author::                   $
 *
 * @package Sice
 * @category Model
 * @name ParametroCertificado
 */

/**
 * Classe de Modelo: Fnde_Sice_Model_ParametroCertificado
 * @uses Fnde_Sice_Model_Database_ParametroCertificado
 * @version $Id$
 */
class Fnde_Sice_Model_ParametroCertificado extends Fnde_Sice_Model_Database_ParametroCertificado
{

    public function getPorData($data){
        $select = $this->select()
            ->from(array('pc' => "{$this->_schema}.{$this->_name}"), array(
                'NU_SEQ_PARAM_CERT',
                'NO_SECRETARIO',
                'NO_CARGO',
                'NO_LOCAL_ATUACAO',
                'DT_INICIO' => "TO_CHAR(DT_INICIO, 'DD/MM/YYYY')",
                'DT_FIM' => "TO_CHAR(DT_FIM, 'DD/MM/YYYY')",
                'NU_SEQ_LOGOMARCA_CASTOR',
                'DS_FRASE_EFEITO',
                'NU_SEQ_USUARIO_ATUALIZADOR'
            ))
            ->where("to_date(?,'DD/MM/YYYY') BETWEEN DT_INICIO AND DT_FIM", $data)
            ->orWhere("( to_date(?,'DD/MM/YYYY') >= DT_INICIO AND DT_FIM IS NULL )", $data);

        $response = $this->fetchRow($select);
        return $response ? $response->toArray() : array();
    }

    public function getMaisAtual($idExcessao = null)
    {
        $select = $this->select()
            ->from(array('pc' => "{$this->_schema}.{$this->_name}"), array(
                'NU_SEQ_PARAM_CERT',
                'NO_SECRETARIO',
                'NO_CARGO',
                'NO_LOCAL_ATUACAO',
                'DT_INICIO' => "TO_CHAR(DT_INICIO, 'DD/MM/YYYY')",
                'DT_INICIO_EUA' => "TO_CHAR(DT_INICIO, 'YYYY-MM-DD')",
                'DT_FIM' => "TO_CHAR(DT_FIM, 'DD/MM/YYYY')",
                'DT_FIM_EUA' => "TO_CHAR(DT_FIM, 'YYYY-MM-DD')",
                'NU_SEQ_LOGOMARCA_CASTOR',
                'DS_FRASE_EFEITO',
                'NU_SEQ_USUARIO_ATUALIZADOR'
            ))
            ->where("DT_INICIO = ?", new Zend_Db_Expr("(SELECT MAX(DT_INICIO) FROM {$this->_schema}.{$this->_name})"));

        if($idExcessao){
            $select->where('NU_SEQ_PARAM_CERT != ?', $idExcessao);
        }

        return $this->fetchAll($select)->current();
    }

    public function find($id)
    {
        $select = $this->select()
            ->from(array('pc' => "{$this->_schema}.{$this->_name}"), array(
                'NU_SEQ_PARAM_CERT',
                'NO_SECRETARIO',
                'NO_CARGO',
                'NO_LOCAL_ATUACAO',
                'DT_INICIO' => "TO_CHAR(DT_INICIO, 'DD/MM/YYYY')",
                'DT_FIM' => "TO_CHAR(DT_FIM, 'DD/MM/YYYY')",
                'NU_SEQ_LOGOMARCA_CASTOR',
                'DS_FRASE_EFEITO',
                'NU_SEQ_USUARIO_ATUALIZADOR'
            ))
            ->where('NU_SEQ_PARAM_CERT = ?', $id);
        return $this->fetchRow($select);
    }

    public function conflitoData($dataInicio, $dataFim = null, $idExcessao = null)
    {

        $select = $this->select()
            ->from(array('pc' => "{$this->_schema}.{$this->_name}"), array(
                'NU_SEQ_PARAM_CERT',
                'NO_SECRETARIO',
                'NO_CARGO',
                'NO_LOCAL_ATUACAO',
                'DT_INICIO' => "TO_CHAR(DT_INICIO, 'YYYY-MM-DD')",
                'DT_FIM' => "TO_CHAR(DT_FIM, 'YYYY-MM-DD')",
                'NU_SEQ_LOGOMARCA_CASTOR',
                'DS_FRASE_EFEITO',
                'NU_SEQ_USUARIO_ATUALIZADOR'
            ))
            ->order('DT_INICIO DESC');

        if ($idExcessao) {
            $select = $select->where('NU_SEQ_PARAM_CERT != ?', $idExcessao);
        }

        if (empty($dataFim)) {
            $select = $select->where("(to_date(:data_inicio,'YYYY-MM-DD') <= DT_INICIO OR to_date(:data_inicio,'YYYY-MM-DD') <= DT_FIM)")
            ->bind(array(
                ':data_inicio' => $dataInicio
            ));
        } else {
            $select = $select->where("(
                (DT_INICIO BETWEEN to_date(:data_inicio,'YYYY-MM-DD') AND to_date(:data_fim,'YYYY-MM-DD'))
                OR (DT_FIM BETWEEN to_date(:data_inicio,'YYYY-MM-DD') AND to_date(:data_fim,'YYYY-MM-DD'))
                OR (DT_INICIO <= to_date(:data_inicio,'YYYY-MM-DD') AND DT_FIM >= to_date(:data_fim,'YYYY-MM-DD'))
                )")
                ->bind(array(
                    ':data_inicio' => $dataInicio,
                    ':data_fim' => $dataFim
                ));
        }

        $data = $this->fetchAll($select);

        if ($data->count() > 0) {
            $data = $data->toArray();
            // se a data inicio escolhida for superior a data do registro mais recente e este não tiver uma data fim,
            // então não existe conflito.
            // pois a data fim sera setada automaticamente para o registro ja existente na base
            return !($data[0]['DT_FIM'] == null && $data[0]['DT_INICIO'] < $dataInicio);
        }
        return false;
    }

    function lista($filtro)
    {
        $select = $this->select()
            ->from(array('pc' => "{$this->_schema}.{$this->_name}"), array(
                'NU_SEQ_PARAM_CERT',
                'DT_INICIO' => "TO_CHAR(DT_INICIO, 'DD/MM/YYYY')",
                'DT_FIM' => "TO_CHAR(DT_FIM, 'DD/MM/YYYY')",
                'NO_SECRETARIO',
                'NO_CARGO',
                'NO_LOCAL_ATUACAO',
                'DS_FRASE_EFEITO'
            ))
            ->order('DT_INICIO DESC');

        if (!empty($filtro['ANO'])) {
            $select->where("TO_CHAR(DT_INICIO, 'YYYY') = ?", $filtro['ANO']);
        }

        if (isset($filtro['NO_SECRETARIO'])) {
            $filtro['NO_SECRETARIO'] = strtoupper($filtro['NO_SECRETARIO']);
            $select->where("UPPER(NO_SECRETARIO) LIKE ?", "%{$filtro['NO_SECRETARIO']}%");
        }

        if (isset($filtro['NO_CARGO'])) {
            $filtro['NO_CARGO'] = strtoupper($filtro['NO_CARGO']);
            $select->where("UPPER(NO_CARGO) LIKE ?", "%{$filtro['NO_CARGO']}%");
        }

        if (isset($filtro['NO_LOCAL_ATUACAO'])) {
            $filtro['NO_LOCAL_ATUACAO'] = strtoupper($filtro['NO_LOCAL_ATUACAO']);
            $select->where("UPPER(NO_LOCAL_ATUACAO) LIKE ?", "%{$filtro['NO_LOCAL_ATUACAO']}%");
        }

        $resultado = $this->fetchAll($select)->toArray();

        return $resultado;
    }
}