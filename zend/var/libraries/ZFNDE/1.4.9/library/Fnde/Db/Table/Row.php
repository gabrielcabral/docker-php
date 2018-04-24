<?php
/**
 * $Rev:: 14#$
 * $Date:: 2#$
 * $Author:: $
 *
 * @package ZFnde
 * @category Db Table Row
 * @name Db Table Row
 *
 * Fnde Db Table Row
 * @since v1.1.2
 * @author theoziran<theoziran.silva@fnde.gov.br>
 */
class Fnde_Db_Table_Row extends Zend_Db_Table_Row_Abstract {

    protected $_maps;
    protected $_cols;

    public function __construct(array $config = array()) {
        parent::__construct($config);
    }

    public function init() {
        $this->_cols = $this->_table->getCols();
        $this->_maps = array_filter(array_keys($this->_cols), 'is_string');
    }

    /**
     * Transforma os labels baseado no mapeamento $_cols
     * @param string $columnName
     * @return string
     */
    protected function _transformColumn($columnName) {
        if (array_search($columnName, $this->_maps) !== false) {
            $columnName = $this->_cols[$columnName];
        }
        return parent::_transformColumn($columnName);
    }

    /**
     * Retorna os arrays adicionando o campo com os mapeamentos
     * @return array
     */
    public function toArray() {
        $data = (array) $this->_data;
        $cols = $this->_cols;
        foreach ($this->_maps as $map)
            if (array_key_exists($map, $cols))
                $data[$map] = $data[$cols[$map]];
        return $data;
    }

    /**
     * Pode passar os dados através dos array baseado no mapeamento dos $_cols
     * @param array $data
     * @return Fnde_Db_Table_Row 
     */
    public function setFromArray(array $data) {
        foreach ($data as $columnName => $value) {
            $columnName = $this->_transformColumn($columnName);
            $this->__set($columnName, $value);
        }
        return $this;
    }

}