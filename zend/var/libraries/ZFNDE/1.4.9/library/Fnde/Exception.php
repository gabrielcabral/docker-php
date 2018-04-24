<?php
class Fnde_Exception extends Exception 
{
    protected $_message = 'Fnde: %s';

    public function  __construct($message, $code) {
        parent::__construct(sprintf($this->_message, $message), $code);
    }

}