<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Soap
 *
 * @author Leandro
 */
class Fnde_Auth_Adapter_Soap implements Zend_Auth_Adapter_Interface {

    /**
     * Soap Client
     *
     * @var Zend_Soap_Client
     */
    protected $_soapClient = null;

    /**
     * $_methodName - the methodName name to check
     *
     * @var string
     */
    protected $_methodName = null;

    /**
     * $_identityParam - the column to use as the identity
     *
     * @var string
     */
    protected $_identityParam = null;

    /**
     * $_credentialParams - columns to be used as the credentials
     *
     * @var string
     */
    protected $_credentialParam = null;

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
     * @param  Zend_Db_Adapter_Abstract $soapClient
     * @param  string                   $methodName
     * @param  string                   $identityParam
     * @param  string                   $credentialParam
     * @return void
     */
    public function __construct(Zend_Soap_Client $soapClient, $methodName = null, $identityParam = null,
                                $credentialParam = null)
    {
        $this->_soapClient = $soapClient;

        if (null !== $methodName) {
            $this->setMethodName($methodName);
        }

        if (null !== $identityParam) {
            $this->setidentityParam($identityParam);
        }

        if (null !== $credentialParam) {
            $this->setcredentialParam($credentialParam);
        }
    }

    /**
     * setMethodName() - set the method name to be used in authentication
     *
     * @param  string $methodName
     * @return Fnde_Auth_Adapter_Soap Provides a fluent interface
     */
    public function setMethodName($methodName)
    {
        $this->_methodName = $methodName;
        return $this;
    }

    /**
     * setIdentityParam() - set the column name to be used as the identity column
     *
     * @param  string $identityParam
     * @return Fnde_Auth_Adapter_Soap Provides a fluent interface
     */
    public function setIdentityParam($identityParam)
    {
        $this->_identityParam = $identityParam;
        return $this;
    }

    /**
     * setCredentialParam() - set the column name to be used as the credential column
     *
     * @param  string $credentialParam
     * @return Fnde_Auth_Adapter_Soap Provides a fluent interface
     */
    public function setCredentialParam($credentialParam)
    {
        $this->_credentialParam = $credentialParam;
        return $this;
    }

    /**
     * setIdentity() - set the value to be used as the identity
     *
     * @param  string $value
     * @return Fnde_Auth_Adapter_Soap Provides a fluent interface
     */
    public function setIdentity($value)
    {
        $this->_identity = $value;
        return $this;
    }

    /**
     * setCredential() - set the credential value to be used
     *
     * @param  string $credential
     * @return Fnde_Auth_Adapter_Soap Provides a fluent interface
     */
    public function setCredential($credential)
    {
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
    public function authenticate()
    {
        $this->_authenticateSetup();
        $authResult = $this->_authenticateCallMethod();
        $this->_resultRow = $authResult->getIdentity();
        $this->_authenticateResultInfo['code'] = $authResult->getCode();
        $this->_authenticateResultInfo['messages'] = $authResult->getMessages();

        return $authResult;
    }

    /**
     * _authenticateSetup() - This method abstracts the steps involved with
     * making sure that this adapter was indeed setup properly with all
     * required pieces of information.
     *
     * @throws Zend_Auth_Adapter_Exception - in the event that setup was not done properly
     * @return true
     */
    protected function _authenticateSetup()
    {
        $exception = null;

        if ($this->_methodName == '') {
            $exception = 'A methodName must be supplied for the Fnde_Auth_Adapter_Soap authentication adapter.';
        } elseif ($this->_identityParam == '') {
            $exception = 'An identity column must be supplied for the Fnde_Auth_Adapter_Soap authentication adapter.';
        } elseif ($this->_credentialParam == '') {
            $exception = 'A credential column must be supplied for the Fnde_Auth_Adapter_Soap authentication adapter.';
        } elseif ($this->_identity == '') {
            $exception = 'A value for the identity was not provided prior to authentication with Fnde_Auth_Adapter_Soap.';
        } elseif ($this->_credential === null) {
            $exception = 'A credential value was not provided prior to authentication with Fnde_Auth_Adapter_Soap.';
        }

        if (null !== $exception) {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            require_once 'Zend/Auth/Adapter/Exception.php';
            throw new Zend_Auth_Adapter_Exception($exception);
        }

        $this->_authenticateResultInfo = array(
            'code'     => Zend_Auth_Result::FAILURE,
            'identity' => $this->_identity,
            'messages' => array()
            );

        return true;
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
    protected function _authenticateCallMethod()
    {
        try {
            ini_set("soap.wsdl_cache_enabled", 0); //** Limpa o cache

            $resultIdentities = unserialize(
                urldecode(
                    $this->_soapClient->__call($this->_methodName, array($this->_identity, $this->_credential))
                )
            );

        } catch (Exception $e) {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            require_once 'Zend/Auth/Adapter/Exception.php';
            throw new Zend_Auth_Adapter_Exception('The supplied parameters to Fnde_Auth_Adapter_Soap failed to '
                                                . 'call service, please check table and column names '
                                                . 'for validity.', 0, $e);
        }
        return $resultIdentities;
    }

     /**
     * getResultRowObject() - Returns the result row as a stdClass object
     *
     * @param  string|array $returnColumns
     * @param  string|array $omitColumns
     * @return stdClass|boolean
     */
    public function getResultRowObject($returnColumns = null, $omitColumns = null)
    {

        if (!$this->_resultRow) {
            return false;
        }

        $returnObject = new stdClass();
        if (null !== $returnColumns) {

            $availableColumns = array_keys($this->_resultRow);
            foreach ( (array) $returnColumns as $returnColumn) {
                if (in_array($returnColumn, $availableColumns)) {
                    $returnObject->{$returnColumn} = $this->_resultRow[$returnColumn];
                }
            }
            return $returnObject;

        } elseif (null !== $omitColumns) {

            $omitColumns = (array) $omitColumns;
            foreach ($this->_resultRow as $resultColumn => $resultValue) {
                if (!in_array($resultColumn, $omitColumns)) {
                    $returnObject->{$resultColumn} = $resultValue;
                }
            }
            return $returnObject;

        } else {

            foreach ($this->_resultRow as $resultColumn => $resultValue) {
                $returnObject->{$resultColumn} = $resultValue;
            }
            return $returnObject;

        }
    }
}