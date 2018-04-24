<?php

/**
 * Model do Idm
 * @author Daniel Wilson de Alvarenga <daniel.alvarenga@fnde.gov.br>
 * 
 */
class Fnde_Model_Idm extends Fnde_Model_Service_Rest {

    const XML_GET_INTERNAL_ROLE =
            '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
        %s
        <body>
            <params>
                <id_usuario>%s</id_usuario>
                <sistema>%s</sistema>
            </params>
        </body>
        </request>';
    const XML_GET_ENTITY =
            '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
        %s
        <body>
            <params>
                <id_usuario>%s</id_usuario>
            </params>
        </body>
    </request>';
    const XML_GET_EXTERNAL_ROLE =
            '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
        %s
        <body>
            <params>
                <id_usuario>%s</id_usuario>
                <sistema>%s</sistema>
                <id_entidade>%s</id_entidade>
            </params>
        </body>
    </request>';
    const XML_GET_ENTITY_BY_SYSTEM =
            '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
        %s
    <body>
        <params>
            <id_usuario>%s</id_usuario>
            <sistema>%s</sistema>
        </params>
    </body>
    </request>';
    const XML_GET_USER_BY_DN =
            '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
        %s
    <body>
        <user_dn>%s</user_dn>
    </body>
    </request>';
    const XML_LIST_USER_DN_BY_ID =
            '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
        %s
    <body>
        <user_id>%s</user_id>
    </body>
    </request>';
    const XML_SEARCH_USER_BY_APP = '<?xml version="1.0" encoding="iso-8859-1"?>
<request>
  %s
  <body>
    <system>%s</system>
    <query>%s</query>
    <status>%s</status>
  </body>
</request>';
    const ROLE_INTERNAL = 'internal';
    const ROLE_EXTERNAL = 'external';

    protected static $ASSOC_ATTR = array("user_dn" => "userDn",
        "user_id" => "userId",
        "name" => "nome",
        "user_type" => "tipo",
        "cpf" => "cpf",
        "descricao_lotacao" => "lotacaoDescricao",
        "state" => "estado",
        "city" => "cidade",
        "organizational_unit" => "lotacaoSigla",
        "company" => "empresa",
        "fone" => "fone",
        "status" => "status",
        "email" => "email");

    /**
     * @var string
     */
    protected $_idUsuario;

    public function __construct() {
        parent::__construct();
        if (empty($this->_config['webservices']['idm']['uri'])) {
            throw new Zend_Rest_Client_Exception('O URI do serviço do IDM deve conter no arquivo de configuração [application.ini] webservices.idm.uri = http://example ');
        }
        $this->setUrl($this->_config['webservices']['idm']['uri']);
    }

    /**
     * Seta o id do usuário
     * @param string $idUsuario
     * @return Fnde_Model_Idm 
     */
    public function setIdUsuario($idUsuario) {
        $this->_idUsuario = $idUsuario;
        return $this;
    }

    /**
     * Retorna o id do usuário
     * @return string
     */
    public function getIdUsuario() {
        return $this->_idUsuario;
    }

    /**
     * @param string $xml
     * @param string $item
     * @return array
     */
    protected function getValues($xml, $item, $msg = null) {
        $array = $this->xmlToArray($xml);
        if (!empty($array['body'][$item])) {
            if (is_array($array['body'][$item])) {
                return $array['body'][$item];
            } else {
                return array($array['body'][$item]);
            }
        } else {
            throw new Zend_Rest_Client_Exception(empty($msg) ? 'Não existem itens' : $msg);
        }
    }

    /**
     * @param string $url URL do Webservice
     * @param string $xml XML Request
     * @param string $method Método disponível no Webservice Rest
     * @return string
     * @throws Zend_Http_Exception
     */
    protected function getResponseBody($url, $xml, $method) {
        $httpClient = $this->getHttpClient();
        $httpClient->setUri($url);
        $httpClient->setMethod('POST');
        $httpClient->setParameterPost('method', $method);
        $httpClient->setParameterPost('xml', $xml);
        $request = $httpClient->request();
        try {
            $response = $request->getBody();
        } catch (Exception $e) {
            throw new Zend_Http_Exception('O serviço de autenticação e/ou autorização pode estar indisponível');
        }
        return $response;
    }

    /**
     * Retorna todas as entidadas que o usuário possue pelo menos um role
     * @param Sigla da aplicação string $application
     * @return array
     */
    public function getEntity($application = null) {
        if (empty($application)) {
            $application = $this->_app;
            if (empty($application)) {
                $application = $this->getApp();
            }
        }

        $url = $this->getUrl() . 'webservice/user/';
        $method = 'getEntityBySystem';
        $xml = sprintf(self::XML_GET_ENTITY_BY_SYSTEM, $this->getHeader(), $this->_idUsuario, $application);
        $this->getResponseBody($url, $xml, $method);
        $response = $this->getResponseBody($url, $xml, $method);
        $arrayResponse = $this->getValues($response, 'entidade', 'O usuário não possui entidades para essa aplicação');
        return $arrayResponse;
    }

    /**
     * @param const $type
     * @param string $idEntity
     * @param string $application
     * @return array
     */
    public function getRole($type, $application = null, $idEntity = null) {
        if (empty($application)) {
            $application = $this->_app;
            if (empty($application)) {
                $application = $this->getApp();
            }
        }
        $url = $this->getUrl() . 'webservice/user/';

        if ($type == self::ROLE_EXTERNAL) {
            $xml = sprintf(self::XML_GET_EXTERNAL_ROLE, $this->getHeader(), $this->_idUsuario, $application, $idEntity);
            $method = 'getExternalRole';
            if (empty($idEntity)) {
                throw new Zend_Rest_Exception('Para acessar roles externas a entidade não pode estar vazia');
            }
        } elseif ($type == self::ROLE_INTERNAL) {
            $xml = sprintf(self::XML_GET_INTERNAL_ROLE, $this->getHeader(), $this->_idUsuario, $application);
            $method = 'getInternalRole';
        }

        $response = $this->getResponseBody($url, $xml, $method);
        try {
            $arrayResponse = $this->getValues($response, 'perfil', 'O usuário não possui roles com esses parâmetros');
        } catch (Zend_Rest_Client_Exception $e) {
            $arrayResponse = array();
        }
        $arrayResponse = array_map('strtolower', $arrayResponse);
        return $arrayResponse;
    }

    /**
     * Retorna todas as entidades vinculadas ao usuário menos que não tenham nenhuma rowler associada
     * @return array
     */
    public function getAllEntities() {
        $xml = sprintf(self::XML_GET_ENTITY, $this->getHeader(), $this->_idUsuario);
        $url = $this->getUrl() . 'webservice/user/';
        $method = 'getEntity';
        $response = $this->getResponseBody($url, $xml, $method);
        $arrayResponse = $this->getValues($response, 'entidade');
        return $arrayResponse;
    }

    /**
     * Retorna um array com as informações do usuario
     * @return array
     */
    public function getInfo() {
        $xmlResponse = $this->getResponseBody(
                $this->getUrl() . 'webservice/security/', 
                sprintf(self::XML_LIST_USER_DN_BY_ID, $this->getHeader(), $this->getIdUsuario()),
                'listUserDnById'
        );
        $response = $this->xmlToArray($xmlResponse);
        if (isset($response['body'])) {
            $dn = $response['body']['user_dn'];
        }
        $xml = sprintf(self::XML_GET_USER_BY_DN, $this->getHeader(), $dn);
        $url = $this->getUrl() . 'webservice/security/';
        $method = 'getUserByDn';

        $userInfo = new stdClass();
        $xmlResponse = $this->getResponseBody($url, $xml, $method);
        $response = $this->xmlToArray($xmlResponse);
        if (isset($response['body'])) {
            foreach (self::$ASSOC_ATTR as $field => $attr) {
                $userInfo->$attr = $response['body'][$field];
            }
            $this->setIdUsuario($userInfo->cpf);
            $userInfo->sistemas = $response['body']['sistemas']['sistema'];
            // Apenas roles que possui no sistema e internas
            if (is_array($userInfo->sistemas)){
                $userInfo->credentials = array();
                foreach($userInfo->sistemas as $value){
                    $userInfo->credentials += $this->getRole(self::ROLE_INTERNAL,$value);
                }
            } else {
                $userInfo->credentials = $this->getRole(self::ROLE_INTERNAL);
            }
        }

        return $userInfo;
    }

    public function searchUserByApplication($system, $query, $status = '') {
        $xml = sprintf(self::XML_SEARCH_USER_BY_APP, $this->getHeader(), $system, $query, $status);
        $url = $this->getUrl() . 'webservice/security/';
        $method = 'searchUserBySystem';
        $xmlResponse = $this->getResponseBody($url, $xml, $method);
        $response = $this->xmlToArray($xmlResponse);
        if (!isset($response['body']['user_found'])) {
            $response['body']['user_found'] = array();
        }
        $result = array();

        //Correção para quando o resultado é apenas uma linha
        if (is_null($response['body']['user_found'][0])) {
            $tmp = $response['body']['user_found'];
            $response['body']['user_found'] = array($tmp);
        }

        if (!empty($response['body']['user_found']))
            foreach ($response['body']['user_found'] as $item) {
                $userInfo = new stdClass();
                foreach ($item as $field => $value) {
                    if (array_key_exists($field, self::$ASSOC_ATTR)) {
                        $attr = self::$ASSOC_ATTR[$field];
                        $userInfo->$attr = $value;
                    }
                }
                $result[] = $userInfo;
            }
        return $result;
    }

}