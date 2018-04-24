<?php
/**
 * Arquivo de classe Segweb FNDE.
 *
 * $Rev:: 346                  $
 * $Date:: 2012-06-12 15:31:26#$
 * $Author:: 88602010125       $
 *
 * @package Fnde
 * @category Model
 * @name Receita
 * @author Walker de Alencar Oliveira
 */

/**
 * Classe que consome o Webservice da Receita
 *
 * @version $Id: Correios.php 346 2012-06-12 18:31:26Z 88602010125 $
 */
class Fnde_Model_Correios extends Fnde_Model_Service_Rest {
    
    private $lastResponse = null;
    
    public function __construct() {
        parent::__construct();
        if (empty($this->_config['webservices']['cep']['uri'])) {
            throw new Zend_Rest_Client_Exception('O URI do serviço dos Correios deve conter no arquivo de configuração [application.ini] webservices.correios.uri = http://example ');
        }
        $this->setUrl($this->_config['webservices']['cep']['uri']);
    }
    
    public function getLastResponse(){
        return $this->lastResponse;
    }
    /**
     * @param string $url URL do Webservice
     * @param string $xml XML Request
     * @param string $method Método disponível no Webservice Rest
     * @return string
     * @throws Zend_Http_Exception
     */
    protected function getResponseBody($url, $cep) {
        try {
            $httpClient = $this->getHttpClient();
            $httpClient->setUri($url . 'cep/'. $cep );
            $httpClient->setMethod('GET');
            $httpClient->setConfig(
                array(
                    'keepalive' => true,
                    'maxredirects' => 99,
                    'timeout' => 30
                )
            );
            $request = $httpClient->request();
            $response = $request->getBody();
        } catch (Exception $e) {
            throw new Zend_Http_Exception('O serviço da Receita pode estar indisponível');
        }
        $this->lastResponse = $response;
        return $response;
    }
    
    private function filter($value) {
        return str_replace('-', '', str_replace('.', '', $value));
    }
    /**
     * Consulta CPF e traz todos os dados disponíveis na receita.
     *
     * @param string $cpf
     * @param string $cpfConsultante
     * @return array
     */
    public function consultarCep($Cep) {
        $response = $this->getResponseBody(
            $this->getUrl(),
            $this->filter($Cep)
        );
        $response = simplexml_load_string($response);
        if ((int) $response->status->result == '0') {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        return array('result' => '1', 'content' => self::xmlToArray($response->body));
    }
    

}
