<?php
class Fnde_Model_Service_Exception extends Fnde_Exception
{
    protected $_message = 'Service: %s';
    
    public function  __construct($message, $code) {
        parent::__construct($message, $code);
    }
}