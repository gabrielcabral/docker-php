<?php
/**
 * Arquivo de classe Segweb FNDE.
 *
 * $Rev:: 239                  $
 * $Date:: 2011-03-30 10:54:07#$
 * $Author:: WalkerAlencar     $
 *
 * @package Fnde
 * @category Model
 * @name Receita
 * @author Walker de Alencar Oliveira
 */

/**
 * Classe que consome o Webservice da Receita
 *
 * @version $Id: Receita.php 239 2011-03-30 13:54:07Z WalkerAlencar $
 */
class Fnde_Model_Receita extends Fnde_Model_Service_Rest {
    const XML_REQUEST_CPF = '<?xml version="1.0" encoding="iso-8859-1"?>
<request>%s<body><params><cpf>%s</cpf><cpfConsultante>%s</cpfConsultante><ttl>%s</ttl></params></body></request>';
    const XML_REQUEST_CNPJ = '<?xml version="1.0" encoding="iso-8859-1"?>
<request>%s<body><params><cnpj>%s</cnpj><cpfConsultante>%s</cpfConsultante><cache>%s</cache></params></body></request>';

    private $lastResponse = null;
    
    public function __construct() {
        parent::__construct();
        if (empty($this->_config['webservices']['receita']['uri'])) {
            throw new Zend_Rest_Client_Exception('O URI do serviço da Receita deve conter no arquivo de configuração [application.ini] webservices.receita.uri = http://example ');
        }
        $this->setUrl($this->_config['webservices']['receita']['uri']);
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
    protected function getResponseBody($url, $xml, $method) {
        try {
            $httpClient = $this->getHttpClient();
            $httpClient->setUri($url);
            $httpClient->setMethod('POST');
            $httpClient->setParameterPost('method', $method);
            $httpClient->setParameterPost('xml', $xml);
            $httpClient->setConfig(
                array(
                    'keepalive' => true,
                    'maxredirects' => 0,
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
    public function consultarCpf($cpf, $cpfConsultante, $ttl = 0) {
	$ttl = (int) $ttl;
        $response = $this->getResponseBody(
            $this->getUrl() . 'pessoa/fisica/',
            sprintf(self::XML_REQUEST_CPF, $this->getHeader(), $this->filter($cpf),$this->filter($cpfConsultante), $ttl),
            'consultarCpf'
        );
        $response = simplexml_load_string($response);
        if ((int) $response->status->result == '0') {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        return array('result' => '1', 'content' => self::xmlToArray($response->body));
    }
    
    /**
     * Consulta CNPJ e traz todos os dados disponíveis na receita.
     *
     * @param string $cnpj
     * @param string $cpfConsultante
     * @return array
     */
    public function consultarCnpj($cnpj, $cpfConsultante, $cache = false) {
	$cache = ($cache)?'true':'false';
        $response = $this->getResponseBody(
            $this->getUrl() . 'pessoa/juridica/',
            sprintf(self::XML_REQUEST_CNPJ, $this->getHeader(), $this->filter($cnpj),$this->filter($cpfConsultante),$cache),
            'consultarCnpj'
        );
        $response = simplexml_load_string($response);
        if ((int) $response->status->result == '0') {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        return array('result' => '1', 'content' => self::xmlToArray($response->body));
    }


}
