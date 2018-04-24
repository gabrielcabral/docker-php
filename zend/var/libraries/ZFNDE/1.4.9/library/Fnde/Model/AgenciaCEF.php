<?php

/**
 * Arquivo de classe AgenciasCef MEC.
 *
 * @package Fnde
 * @category Fnde_Model
 * @name Agencia Caixa Economica Federal
 * @author Daniel Wilson de Alvarenga <daniel.alvarenga@fnde.gov.br>
 */

/**
 * Classe que consome dados da Agencia Caixa Econômica Federal
 *
 * @version $Id$
 */
class Fnde_Model_AgenciaCEF extends Fnde_Model_Service_Mec_Soap {

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
        
        if (empty($this->_config['webservices']['agenciacef']['uri'])) {
            throw new Zend_Soap_Client_Exception('O URI do serviço do Agências do Banco do Brasil deve conter no arquivo de configuração 
                [application.ini] webservices.agenciacef.uri = http://example ');
        }
        
        $this->setUrl($this->_config['webservices']['agenciacef']['uri']);
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
    public function getMunicipio($codIBGE, $faixaDistancia) {

        $xml = self::$soapClient->getMunicipio($codIBGE, $faixaDistancia);
        $xml = $this->xmlToArray($xml);

        foreach ($xml["NODELIST"] as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }
    
    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
    public static function getGeoReferenciamento($codIBGE, $faixaDistancia) {
        return self::$soapClient->getObjMunicipio($codIBGE, $faixaDistancia);
    }
}
?>