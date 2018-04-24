<?php

/**
 * Arquivo de classe Diretório Nacional de Endereços - Dne/MEC.
 *
 * @package Fnde
 * @category Fnde_Model
 * @name Diretório Nacional de Endereços
 * @author Daniel Wilson de Alvarenga <daniel.alvarenga@fnde.gov.br>
 */

/**
 * Classe que consome o Webservice Dne
 *
 * @version $Id$
 */
class Fnde_Model_Dne extends Fnde_Model_Service_Mec_Soap {

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

        if (empty($this->_config['webservices']['dne']['uri'])) {
            throw new Zend_Soap_Client_Exception('O URI do serviço do Diretório Nacional de Endereços deve conter no arquivo de configuração 
                [application.ini] webservices.dne.uri = http://example ');
        }

        $this->setUrl($this->_config['webservices']['dne']['uri']);
        $this->conectar();
    }

    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
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
     * Captura a lista de Cpf
     *
     * @return mixed
     */
    public function getTest($cpf) {
        echo "<pre>";
        //$funct = self::$soapClient->__getTypes();
        $func = self::$soapClient->__getFunctions();
        print_r($func);
        die;
    }

    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
    public function getAllUf() {

        $xml = self::$soapClient->lerDNE();
        $xml = $this->xmlToArray($xml);

        foreach ($xml["NODELIST"] as $value) {
            $result[$value['sg_uf']] = $value['sg_uf'];
        }
        return $result;
    }

    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
    public function getCidadesByUf($sgUf) {

        if ($sgUf) {
            $xml = self::$soapClient->lerDNE($sgUf);
            $xml = $this->xmlToArray($xml);

            foreach ($xml["NODELIST"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
    public function getBairrosByCoCidade($sgUf, $noCidade) {

        if ($sgUf && $noCidade) {

            $xml = self::$soapClient->lerDNE($sgUf, $noCidade);
            $xml = $this->xmlToArray($xml);

            foreach ($xml["NODELIST"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
    public function getLogradouroByBairro($sgUf, $noCidade, $noBairro) {

        if ($sgUf && $noCidade && $noBairro) {
            $xml = self::$soapClient->lerDNE($sgUf, $noCidade, $noBairro);
            $xml = $this->xmlToArray($xml);

            foreach ($xml["NODELIST"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
    public function getCepByLogradouro($sgUf, $noCidade, $noBairro, $noLogradouro) {

        if ($sgUf && $noCidade && $noBairro && $noLogradouro) {
            $xml = self::$soapClient->lerDNE($sgUf, $noCidade, $noBairro, $noLogradouro);
            $xml = $this->xmlToArray($xml);

            foreach ($xml["NODELIST"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
    public function getLogradouroByCep($cep) {

        if ($cep) {
            $xml = self::$soapClient->lerDNE(NULL, NULL, NULL, NULL, $cep);
            $xml = $this->xmlToArray($xml);

            foreach ($xml["NODELIST"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de UF
     *
     * @return mixed
     */
    public function getAllTipoLogradouro() {
        return null;
    }

}

?>