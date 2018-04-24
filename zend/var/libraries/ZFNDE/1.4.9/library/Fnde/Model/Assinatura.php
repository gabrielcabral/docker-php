<?php

/**
 * Classe que consome os serviços do WS-Assinatura Eletronica
 * 
 * @category Fnde
 * @package Fnde_Model
 * @subpackage Assinatura
 * @author Alberto Guimarães Viana <alberto.viana@fnde.gov.br>
 */
class Fnde_Model_Assinatura extends Fnde_Model_Service_Rest implements Fnde_Model_Assinatura_Interface
{

    CONST XML_DOCUMENTO_CREATE = '<?xml version="1.0" encoding="iso-8859-1"?>
        <request>
          %s
          <body>
            <params>
              <tp_documento>%s</tp_documento>
              %s
              <co_aplicacao>%s</co_aplicacao>
              <ds_login>%s</ds_login>
              <qt_assinatura>%s</qt_assinatura>
              <no_arquivo>%s</no_arquivo>
              <tp_arquivo>%s</tp_arquivo>
            </params>
          </body>
        </request>';
    CONST XML_DOCUMENTO_UPDATE = '<?xml version="1.0" encoding="iso-8859-1"?>
        <request>
          %s
          <body>
            <params>
              <nu_seq_documento>%s</nu_seq_documento>
              <co_aplicacao>%s</co_aplicacao>
              <ds_login>%s</ds_login>
              <no_arquivo>%s</no_arquivo>
              <tp_arquivo>%s</tp_arquivo>
            </params>
          </body>
        </request>';
    CONST XML_DOCUMENTO_INFO = '<?xml version="1.0" encoding="iso-8859-1"?>
        <request>
          %s
          <body>
            <params>
              <nu_seq_documento>%s</nu_seq_documento>
            </params>
          </body>
        </request>';
    CONST XML_ASSINATURA_SIGN = '<?xml version="1.0" encoding="iso-8859-1"?>
        <request>
          %s
          <body>
            <params>
              <nu_seq_documento>%s</nu_seq_documento>
              <ds_login>%s</ds_login>
            </params>
          </body>
        </request>';
    CONST XML_AUTENTICIDADE = '<?xml version="1.0" encoding="iso-8859-1"?>
        <request>
          %s
          <body>
            <params>
              <ds_assinatura>%s</ds_assinatura>
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
        if (empty($this->_config['webservices']['assinatura']['uri'])) {
            throw new Zend_Rest_Client_Exception('O URI do serviço Assinatura Eletrônica deve conter no arquivo de 
                configuração [application.ini] webservices.assinatura.uri = http://example ');
        }

        $this->getHttpClient()->setConfig(array('timeout' => 30));
        $this->setUrl($this->_config['webservices']['assinatura']['uri']);
    }

    /**
     * Cria um documento 
     * @param string $dsLogin
     * @param int $qtAssinatura
     * @param string $file
     * @param string $filename
     * @param string $mimeType
     * @param string $tpDocumento
     * @param string $nuDocumento
     * @param string $coAplicacao
     * @throws Zend_Http_Exception
     * @return string|array 
     */
    public function create($dsLogin, $qtAssinatura, $file, $filename, $mimeType, $tpDocumento, $nuDocumento = '',
                           $coAplicacao = null)
    {
        try {

            if (!empty($nuDocumento)) {
                $nuDocumento = "<nu_documento>{$nuDocumento}</nu_documento>";
            }

            if (is_null($coAplicacao)) {
                $coAplicacao = $this->_config['app']['name'];
            }

            $xml = sprintf(
                    self::XML_DOCUMENTO_CREATE, $this->getHeader(), $tpDocumento, $nuDocumento, $coAplicacao, $dsLogin,
                    $qtAssinatura, $filename, $mimeType
            );

            $this->getHttpClient()->setParameterPost('method', 'create');
            $this->getHttpClient()->setParameterPost('xml', $xml);
            $this->getHttpClient()->setFileUpload($file, 'arquivo');
            $this->getHttpClient()->setUri($this->getUrl() . 'webservice/documento');

            $response = $this->getHttpClient()->request(Zend_Http_Client::POST);

            $data = simplexml_load_string($response->getBody());

            if ((int) $data->status->result === 0) {
                $result = self::xmlToArray($data->status->error);
                return array('result' => 0, 'message' => $result['message']);
            }

            return (string) $data->body->nu_seq_documento;
        } catch (Exception $e) {
            throw new Zend_Http_Exception($e->getMessage());
        }
    }

    /**
     * Atualizar um documento
     * @param int $nuSeqDocumento
     * @param string $dsLogin
     * @param string $file
     * @param string $filename
     * @param string $mimeType
     * @param string $coAplicacao
     * @throws Zend_Http_Exception
     * @return boolean|array 
     */
    public function update($nuSeqDocumento, $dsLogin, $file, $filename, $mimeType, $coAplicacao = null)
    {
        try {

            if (is_null($coAplicacao)) {
                $coAplicacao = $this->_config['app']['name'];
            }

            $xml = sprintf(
                    self::XML_DOCUMENTO_UPDATE, $this->getHeader(), $nuSeqDocumento, $coAplicacao, $dsLogin, $filename,
                    $mimeType
            );

            $this->getHttpClient()->setParameterPost('method', 'update');
            $this->getHttpClient()->setParameterPost('xml', $xml);
            $this->getHttpClient()->setFileUpload($file, 'arquivo');
            $this->getHttpClient()->setUri($this->getUrl() . 'webservice/documento');

            $response = $this->getHttpClient()->request(Zend_Http_Client::POST);

            $data = simplexml_load_string($response->getBody());

            if ((int) $data->status->result === 0) {
                $result = self::xmlToArray($data->status->error);
                return array('result' => 0, 'message' => $result['message']);
            }

            return true;
        } catch (Exception $e) {
            throw new Zend_Http_Exception($e->getMessage());
        }
    }

    /**
     * Recupera as informações de um documento
     * @param string $nuSeqDocumento
     * @throws Zend_Http_Exception
     * @return array 
     */
    public function info($nuSeqDocumento)
    {
        try {

            if (empty($nuSeqDocumento)) {
                throw new Exception('Deve ser informado um valor para o número do documento', E_ERROR);
            }

            $xml = sprintf(self::XML_DOCUMENTO_INFO, $this->getHeader(), $nuSeqDocumento);

            $this->getHttpClient()->setParameterPost('method', 'info');
            $this->getHttpClient()->setParameterPost('xml', $xml);
            $this->getHttpClient()->setUri($this->getUrl() . 'webservice/documento');

            $response = $this->getHttpClient()->request(Zend_Http_Client::POST);
            $data = simplexml_load_string($response->getBody());

            if ((int) $data->status->result === 0) {
                $result = self::xmlToArray($data->status->error);
                return array('result' => 0, 'message' => $result['message']);
            }

            return Fnde_Util::xmlToArray($data->body);
        } catch (Exception $e) {
            throw new Zend_Http_Exception($e->getMessage());
        }
    }

    /**
     * Recupera os tipos de documento existentes no sistema Documenta
     * @throws Zend_Http_Exception
     * @return array 
     */
    public function getTipoDocumento()
    {
        try {
            $this->getHttpClient()->setParameterPost('method', 'getTipoDocumento');
            $this->getHttpClient()->setUri($this->getUrl() . 'webservice/documento');

            $response = $this->getHttpClient()->request(Zend_Http_Client::POST);
            $data = simplexml_load_string($response->getBody());

            if ((int) $data->status->result === 0) {
                $result = self::xmlToArray($data->status->error);
                return array('result' => 0, 'message' => $result['message']);
            }

            $arrResult = array();
            foreach ($data->body->row as $value) {
                $arrResult[utf8_decode((string) $value->tp_documento)] = utf8_decode((string) $value->ds_documento);
            }

            return $arrResult;
        } catch (Exception $e) {
            throw new Zend_Http_Exception($e->getMessage());
        }
    }

    /**
     * Assina um documento
     * @param int $nuSeqDocumento
     * @param string $dsLogin
     * @throws Zend_Http_Exception
     * @return array 
     */
    public function sign($nuSeqDocumento, $dsLogin)
    {
        try {
            $xml = sprintf(self::XML_ASSINATURA_SIGN, $this->getHeader(), $nuSeqDocumento, $dsLogin);

            $this->getHttpClient()->setParameterPost('method', 'sign');
            $this->getHttpClient()->setParameterPost('xml', $xml);
            $this->getHttpClient()->setUri($this->getUrl() . 'webservice/assinatura');

            $response = $this->getHttpClient()->request(Zend_Http_Client::POST);
            $data = simplexml_load_string($response->getBody());

            if ((int) $data->status->result === 0) {
                $result = self::xmlToArray($data->status->error);
                return array('result' => 0, 'message' => $result['message']);
            }

            return Fnde_Util::xmlToArray($data->body);
        } catch (Exception $e) {
            throw new Zend_Http_Exception($e->getMessage());
        }
    }

    /**
     * Verifica a autenticidade de um documento
     * @param string $dsAssinatura
     * @return string
     * @throws Zend_Http_Exception 
     */
    public function autenticidade($dsAssinatura)
    {
        try {
            $xml = sprintf(self::XML_AUTENTICIDADE, $this->getHeader(), $dsAssinatura);

            $this->getHttpClient()->setParameterPost('method', 'autenticidade');
            $this->getHttpClient()->setParameterPost('xml', $xml);
            $this->getHttpClient()->setUri($this->getUrl() . 'webservice/assinatura');

            $response = $this->getHttpClient()->request(Zend_Http_Client::POST);

            $data = simplexml_load_string($response->getBody());

            if ((int) $data->status->result === 0) {
                $result = self::xmlToArray($data->status->error);
                return array('result' => 0, 'message' => $result['message']);
            }

            return (string) $data->body->uri;
        } catch (Exception $e) {
            throw new Zend_Http_Exception($e->getMessage());
        }
    }

}