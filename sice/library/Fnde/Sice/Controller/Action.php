<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alessandro.bartels
 * Date: 12/06/13
 * Time: 16:28
 * To change this template use File | Settings | File Templates.
 */
class Fnde_Sice_Controller_Action extends Fnde_Controller_Action{

    public function init(){
	
		$path = preg_replace(array('/public\//', '/public/',  '/index.php\//', '/index.php/'), '', $this->_request->getBaseUrl());
		
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $credencial = $usuarioLogado->credentials;

        $perfil = str_replace('sice_', '', $credencial[0]);

        $path .= '/Help/';
        $path .= $perfil;
        $path .= '/!SSL!/WebHelp_Pro/SICE.htm';

        $this->view->help()->setLink($path);
        parent::init();
    }
}
