<?php

class Fnde_Plugin_Authenticate extends Zend_Controller_Plugin_Abstract {

    protected $hasPerfil = true;

    public function getHeader() {
        return apache_request_headers();
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        $config = Zend_Registry::get('config');


        if($config['security']['provider'] == "idm"){
            if (!isset($config['idm']['logout'])) {
                throw new Exception('Nao existe URL de logout no arquivo de configuracao (idm.logout = ?)');
            }
            $auth = Zend_Auth::getInstance();
            $header = $this->getHeader();

            //if ($header['FNDE_ID_USUARIO']) {
                //Busca Informações do registro
                try {
                    $userInfo = new stdClass();
                    $userInfo->username = $header['FNDE_ID_USUARIO'];
                    $userInfo->name = $header['FNDE_NOME_USUARIO'];
                    $userInfo->email = $header['FNDE_EMAIL_USUARIO'];
                    $userInfo->departamento = $header['FNDE_DEPARTAMENTO'];
                    $userInfo->cpf = $header['FNDE_ID_USUARIO'];
                    $userInfo->tipo = $header['FNDE_TIPO_USUARIO'];
                    $userInfo->credentials = $this->getRoles($header['FNDE_ID_USUARIO']);
                    $authAdapter = new Fnde_Auth_Adapter_Idm();
                    $authAdapter->setIdentity($userInfo);
                    $auth->authenticate($authAdapter);
                    if ($config->security->enabled && empty($userInfo->credentials)) {
                        readfile( APPLICATION_DATA . 'no-role.html');
                        die;
                    }
                } catch (Exception $e) {
                    // O usuário não tem perfil
                    throw new Exception('There is something wrong in this request. Contact the support.');
                    //$this->getResponse()->setRedirect($config['idm']['logout'] . $app);
                }
            /*} else {
                // Não existe o header na requisição
                if ($config['security']['enabled']) {
                    throw new Exception('There is no header in this request. Contact the support.');
                }
            }*/

        }

    }

    public function getRoles($userId) {
        $modelIdm = new Fnde_Model_Idm();
        $modelIdm->setIdUsuario($userId);
        $roles = $modelIdm->getRole(Fnde_Model_Idm::ROLE_INTERNAL);
        return array_map('strtolower', $roles);
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request) {
    }

}
