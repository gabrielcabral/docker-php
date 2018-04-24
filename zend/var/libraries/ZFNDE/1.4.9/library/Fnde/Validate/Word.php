<?php
/**
 * Validador para palavras inexistentes 
 * @author theoziran.silva@fnde.gov.br
 */
require_once 'Zend/Validate/Abstract.php';

class Fnde_Validate_Word extends Zend_Validate_Abstract {
    const WORD = 'word';

    /**
     * http://pt.wikipedia.org/wiki/Maiores_palavras_da_l%C3%ADngua_portuguesa
     */
    const BIGGEST_WORD = 29;

    /**
     * @var array 
     */
    protected $_messageTemplates = array(
        self::WORD => "O campo contém palavra provavelmente inexistente no português (brasileiro)"
    );

    /**
     * @return boolean 
     */
    public function isValid($value) {
        $this->_setValue($value);

        $words = explode(' ', $value);
        foreach ($words as $word) {
            if ($this->isBigger($word)) {
                $this->_error(self::WORD);
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $word
     * @return boolean 
     */
    protected function isBigger($word) {
        return strlen($word) > self::BIGGEST_WORD;
    }


}