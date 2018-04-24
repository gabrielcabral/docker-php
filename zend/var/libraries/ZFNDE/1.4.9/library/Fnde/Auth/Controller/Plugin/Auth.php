<?php

/**
 * Plugin de autenticaчуo
 *
 * @todo Ajustes de redirecionamento em caso de acessar o authenticate/index/[index|login]
 */
class Fnde_Auth_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract {
    const MASTER_ROLE = 'master';
    const DEFAULT_ROLE = 'guest';
    const ROLE_NOT_EXIST = 'A role: [%s] nуo foi declarada no ACL';
    /**
     *
     * @var Zend_Auth
     */
    protected $_auth;
    /**
     *
     * @var Zend_Acl
     */
    protected $_acl;
    protected $_noauth = array();
    protected $_noacl = array();
    protected $_isAllowAnonymousAccess = false;
    protected $_defaultRole = 'guest';

    public function __construct(Zend_Auth $auth, Zend_Acl $acl, array $options) {
        $this->_auth = $auth;
        $this->_acl = $acl;
        $this->_noacl = $options['noacl'];
        $this->_noauth = $options['noauth'];
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $module = strtolower($request->getModuleName());
        $controller = strtolower($request->getControllerName());
        $action = strtolower($request->getActionName());
		


        $redirect = FALSE;
        $resource = trim($module) . ':' . trim($controller);
        if ($this->_acl->hasRole(self::MASTER_ROLE)) {
            $this->_acl->removeRole(self::MASTER_ROLE);
        }
        if ($module != $this->_noauth['module']) {
            $allowed = FALSE;
            $logged = FALSE;
			
			if ($this->_auth->hasIdentity()) {
				$this->_acl->addRole(self::MASTER_ROLE, $this->_auth->getIdentity()->credentials);
			}

            // Verifica se tem usuario logado
            if (!$this->_acl->isAllowed(self::DEFAULT_ROLE, trim($resource), trim($action))) {
                if (($logged = $this->_auth->hasIdentity())) {
                    $identity = $this->_auth->getIdentity();
                    foreach ($identity->credentials as $credential) {
                        if (!$this->_acl->hasRole($credential)) {
                            throw new Exception(sprintf(self::ROLE_NOT_EXIST, $credential));
                        }
                    }

                    if ($this->_acl->isAllowed(self::MASTER_ROLE, trim($resource), trim($action))) {
                        $allowed = TRUE;
                    }

                    if (!$allowed && $logged) {
                        $module = $this->_noacl ['module'];
                        $controller = $this->_noacl ['controller'];
                        $action = $this->_noacl ['action'];
                        $redirect = TRUE;
                    }
                } else {
                    $module = $this->_noauth ['module'];
                    $controller = $this->_noauth ['controller'];
                    $action = $this->_noauth ['action'];
                    $redirect = TRUE;
                }
            }

            $exceptions = $this->getResponse()->getException();
            if (count($exceptions) == 0) {
                if ($redirect) {
                    $request->setModuleName($module);
                    $request->setControllerName($controller);
                    $request->setActionName($action);
                }
            }
        }
    }

    /**
     * @return AclPlugin
     */
    public function getAcl() {
        return $this->_acl;
    }

    /**
     * @return Zend_Auth
     */
    public function getAuth() {
        return $this->_auth;
    }

    public function requestIsNoAuth($module, $controller, $action) {
        if ($module == $this->_noauth['module'] &&
            $controller == $this->_noauth['controller'] &&
            $action == $this->_noauth['action']) {
            return true;
        }
        return false;
    }

    public function requestIsNoAcl($module, $controller, $action) {
        if ($module == $this->_noacl['module'] &&
            $controller == $this->_noacl['controller'] &&
            $action == $this->_noacl['action']) {
            return true;
        }
        return false;
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        if ($this->_auth->hasIdentity()) {
            if ($request->getModuleName() === $this->_noauth['module']
                && $request->getControllerName() === $this->_noauth['controller']
                && in_array($request->getActionName(), array('index', 'login'))) {
                return false;
            }
            $credentials = $this->_auth->getIdentity()->credentials;
            $navigation = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view->navigation();
            $navigation->setAcl($this->_acl);
			$navigation->setRole(self::MASTER_ROLE);
            $this->_acl->prepareAclNavigation($navigation);
        }
    }

}

?>