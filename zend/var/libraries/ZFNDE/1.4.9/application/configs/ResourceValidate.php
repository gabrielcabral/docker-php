<?php
function UTF8Fix (&$value){
	$value = utf8_decode($value);
}
if ( defined('ZF_FNDE_ROOT') ){
    $resourceFile = ZF_FNDE_ROOT . '/resources/languages/pt_BR/Zend_Validate.php';

    if (file_exists($resourceFile)) {
        $data = include($resourceFile);
    } else {
        throw new Exception('Resources da Zend indispon�veis!', E_ERROR);
    }
    array_walk($data,'UTF8Fix');
    $data["'%value%' is less than %min% characters long"] = "O conte�do informado n�o pode ser inferior a '%min%' caracteres.";
    $data["'%value%' is more than %max% characters long"] = "O conte�do informado n�o pode ser superior a '%max%' caracteres.";
    $data["Value is required and can't be empty"] = "O campo � obrigat�rio e n�o pode estar vazio";
    return $data;
}