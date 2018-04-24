<?php
/**
 * @category   Fnde
 * @package    Db
 * @subpackage Adapter
 * @author     Alberto Guimarães Viana <alberto.viana@fnde.gov.br>
 * @author     Theoziran Lima<theoziran.silva@fnde.gov.br>
 * @version    0.3
 */
class Fnde_Db_Adapter_Pdo_Oci extends Zend_Db_Adapter_Pdo_Oci {

    public function __construct($config) {
        parent::__construct($config);
        $this->init();
    }

    protected function init() {
        $this->fixOrderBy();
    }


    /**
     * Quando o Oracle orderna um campo no banco de dados ele faz isso baseado no collation definido
     * na tabela, então quando não não está como padrão ordernar binariamente esse método define.
     * @return void
     */
    protected function fixOrderBy() {
        $this->query("ALTER SESSION SET nls_sort='BINARY_AI'");
    }

    /**
     * Inserts a table row with specified data.
     *
     * @param mixed $table The table to insert data into.
     * @param array $bind Column-value pairs.
     * @return int The number of affected rows.
     */
    public function insert($table, array $bind) {
        // extract and quote col names from the array keys
        $cols = array();
        $vals = array();
        foreach ($bind as $col => $val) {
            $cols[] = $this->quoteIdentifier($col, true);
            if ($val instanceof Zend_Db_Expr) {
                $vals[] = $val->__toString();
                unset($bind[$col]);
            } else {
                if (is_float($val)) {
                    $vals[] = $val;
                } else if ($this->checkParamDate($val)) {
                    $vals[] = $this->formatDate($val);
                } else {
                    $val = stripcslashes($val);
                    $vals[] = $this->quote($val);
                }
            }
        }

        // build the statement
        $sql = "INSERT INTO "
                . $this->quoteIdentifier($table, true)
                . ' (' . implode(', ', $cols) . ') '
                . 'VALUES (' . implode(', ', $vals) . ')';

        // execute the statement and return the number of affected rows
        $stmt = $this->prepare($sql);
        $stmt->execute();
        $result = $stmt->rowCount();
        return $result;
    }

    /**
     * Updates table rows with specified data based on a WHERE clause.
     *
     * @param  mixed        $table The table to update.
     * @param  array        $bind  Column-value pairs.
     * @param  mixed        $where UPDATE WHERE clause(s).
     * @return int          The number of affected rows.
     */
    public function update($table, array $arParams, $where = '') {
        $set = array();
        foreach ($arParams as $col => $value) {
            if (is_float($value)) {
                $set[] = $col . '=' . $value;
            } else if ($this->checkParamDate($value)) {
                $set[] = $col . '=' . $this->formatDate($value);
            } else {
                $value = stripcslashes($value);
                $set[] = $col . '=' . $this->quote($value);
            }
        }

        $where = $this->_whereExpr($where);

        $sql = "UPDATE "
                . $this->quoteIdentifier($table, true)
                . ' SET ' . implode(', ', $set)
                . (($where) ? " WHERE $where" : '');

        $stmt = $this->prepare($sql);
        $stmt->execute();

        $result = $stmt->rowCount();
        return $result;
    }

    /**
     * Verifica se o parametro informado tem o formato de data
     *
     * @param  string   $param
     * @return boolean
     */
    private function checkParamDate($param) {
        return ( ( preg_match('/^([0,1,2][0-9]|3[0,1])\/(0[1-9]|1[0,1,2])\/([19|20]+[0-9]{2})$/', $param) ) ? true : false );
    }

    /**
     * Adiciona a funcao to_date do Oracle
     *
     * @param  string $param
     * @return string
     */
    private function formatDate($param) {
        return "to_date('{$param}', 'dd/mm/yyyy')";
    }

}