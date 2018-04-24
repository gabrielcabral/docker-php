<?php

/**
 * Model que consome os serviços do WS-CAStor
 * 
 * @category Fnde
 * @package Fnde_Model
 * @subpackage Castor
 * @author Alberto Guimarães Viana <alberto.viana@fnde.gov.br>
 */
class Fnde_Model_Castor extends Fnde_Model_Service_Rest implements Fnde_Model_Castor_Interface
{
    const XML_INFO = '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
      %s
      <body>
        <params>
          <sg_aplicacao>%s</sg_aplicacao>
          <arquivo>
            %s
          </arquivo>
        </params>
      </body>
    </request>';

    /**
     * @throws Zend_Rest_Client_Exception
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        if (empty($this->_config['webservices']['castor']['uri'])) {
            throw new Zend_Rest_Client_Exception('O URI do serviço do CAStor deve conter no arquivo de configuração 
                [application.ini] webservices.castor.uri = http://example ');
        }

        $this->setUrl($this->_config['webservices']['castor']['uri']);
    }

    /**
     * Salva um arquivo no Castor
     * @param string $dsLogin - login do usuario que realizou o envio
     * @param resource $arquivo
     * @param string $nomeArquivo - nome do arquivo
     * @param string $extensao - extensao do arquivo
     * @param string $sgAplicacao - sigla da aplicacao
     * @param string $hashFile - hash do arquivo, o WS-CAStor valida se o arquivo esta integro comparando o hash
     * @param int $nuSeqArquivoMaster - numero do ultimo arquivo enviado quando o envio e realizado multipart
     * @param boolean $eof - end of file representa a ultima parte do arquivo a ser enviada
     * @throws Zend_Http_Exception
     * @return string - numero do arquivo no WS-CAStor.
     */
    public function write($dsLogin, $arquivo, $nomeArquivo, $extensao, $sgAplicacao = null, $hashFile = null,
                          $nuSeqArquivoMaster = 0, $eof = false)
    {
        try {
            
            if(is_null($sgAplicacao)) {
                $sgAplicacao = $this->_config['app']['name'];
            }

            $params = array(
                'sg_aplicacao' => $sgAplicacao,
                'ds_login' => $dsLogin,
                'nome_arquivo' => $nomeArquivo,
                'extensao' => $extensao,
                'nu_seq_arquivo_master' => $nuSeqArquivoMaster,
                'hash_md5' => $hashFile,
                'eof' => $eof
            );

            $this->getHttpClient()->setUri($this->getUrl() . 'write');
            $this->getHttpClient()->setParameterPost($params);
            $this->getHttpClient()->setFileUpload($arquivo, 'arquivo');

            $response = simplexml_load_string($this->getHttpClient()->request(Zend_Http_Client::POST)->getBody());

            $this->validateResponse($response);

            return (string) $response->body->nu_seq_arquivo;
        } catch (Exception $e) {
            throw new Zend_Http_Exception($e->getMessage());
        }
    }

    /**
     * Recupera um arquivo no WS-CAStor
     * @param string $nuSeqArquivo
     * @throws Zend_Http_Exception
     * @return Zend_Http_Response 
     */
    public function view($nuSeqArquivo, $coAplicacao = null)
    {
        try {
            
            if(is_null($coAplicacao)) {
                $coAplicacao = $this->_config['app']['name'];
            }
            
            $params = array(
                'sg_aplicacao' => $coAplicacao,
                'nu_seq_arquivo' => $nuSeqArquivo
            );

            $this->getHttpClient()->setUri($this->getUrl() . 'view');
            $this->getHttpClient()->setParameterGet($params);

            return $this->getHttpClient()->request(Zend_Http_Client::GET);
        } catch (Exception $e) {
            throw new Zend_Http_Exception($e->getMessage());
        }
    }

    /**
     * Recupera um arquivo no WS-CAStor
     * @param string $nuSeqArquivo
     * @param string $coAplicacao
     * @throws Zend_Http_Exception
     * @return string - contendo a mensagem do atributo status->message->text 
     */
    public function delete($nuSeqArquivo, $coAplicacao = null)
    {
        try {
            
            if(is_null($coAplicacao)) {
                $coAplicacao = $this->_config['app']['name'];
            }
            
            $params = array(
                'sg_aplicacao' => strtoupper($coAplicacao),
                'nu_seq_arquivo' => $nuSeqArquivo
            );

            $this->getHttpClient()->setUri($this->getUrl() . 'delete');
            $this->getHttpClient()->setParameterPost($params);

            $response = simplexml_load_string($this->getHttpClient()->request(Zend_Http_Client::POST)->getBody());

            $this->validateResponse($response);

            return (string) $response->status->message->text;
        } catch (Exception $e) {
            throw new Zend_Http_Exception($e->getMessage());
        }
    }

    /**
     * Recupera as informacoes do arquivo no WS-CAStor
     * @param string|array $nuSeqArquivo
     * @throws Zend_Http_Exception
     * @return array - com as informacoes do arquivo 
     */
    public function info($nuSeqArquivo)
    {
        try {
            $row = '';

            if (is_string($nuSeqArquivo)) {
                $row .= "<nu_seq_arquivo>{$nuSeqArquivo}</nu_seq_arquivo>";
            }

            if (is_array($nuSeqArquivo)) {
                foreach ($nuSeqArquivo as $value) {
                    $row .= "<nu_seq_arquivo>{$value}</nu_seq_arquivo>";
                }
            }

            $xml = sprintf(self::XML_INFO, $this->getHeader(), $this->getApp(), $row);

            $this->getHttpClient()->setUri($this->getUrl() . 'info');
            $this->getHttpClient()->setParameterPost(array('xml' => $xml));

            $response = simplexml_load_string($this->getHttpClient()->request(Zend_Http_Client::POST)->getBody());

            $this->validateResponse($response);

            $response = $this->xmlToArray($response);

            return $response['body']['row'];
        } catch (Exception $e) {
            throw new Zend_Http_Exception($e->getMessage());
        }
    }

    /**
     * Valida o retorno do serviço e se houve algum erro lança sua excessao
     * @param SimpleXMLElement $response
     * @throws Exception
     * @return void  
     */
    protected function validateResponse(SimpleXMLElement $response)
    {
        if ((int) $response->status->result === 0) {
            $result = $this->xmlToArray($response->status->error);
            throw new Zend_Http_Exception($result['message']['text'], $result['message']['code']);
        }
    }

}