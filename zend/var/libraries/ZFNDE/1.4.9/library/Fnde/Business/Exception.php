<?php
class Fnde_Business_Exception extends Fnde_Exception 
{
    protected $_message = 'Business: %s';

    public function  __construct($message, $code) {
        parent::__construct($message, $code);
    }
}