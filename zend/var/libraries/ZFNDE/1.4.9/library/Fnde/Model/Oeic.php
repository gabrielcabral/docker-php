<?php

/**
 * Arquivo de classe OEIC MEC.
 *
 * @package Fnde
 * @category Fnde_Model
 * @name Orgão Emissor de Identificação Civil (OEIC)
 * @author Daniel Wilson de Alvarenga <daniel.alvarenga@fnde.gov.br>
 */

/**
 * Classe que consome o Orgão Emissor de Identificação Civil (OEIC)
 *
 * @version $Id$
 */
class Fnde_Model_Oeic extends Fnde_Model_Service_Mec_Soap {

    /**
     * SoapClient Class
     *
     * @var SoapClient
     */
    private static $soapClient;
    private static $isConnected;

    /**
     * @throws Zend_Soap_Client_Exception
     * @return void
     */
    public function __construct() {
                 
        parent::__construct();
        
        if (empty($this->_config['webservices']['oeic']['uri'])) {
            throw new Zend_Soap_Client_Exception('O URI do serviço do Orgão Emissor de Identificação Civil (OEIC) deve conter no arquivo de configuração 
                [application.ini] webservices.oeic.uri = http://example ');
        }
        
        $this->setUrl($this->_config['webservices']['oeic']['uri']);
        $this->conectar();
    }

    public function conectar() {
        try {
          //self::$soapClient = new Zend_Soap_Client($this->getUrl());
            self::$soapClient = new SoapClient($this->getUrl());
            self::$isConnected = true;
        } catch (Exception $e) {
            self::$isConnected = false;
        }
    }

    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
    public function getTest() {
    
    //$funct = self::$soapClient->__getTypes();
    $funct = self::$soapClient->__getFunctions();
    $funct = $this->lerTodos();
    
    echo "<pre>";
    print_r($funct);
    die;
    }

    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
    public function lerTodos() {

        $xml = self::$soapClient->lerTodos();
        $xml = $this->xmlToArray($xml);
        
        if (isset($xml)) {
            foreach ($xml["NODELIST"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
?>