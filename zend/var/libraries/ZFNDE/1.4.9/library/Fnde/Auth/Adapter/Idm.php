<?php
/**
 * Description of Idm
 *
 * @author Daniel Wilson de Alvarenga daniel.alvarenga@fnde.gov.br
 */
class Fnde_Auth_Adapter_Idm implements Zend_Auth_Adapter_Interface {

    public function setIdentity($value) {
        $this->_identity = $value;
        return $this;
    }

    public function getIdentity() {
        return $this->_identity;
    }

    /**
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        return $this->result(Zend_Auth_Result::SUCCESS);
    }

    /**
     * @param int $code
     * @param array $messages
     * @return Zend_Auth_Result
     */
    public function result($code, $messages = array()) {
        return new Zend_Auth_Result(
                        $code,
                        $this->getIdentity(),
                        $messages
        );
    }

}