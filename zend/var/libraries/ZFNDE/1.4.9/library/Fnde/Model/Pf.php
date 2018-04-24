<?php

/**
 * Arquivo de classe Pessoa Fisica MEC.
 *
 * @package Fnde
 * @category Fnde_Model
 * @name Pessoa Fisica (Pf)
 * @author Daniel Wilson de Alvarenga <daniel.alvarenga@fnde.gov.br>
 */

/**
 * Classe que consome o Pessoa Fisica (Pf)
 *
 * @version $Id$
 */
class Fnde_Model_Pf extends Fnde_Model_Service_Mec_Soap {

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

        if (empty($this->_config['webservices']['pf']['uri'])) {
            throw new Zend_Soap_Client_Exception('O URI do serviço Pessoa Fisica (PF) deve conter no arquivo de configuração 
                [application.ini] webservices.pf.uri = http://example ');
        }

        $this->setUrl($this->_config['webservices']['pf']['uri']);
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
     * Captura a lista de Cpf
     *
     * @return mixed
     */
    public function getTest($cpf) {
        echo "<pre>";
        //$funct = self::$soapClient->__getTypes();
        $func = self::$soapClient->__getFunctions();
        print_r($func);
        $funct1 = $this->solicitarDadosResumidoPessoaFisicaPorCpf($cpf);
        print_r($funct1);
        $funct2 = $this->solicitarDadosReceitaPorCpf($cpf);
        print_r($funct2);
        $funct3 = $this->solicitarDadosPessoaFisicaPorCpf($cpf);
        print_r($funct3);
        $funct4 = $this->solicitarDadosEnderecoPessoaFisicaPorCpf($cpf);
        print_r($funct4);
        $funct5 = $this->solicitarDadosContatoPessoaFisicaPorCpf($cpf);
        print_r($funct5);
        die;
    }

    /**
     * Captura a lista de Cpf
     *
     * @return mixed
     */
    public function solicitarDadosResumidoPessoaFisicaPorCpf($cpf) {

        $xml = self::$soapClient->solicitarDadosResumidoPessoaFisicaPorCpf($cpf);
        $xml = $this->xmlToArray($xml);
        
        if (isset($xml)) {
            foreach ($xml["PESSOA"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de Cpf
     *
     * @return mixed
     */
    public function solicitarDadosReceitaPorCpf($cpf) {

        $xml = self::$soapClient->solicitarDadosReceitaPorCpf($cpf);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["PESSOA"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de Cpf
     *
     * @return mixed
     */
    public function solicitarDadosPessoaFisicaPorCpf($cpf) {

        $xml = self::$soapClient->solicitarDadosPessoaFisicaPorCpf($cpf);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["PESSOA"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de Cpf
     *
     * @return mixed
     */
    public function solicitarDadosEnderecoPessoaFisicaPorCpf($cpf) {

        $xml = self::$soapClient->solicitarDadosEnderecoPessoaFisicaPorCpf($cpf);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["ENDERECO"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de Cpf
     *
     * @return mixed
     */
    public function solicitarDadosContatoPessoaFisicaPorCpf($cpf) {

        $xml = self::$soapClient->solicitarDadosContatoPessoaFisicaPorCpf($cpf);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["CONTATO"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

}

?>