<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Schema
 *
 * @author walkero
 */
class Fnde_Corp_Model_Schema {

    /**
     * @var Zend_Db_Adapter_Pdo_Oci
     */
    private $_db = null;

    public function __construct() {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    /**
     *
     * @param string $grantee
     * @return array
     */
    public function listTables($grantee) {
                ;
        try {
            $tmpReturn = $this->_db->fetchAll("
                SELECT DISTINCT a.table_schema, a.table_name, b.object_type
                FROM all_tab_privs a, all_objects b
                WHERE a.table_name = b.object_name
                    AND b.object_type IN ('TABLE', 'VIEW')
                    AND a.grantee = '{$grantee}'
                    AND a.table_name not like 'BIN$%'
                ORDER BY a.table_schema, a.table_name
            ");
        } catch (PDOException $e) {
            throw new Exception(__CLASS__ . ' Database Statement Exception', E_ERROR);
        }
        return $tmpReturn;
    }

    /**
     *
     * @param string $name
     * @return array
     */
    public function infoTable($name){
        $table = new Zend_Db_Table($name);
        return $table->info();
    }
}
?>
