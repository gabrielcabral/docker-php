<?php

/**
 * Classe abstrata Fnde Model Database Abstract
 *
 * Gerado automaticamente pelo gerador: ZFnde Model.
 *
 * Último Commit: r$Rev: 2 $ por $Author: WalkerAlencar $ em $Date: 2010-08-25 13:12:54 -0300 (qua, 25 ago 2010) $.
 *
 * @package Database
 * @category Model Abstract
 * @name Fnde Model Database Abstract
 * @author Walker Alencar
 * @author Theoziran Lima <theoziran.silva@fnde.gov.br>
 */

/**
 * Classe de Modelo: Fnde_Model_Database_Abstract
 * @uses Zend_Db_Table_Abstract
 * @version $Id: Album.php 2 2010-08-25 16:12:54Z WalkerAlencar $
 */
abstract class Fnde_Model_Database_Abstract extends Zend_Db_Table_Abstract {

    public function fixDate() {
        $this->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
    }

    public function fixDateToBr() {
        $this->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
    }

    public function validateRequiredFields(array $data) {
        foreach ($this->_cols as $key => $col){
            if (is_string($key)){
                $data[$col] = $data[$key];
            }
        }
        foreach ($this->_metadata as $key => $value) {
            if (($value['NULLABLE'] === false) && ($value['PRIMARY'] === false) && (!isset($data[$key]) || empty($data[$key]))) {
                throw new Fnde_Model_Exception('Campo(s) requerido(s) não informado(s)', E_ERROR);
            }
        }
    }

    public function getCols() {
        return $this->_getCols();
    }

}