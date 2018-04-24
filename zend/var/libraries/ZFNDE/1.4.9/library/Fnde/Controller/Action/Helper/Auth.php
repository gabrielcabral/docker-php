<?php

class Fnde_Controller_Action_Helper_Auth extends Zend_Controller_Action_Helper_Abstract {
    const DEFAULT_ROLE = 'guest';

    /**
     * @var Fnde_Acl
     */
    protected $_acl;
    /**
     * Credenciais do usuários
     * @var array
     */
    protected $_credentials;

    public function __construct(Fnde_Acl $acl, Zend_Auth $auth = null) {
        $this->_auth = is_null($auth) ? Zend_Auth::getInstance() : $auth;
        $this->_acl = $acl;
        $this->_credentials = array(self::DEFAULT_ROLE);
        if ($this->_auth && $this->_auth->hasIdentity()) {
            $this->_credentials = array_merge($this->_credentials, $this->_auth->getIdentity()->credentials);
        }
    }

    /**
     * Retorna as credênciais do visitante
     * @return array
     */
    protected function getCredentials() {
        return $this->_credentials;
    }

    /**
     * @return Fnde_Acl
     */
    public function getAcl() {
        return $this->_acl;
    }

    public function setAcl(Fnde_Acl $acl) {
        $this->_acl = $acl;
    }

    /**
     * @return Zend_Auth
     */
    public function getAuth() {
        return $this->_auth;
    }

    public function setAuth(Zend_Auth $auth) {
        $this->_auth = $auth;
    }

    /**
     * Retorna se algum perfil do usuário possui permissão para acessar o recursos
     * @param string $role
     * @param string $resource
     * @param string $privilege
     * @return boolean
     */
    protected function _isAllowed($resource = null, $privilege = null) {
        foreach ($this->getCredentials() as $credential) {
            if (
                    $this->_acl->hasRole($credential) &&
                    $this->_acl->has($resource) &&
                    $this->_acl->isAllowed($credential, $resource, $privilege)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retorna o recurso da requisição
     * @return string
     */
    public function getResource() {
        $request = $this->getActionController()->getRequest();
        return "{$request->getModuleName()}:{$request->getControllerName()}";
    }

    /**
     * Retorna a ação da requisição
     * @return string
     */
    public function getAction() {
        return $this->getActionController()->getRequest()->getActionName();
    }

    protected function isDefaultAuthRequest() {
        $request = $this->getActionController()->getRequest();
        $defaultAuthResource = $this->getDefaultAuthResource();
        unset($defaultAuthResource['action']);
        $requestArray = array(
            'module' => $request->getModuleName(),
            'controller' => $request->getControllerName(),
        );
        return ($requestArray == $defaultAuthResource);
    }

    protected function isDefaultForbbidenRequest() {
        $request = $this->getActionController()->getRequest();
        $requestArray = array(
            'module' => $request->getModuleName(),
            'controller' => $request->getControllerName(),
            'action' => $request->getActionName()
        );
        return ($requestArray == $this->getDefaultForbiddenResource());
    }

    protected function getDefaultAuthResource() {
        return $this->_acl->getDefaultAuthResource();
    }

    protected function getDefaultForbiddenResource() {
        return $this->_acl->getDefaultForbiddenResource();
    }

    public function preDispatch() {

        $request = $this->getActionController()->getRequest();
        $resource = $this->getResource();
        $action = $this->getAction();
        $defaultAuthResource = $this->getDefaultAuthResource();
        $defaultForbiddenResource = $this->getDefaultForbiddenResource();


        if ($this->_acl->has($resource)) {
            if (
                    !$this->_acl->isAllowed(self::DEFAULT_ROLE, $resource, $action) &&
                    !$this->_auth->hasIdentity()
            ) {
                $request->setModuleName($defaultAuthResource['module']);
                $request->setControllerName($defaultAuthResource['controller']);
                $request->setActionName($defaultAuthResource['action']);
                $redir = true;
            } elseif (
                    $this->_auth->hasIdentity() &&
                    !$this->_isAllowed($resource, $action) &&
                    !$this->_acl->isAllowed(self::DEFAULT_ROLE, $resource, $action)
            ) {
                $request->setModuleName($defaultForbiddenResource['module']);
                $request->setControllerName($defaultForbiddenResource['controller']);
                $request->setActionName($defaultForbiddenResource['action']);
                $redir = true;
            }
        } elseif ($this->isDefaultForbbidenRequest() && !$this->_auth->hasIdentity()) {
            $request->setModuleName($defaultAuthResource['module']);
            $request->setControllerName($defaultAuthResource['controller']);
            $request->setActionName($defaultAuthResource['action']);
            $redir = true;
        }else{
           // die('there is no resource');
        }

        if ($redir) {
            $request->setDispatched(false);
        }
    }

    function postDispatch() {
        $navigation = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view->navigation();
        $this->prepareAclNavigation($navigation);
    }

    function prepareAclNavigation(Zend_View_Helper_Navigation $navigation) {

        $iterator = new RecursiveIteratorIterator($navigation->getContainer(),
                        RecursiveIteratorIterator::CHILD_FIRST);

        $oldDepth = 0;
        $isActive = array();

        // iterate container
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $parentLabel = ($page->getParent() instanceof Zend_Navigation_Page) ? $page->getParent()->getLabel() : 'root';

            if (empty($isActive[$depth])) {
                $isActive[$depth] = 0;
            }

            if ($page instanceof Zend_Navigation_Page_Mvc) {
                $resource = $page->getModule() . ':' . $page->getController();
                $privilege = $page->getAction();

                if ($this->_acl->has($resource)) {

                    $identity = $this->_auth->getIdentity();
                    if ($identity) {
                        foreach ($identity->credentials as $credential) {
                            if (
                                    $this->_acl->hasRole($credential) &&
                                    $this->_acl->isAllowed($credential, trim($resource), trim($privilege))
                            ) {
                                $isActive[$depth]++;
                                break;
                            }
                        }
                    }

                    $page->setResource($resource);
                    $page->setPrivilege($privilege);
                }
            }

            if ($oldDepth !== $depth) {
                if ($oldDepth > $depth) {
                    $page->setVisible($isActive[$depth + 1]);
                    $isActive[$depth] += $isActive[$depth + 1];
                    $isActive[$depth + 1] = 0;
                }
            }

            if ($depth == 0) {
                $isActive[0] = 0;
            }

            $oldDepth = $depth;
        }
    }

}