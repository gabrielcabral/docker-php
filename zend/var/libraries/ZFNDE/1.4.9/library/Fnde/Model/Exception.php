<?php
class Fnde_Model_Exception extends Fnde_Exception
{
    protected $_message = 'Model: %s';
    
    public function  __construct($message, $code) {
        parent::__construct($message, $code);
    }
}