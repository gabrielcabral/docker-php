<?php

/**
 * Arquivo de classe Ibge MEC.
 *
 * @package Fnde
 * @category Fnde_Model
 * @name Instituto Brasileiro de Geografia e Estatística (IBGE)
 * @author Daniel Wilson de Alvarenga <daniel.alvarenga@fnde.gov.br>
 */

/**
 * Classe que consome o Instituto Brasileiro de Geografia e Estatística (IBGE)
 *
 * @version $Id$
 */
class Fnde_Model_Ibge extends Fnde_Model_Service_Mec_Soap {

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
        
        if (empty($this->_config['webservices']['ibge']['uri'])) {
            throw new Zend_Soap_Client_Exception('O URI do serviço do Instituto Brasileiro de Geografia e Estatística (IBGE) deve conter no arquivo de configuração 
                [application.ini] webservices.ibge.uri = http://example ');
        }
        
        $this->setUrl($this->_config['webservices']['ibge']['uri']);
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
    public function lerIBGE($coUf, $coMunicipio) {

        $xml = self::$soapClient->lerIBGE($coUf, $coMunicipio);
        $xml = $this->xmlToArray($xml);
        
        if (isset($xml)) {
            foreach ($xml["NODELIST"] as $key => $value) {
                $result[$key] = $value;
            }
        } else {
            $result[] = "Não Encontrado.";
        }
        return $result;
    }
}
?>