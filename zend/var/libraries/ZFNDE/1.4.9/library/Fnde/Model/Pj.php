<?php

/**
 * Arquivo de classe Pessoa Juridica MEC.
 *
 * @package Fnde
 * @category Fnde_Model
 * @name Pessoa Juridica (Pj)
 * @author Daniel Wilson de Alvarenga <daniel.alvarenga@fnde.gov.br>
 */

/**
 * Classe que consome o Pessoa Juridica (Pj)
 *
 * @version $Id$
 */
class Fnde_Model_Pj extends Fnde_Model_Service_Mec_Soap {

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

        if (empty($this->_config['webservices']['pj']['uri'])) {
            throw new Zend_Soap_Client_Exception('O URI do serviço Pessoa Juridica (PJ) deve conter no arquivo de configuração 
                [application.ini] webservices.pj.uri = http://example ');
        }

        $this->setUrl($this->_config['webservices']['pj']['uri']);
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
    public function getTest() {
//        echo "<pre>";
//        $types = self::$soapClient->__getTypes();
//        $func  = self::$soapClient->__getFunctions();
//        print_r($types);
//        $cnpj = "08233534000170";
//        $funct1 = $this->solicitarDadosResumidoPessoaJuridicaPorCnpj($cnpj);
//        print_r($funct1);
//        $funct2 = $this->solicitarDadosReceitaPorCnpj($cnpj);
//        print_r($funct2);
//        $funct3 = $this->solicitarListaFiliasPessoaJuridicaPorCnpj($cnpj);
//        print_r($funct3);
//        $funct4 = $this->solicitarDadosPessoaJuridicaPorCnpj($cnpj);
//        print_r($funct4);
//        $funct5 = $this->solicitarDadosEnderecoPessoaJuridicaPorCnpj($cnpj);
//        print_r($funct5);
//        $funct6 = $this->solicitarDadosContatoPessoaJuridicaPorCnpj($cnpj);
//        print_r($funct6);
//        $funct7 = $this->solicitarDadosSocioPessoaJuridicaPorCnpj($cnpj);
//        print_r($funct7);
//        die;
    }

    /**
     * Captura a lista de CNPJ
     *
     * @return mixed
     */
    public function solicitarDadosResumidoPessoaJuridicaPorCnpj($cnpj) {

        $xml = self::$soapClient->solicitarDadosResumidoPessoaJuridicaPorCnpj($cnpj);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["PESSOA"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de CNPJ
     *
     * @return mixed
     */
    public function solicitarDadosReceitaPorCnpj($cnpj) {

        $xml = self::$soapClient->solicitarDadosReceitaPorCnpj($cnpj);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["PESSOA"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de CNPJ
     *
     * @return mixed
     */
    public function solicitarListaFiliasPessoaJuridicaPorCnpj($cnpj) {

        $xml = self::$soapClient->solicitarListaFiliasPessoaJuridicaPorCnpj($cnpj);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["filiais"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de CNPJ
     *
     * @return mixed
     */
    public function solicitarDadosPessoaJuridicaPorCnpj($cnpj) {

        $xml = self::$soapClient->solicitarDadosPessoaJuridicaPorCnpj($cnpj);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["PESSOA"] as $key => $value) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Captura a lista de CNPJ
     *
     * @return mixed
     */
    public function solicitarDadosEnderecoPessoaJuridicaPorCnpj($cnpj) {

        $xml = self::$soapClient->solicitarDadosEnderecoPessoaJuridicaPorCnpj($cnpj);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["ENDERECOS"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de CNPJ
     *
     * @return mixed
     */
    public function solicitarDadosContatoPessoaJuridicaPorCnpj($cnpj) {

        $xml = self::$soapClient->solicitarDadosContatoPessoaJuridicaPorCnpj($cnpj);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["CONTATOS"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Captura a lista de CNPJ
     *
     * @return mixed
     */
    public function solicitarDadosSocioPessoaJuridicaPorCnpj($cnpj) {

        $xml = self::$soapClient->solicitarDadosSocioPessoaJuridicaPorCnpj($cnpj);
        $xml = $this->xmlToArray($xml);

        if (isset($xml)) {
            foreach ($xml["SOCIOS"] as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

}

?>