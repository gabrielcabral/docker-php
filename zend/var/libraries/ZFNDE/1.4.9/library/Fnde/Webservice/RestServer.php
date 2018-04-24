<?php

/**
 * Arquivo de classe de Servidor REST do padrao de webservices do FNDE.
 *
 * Último Commit: r$Rev$ por $Author$ em $Date$.
 *
 * @package Fnde
 * @category Webservice
 * @name RestServer
 * @author Alberto Guimaraes Viana <alberto.viana@fnde.gov.br>
 * @author Walker de Alencar Oliveira <walker.oliveira@fnde.gov.br>
 */
/**
 * Classe de Servidor REST para Webservices
 *
 * @version $Id$
 */
class Fnde_Webservice_RestServer extends Zend_Rest_Server
{
    /**
     * Handle an array or object result
     *
     * @param array|object $struct Result Value
     * @return array Response
     */
    protected function _handleStruct($struct)
    {
      return (array)$struct;
    }

    /**
     * Implement Zend_Server_Interface::fault()
     *
     * Creates XML error response, returning DOMDocument with response.
     *
     * @param string|Exception $fault Message
     * @param int $code Error Code
     * @return array
     */
    public function fault($exception = null, $code = null)
    {
        if (isset($this->_functions[$this->_method])) {
            $function = $this->_functions[$this->_method];
        } elseif (isset($this->_method)) {
            $function = $this->_method;
        } else {
            $function = 'rest';
        }

        if ($function instanceof Zend_Server_Reflection_Method) {
            $class = $function->getDeclaringClass()->getName();
        } else {
            $class = false;
        }

        if ($function instanceof Zend_Server_Reflection_Function_Abstract) {
            $method = $function->getName();
        } else {
            $method = $function;
        }

        $arrResult = array();
        if ($exception instanceof Exception) {
            $arrResult['code'] = $exception->getCode();
            $arrResult['text'] = $exception->getMessage();
        } elseif (($exception !== null) || 'rest' == $function) {
            $arrResult['code'] = E_ERROR;
            $arrResult['text'] = 'An unknown error occured. Please try again.';
        } else {
            $arrResult['code'] = E_ERROR;
            $arrResult['text'] = "Call to ' . $method . '  failed.";
        }

        $arrResult['status'] = 0;

        return $arrResult;
    }

}
