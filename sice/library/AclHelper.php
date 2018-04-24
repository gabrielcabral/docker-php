<?php
class AclHelper extends Zend_Controller_Action_Helper_Abstract {

	protected $_action;
	protected $_auth;
	protected $_acl;
	protected $_controllerName;

	public function __construct( Zend_View_Interface $view = null, array $options = array() ) {
		$this->_auth = Zend_Auth::getInstance();
		$this->_acl = $options['acl'];
	}

	public function init() {
		$this->_action = $this->getActionController();
		$controller = $this->_action->getRequest()->getControllerName();
	}

	public function preDispatch() {
		$role = 'guest';
		if ( $this->_auth->hasIdentity() ) {
			$user = $this->_auth->getIdentity();

			if ( is_object($user) ) {
				$role = $this->_auth->getIdentity()->role;
			}
		}

		$request = $this->_action->getRequest();
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		$module = $request->getModuleName();
		$this->_controllerName = $controller;

		$resource = $controller;
		$privilege = $action;

		if ( !$this->_acl->has($resource) ) {
			$resource = null;
		}

		if ( !$this->_acl->isAllowed($role, $resource, $privilege) ) {
			$request->setModuleName('default');
			$request->setControllerName('auth');
			$request->setActionName('login');
			$request->setDispatched(false);
		}
	}

}
