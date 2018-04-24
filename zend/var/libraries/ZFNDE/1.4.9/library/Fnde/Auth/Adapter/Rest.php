<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rest
 *
 * @author Leandro
 */
class Fnde_Auth_Adapter_Rest implements Zend_Auth_Adapter_Interface {

    /**
     * String do arquivo XML que será enviado
     *
     * @var string
     */
    protected $_xmlStringRequest = "<?xml version='1.0' encoding='iso-8859-1'?>
    <request>
      <header>
        <app>string</app>
        <version>string</version>
        <created>2010-01-11T16:24:06.98</created>
      </header>
      <body>
        <params>
          <sg_aplicacao>%s</sg_aplicacao>
          <login>%s</login>
          <senha>%s</senha>
        </params>
      </body>
    </request>";
    protected $_xmlUserInfoStringRequest = "<?xml version='1.0' encoding='iso-8859-1'?>
    <request>
      <header>
        <app>string</app>
        <version>string</version>
        <created>2010-01-11T16:24:06.98</created>
      </header>
      <body>
        <params>
          <login>%s</login>
        </params>
      </body>
    </request>";

    /**
     * Rest Client
     *
     * @var Zend_Rest_Client
     */
    protected $_restClient = null;

    /**
     * Rest Client
     *
     * @var Zend_Rest_Client
     */
    protected $_restClientUserInfo = null;

    /**
     * $_siglaAplicacao - Sigla da aplicação
     *
     * @var string
     */
    protected $_siglaAplicacao = null;

    /**
     * $_identity - Identity value
     *
     * @var string
     */
    protected $_identity = null;

    /**
     * $_credential - Credential values
     *
     * @var string
     */
    protected $_credential = null;

    /**
     * $_authenticateResultInfo
     *
     * @var array
     */
    protected $_authenticateResultInfo = null;

    /**
     * $_resultRow - Results of webservice authentication
     *
     * @var array
     */
    protected $_resultRow = null;

    /**
     * __construct() - Sets configuration options
     *
     * @param  Zend_Db_Adapter_Abstract $restClient
     * @param  string                   $identityParam
     * @param  string                   $credentialParam
     * @return void
     */
    public function __construct(Zend_Rest_Client $restClient, Zend_Rest_Client $restClientUserInfo = NULL) {
        $this->_restClient = $restClient;
        $this->_restClientUserInfo = $restClientUserInfo;
    }

    /**
     * setSiglaAplicacao() - set the value to be used as the siglaAplicacao
     *
     * @param  string $value
     * @return Fnde_Auth_Adapter_Rest Provides a fluent interface
     */
    public function setSiglaAplicacao($value) {
        $this->_siglaAplicacao = $value;
        return $this;
    }

    /**
     * setIdentity() - set the value to be used as the identity
     *
     * @param  string $value
     * @return Fnde_Auth_Adapter_Rest Provides a fluent interface
     */
    public function setIdentity($value) {
        $this->_identity = $value;
        return $this;
    }

    /**
     * setCredential() - set the credential value to be used
     *
     * @param  string $credential
     * @return Fnde_Auth_Adapter_Rest Provides a fluent interface
     */
    public function setCredential($credential) {
        $this->_credential = $credential;
        return $this;
    }

    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.  This method is called to
     * attempt an authentication.  Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     *
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        $restResult = $this->_authenticateCallMethod();
        return $this->_authenticateValidateResult($restResult);
    }

    /**
     * _authenticateCallMethod() - This method accepts a Zend_Db_Select object and
     * performs a query against the database with that object.
     *
     * @param Zend_Db_Select $dbSelect
     * @throws Zend_Auth_Adapter_Exception - when an invalid select
     *                                       object is encountered
     * @return array
     */
    protected function _authenticateCallMethod() {
        try {
            $this->_restClient->xml(sprintf($this->_xmlStringRequest, $this->_siglaAplicacao, $this->_identity, $this->_credential));
            $resultIdentities = $this->_restClient->post();
        } catch (Exception $e) {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            require_once 'Zend/Auth/Adapter/Exception.php';
            throw new Zend_Auth_Adapter_Exception('The supplied parameters to Fnde_Auth_Adapter_Rest failed to '
                    . 'call service, please check table and column names '
                    . 'for validity.', 0, $e);
        }
        return $resultIdentities;
    }

    /**
     * _authenticateValidateResult() - This method attempts to make
     * certain that only one record was returned in the SimpleXMLElement
     *
     * @param array $resultIdentities
     * @return true|Zend_Auth_Result
     */
    protected function _authenticateValidateResult(Zend_Rest_Client_Result $resultIdentities) {
        if ($resultIdentities->status->result == 0) {
            $errors = $resultIdentities->status->error;

//      switch ((string)$errors->message->code) {
//        case '1':
//          $code = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
//          break;
//      }

            $this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE;
            $this->_authenticateResultInfo['messages'][] = (string) $errors->message->text;
            return $this->_authenticateCreateAuthResult();
        } elseif ($resultIdentities->status->result == 1) {
            $message = $resultIdentities->status->message;
            switch ((string) $message->code) {
                case 'OK':
                    $code = Zend_Auth_Result::SUCCESS;
                    break;
            }
            $this->_authenticateResultInfo['code'] = $code;
            $this->_authenticateResultInfo['messages'][] = (string) $message->text;

            $body = (array) $resultIdentities->body;
            if (is_array($body['perfil'])) {
                $body['credentials'] = $body['perfil'];
            } else {
                $body['credentials'] = array($body['perfil']);
            }
            unset($body['perfil']);
            $this->_resultRow = (array) $body;

            return $this->_authenticateCreateAuthResult();
        }

        return true;
    }

    /**
     * _authenticateCreateAuthResult() - Creates a Zend_Auth_Result object from
     * the information that has been collected during the authenticate() attempt.
     *
     * @return Zend_Auth_Result
     */
    protected function _authenticateCreateAuthResult() {
        return new Zend_Auth_Result(
                        $this->_authenticateResultInfo['code'],
                        $this->_authenticateResultInfo['identity'],
                        $this->_authenticateResultInfo['messages']
        );
    }

    /**
     * getResultRowObject() - Returns the result row as a stdClass object
     *
     * @param  string|array $returnColumns
     * @param  string|array $omitColumns
     * @return stdClass|boolean
     */
    public function getResultRowObject($returnColumns = null, $omitColumns = null) {
        if (!$this->_resultRow) {
            return false;
        }

        $userInfo = (array) $this->_getUserInfo($this->_identity)->body;
        foreach($userInfo as $k => &$it) $it = (string) $it;
        $returnObject = new stdClass();

        //adiciona o nome de usuário ao resultObject
        $returnObject->username = strtoupper($this->_identity);

        if (null !== $returnColumns) {

            $availableColumns = array_keys($this->_resultRow);
            foreach ((array) $returnColumns as $returnColumn) {
                if (in_array($returnColumn, $availableColumns)) {
                    $returnObject->{$returnColumn} = $this->_resultRow[$returnColumn];
                }
            }

            $availableColumns = array_keys($userInfo);
            foreach ((array) $returnColumns as $returnColumn) {
                if (in_array($returnColumn, $availableColumns)) {
                    $returnObject->{$returnColumn} = $userInfo[$returnColumn];
                }
            }
        } elseif (null !== $omitColumns) {
            $omitColumns = (array) $omitColumns;
            foreach ($this->_resultRow as $resultColumn => $resultValue) {
                if (!in_array($resultColumn, $omitColumns)) {
                    $returnObject->{$resultColumn} = $resultValue instanceof SimpleXMLElement ? (array) $resultValue : $resultValue;
                }
            }

            foreach ($userInfo as $resultColumn => $resultValue) {
                if (!in_array($resultColumn, $omitColumns)) {
                    $returnObject->{$resultColumn} = $resultValue instanceof SimpleXMLElement ? (array) $resultValue : $resultValue;
                }
            }
        } else {

            foreach ($this->_resultRow as $resultColumn => $resultValue) {
                $returnObject->{$resultColumn} = $resultValue;
            }

            foreach ($userInfo as $resultColumn => $resultValue) {
                $returnObject->{$resultColumn} = $resultValue instanceof SimpleXMLElement ? (array) $resultValue : $resultValue;
            }
        }
        foreach($returnObject->credentials as &$item) $item = (string) $item;
        return $returnObject;
    }
    
    /**
     *
     * @param string $identity
     * @return SimpleXMLElement
     */
    protected function _getUserInfo($identity) {
        try {
            $this->_restClientUserInfo->xml(sprintf($this->_xmlUserInfoStringRequest, $identity));

            $resultIdentities = $this->_restClientUserInfo->post();
            return $resultIdentities;
        } catch (Exception $e) {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            require_once 'Zend/Auth/Adapter/Exception.php';
            throw new Zend_Auth_Adapter_Exception('The supplied parameters to Fnde_Auth_Adapter_Rest failed to '
                    . 'call service, please check table and column names '
                    . 'for validity.', 0, $e);
        }
        return $resultIdentities;
    }

}
