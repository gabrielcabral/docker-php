<?php

/**
 * Arquivo de classe Febraban MEC.
 *
 * @package Fnde
 * @category Fnde_Model
 * @name Federaчуo Brasileira de Bancos
 * @author Daniel Wilson de Alvarenga <daniel.alvarenga@fnde.gov.br>
 */

/**
 * Classe que consome o Federaчуo Brasileira de Bancos
 *
 * @version $Id$
 */
class Fnde_Model_Febraban extends Fnde_Model_Service_Mec_Soap {

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
        
        if (empty($this->_config['webservices']['febraban']['uri'])) {
            throw new Zend_Soap_Client_Exception('O URI do serviчo da Federaчуo Brasileira de Bancos deve conter no arquivo de configuraчуo 
                [application.ini] webservices.febraban.uri = http://example ');
        }
        
        $this->setUrl($this->_config['webservices']['febraban']['uri']);
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
    public function recuperarLista() {

        $xml = self::$soapClient->recuperarLista();
        $xml = $this->xmlToArray($xml);
        
        foreach ($xml["banco"] as $key => $value) {
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
    public function recuperarPorId($idBanco) {

        $xml = self::$soapClient->recuperarPorId($idBanco);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["banco"] as $key => $value) {
                $result[$key] = $value;
            }
        } else {
            $result[] = "Nуo Encontrado.";
        }
        return $result;
    }
    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
    public function recuperarPorNome($nomeBanco) {

        $xml = self::$soapClient->recuperarPorNome($nomeBanco);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["banco"] as $key => $value) {
                $result[$key] = $value;
            }
        } else {
            $result[] = "Nуo Encontrado.";
        }
        return $result;
    }
}
?>