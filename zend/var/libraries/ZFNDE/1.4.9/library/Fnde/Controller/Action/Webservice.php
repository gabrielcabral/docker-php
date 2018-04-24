<?php

/**
 * Arquivo de classe de Controle do padrao de webservices do FNDE.
 *
 * Último Commit: r$Rev$ por $Author$ em $Date$.
 *
 * @package Fnde
 * @category Webservice
 * @name Controller
 * @author Alberto Guimaraes Viana <alberto.viana@fnde.gov.br>
 * @author Walker de Alencar Oliveira <walker.oliveira@fnde.gov.br>
 */

/**
 * Classe padrão para utilização de Webservice
 * 
 * @version $Id$
 */
abstract class Fnde_Controller_Action_Webservice extends Zend_Controller_Action
{

    private $_businessClass = null;

    /**
     * Instancia de resposta do webservice.
     * @var Fnde_Webservice_Response
     */
    private $_wsResponse = null;

    /**
     * Instancia do Rest Server
     * @var Fnde_Webservice_RestServer
     */
    private $_restServer = null;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs 
     */
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response,
                                array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        $this->_wsResponse = new Fnde_Webservice_Response();
        $this->_restServer = new Fnde_Webservice_RestServer();

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }

    /**
     * @return Fnde_Webservice_Response
     */
    private function getWsResponse()
    {
        return $this->_wsResponse;
    }

    /**
     * @return Fnde_Webservice_RestServer
     */
    private function getRestServer()
    {
        return $this->_restServer;
    }

    /**
     * Valida se a classe de negocio foi definida
     */
    final private function validateBusinessClass()
    {
        if (is_null($this->_businessClass)) {
            throw new Exception('Standards: BusinessClass não informada no INIT');
        }
    }

    /**
     * Defina qual a classe de negocio do servico
     *
     * @param string $businessClass
     */
    final protected function setBusinessClass($businessClass)
    {
        $this->_businessClass = $businessClass;
        $this->validateBusinessClass();
    }

    /**
     * Chama automaticamente o Método da Business e constroi a saida de dados.
     *
     * @param  string $methodName
     * @param  array $args
     * @return void
     */
    final public function __call($methodName, $args)
    {
        $this->validateBusinessClass();

        $this->getRestServer()->setClass($this->_businessClass);
        $this->getRestServer()->returnResponse(true);

        $this->getResponse()->setHeader('Content-type', 'text/xml');

        $upload = new Zend_File_Transfer_Adapter_Http;

        $file = array();
        if ($upload->receive()) {
            $file = $upload->getFileInfo();
        }
        
        $params = array_merge($this->getRequest()->getPost(), $file);
        
        $this->getResponse()->setBody(
                $this->getWsResponse()->build(
                        $this->getRestServer()->handle(
                                $params
                        )
                )
        );
    }

}