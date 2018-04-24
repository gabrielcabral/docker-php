<?php
/**
 * Description of IndexControllers
 *
 * @author Leandro
 */
class Authentication_IndexController extends Fnde_Controller_Action {
    const MSG_LOGIN_SUCCESS = 'Autenticação bem sucedida com o usuário %s.';
	const MSG_LOGIN_ERROR   = 'Autentica&ccedil;&atilde;o falhou';

    public function getForm() {
        $form = new Fnde_Form_Login();
        $form->setAction ( $this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . '/login')
             ->setMethod ( 'post' );
        return $form;
    }
    public function indexAction() {
        $this->setTitle('Login');
        $this->setSubtitle('Área de acesso restrito');
       
        $this->view->form = $this->getForm();
        $this->render('form');
    }

    function loginAction() {
        $this->setTitle('Login');
        $this->setSubtitle('Área de acesso restrito');
        if (!$this->getRequest()->isPost ()) {
            return $this->_forward('index');
        }

        $form = $this->getForm();
        $formData = $this->getRequest()->getPost();
        if (!$form->isValid ( $formData )) {
            $this->view->form = $form;
            return $this->render('form');
        }

        $app = $this->getFrontController()->getParam( 'bootstrap' )->getOption( 'app' );
        $webservices = $this->getFrontController()->getParam( 'bootstrap' )->getOption( 'webservices' );
        $uriLogin    = $webservices['segweb']['uri'] . 'usuario/autenticar';
        $uriUserInfo = $webservices['segweb']['uri'] . 'usuario/info';

        $authAdapter = new Fnde_Auth_Adapter_Rest(new Zend_Rest_Client($uriLogin), new Zend_Rest_Client($uriUserInfo));

        //passar usuário e senha enviado por POST
        $authAdapter->setSiglaAplicacao( $app['name'] );
        $authAdapter->setIdentity( $form->getValue('username') );
        $authAdapter->setCredential( $form->getValue('password') );

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate ( $authAdapter );

        if ($result->isValid ()) {
            $data = $authAdapter->getResultRowObject ( NULL, 'password' );
            $auth->getStorage()->write ( $data );
            $this->addMessage(Fnde_Message::MSG_SUCCESS, sprintf(self::MSG_LOGIN_SUCCESS, $form->getValue('username')) );
            $this->_redirect('/');
        } else {
            $messages = $result->getMessages();
            $this->view->stMensagem = $result->getMessages();
			//Retorna que a autenticação falhou não importa que erro seja para o usuário deverá apenas mostrar se os dados foram autenticados ou não
			//$this->view->stMensagem = array(self::MSG_LOGIN_ERROR);
            return $this->_forward('index');
        }
    }

    function logoutAction() {
        $config = Zend_Registry::get('config');
        Zend_Auth::getInstance()->clearIdentity();
        if($config['security']['provider'] == "idm"){
            $logout = $config['idm']['logout'];
            $this->_redirect( $logout . $this->view->baseUrl());
        }else{
            $this->_redirect( $this->getRequest()->getModuleName() );
        }
    }
}
