<?php
/**
 * @category   Fnde
 * @package    View
 * @subpackage Helper_UserInfo
 */

class Fnde_View_Helper_UserInfo extends Zend_View_Helper_Abstract
{
    /**
     * @param  array  $messages
     * @param  string $class
     * @param  string $title
     * @return string
     */
    function UserInfo()
    {
        $tmpReturn = '';
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()){
            $urlLogout = $this->view->url(array('module' => 'authentication','controller' => 'index', 'action' => 'logout'), null, true);
            $identity = $auth->getIdentity();
            $name = $identity->username;
            $tmpReturn = '
            <div id="menuAux">
                <ul>
                    <li id="infoUsuario">
                        <span id="infoUsuarioNome">' . $name .'</span>
                        <div id="infoUsuarioSessao"></div>
                    </li>
                    <li id="btnSisAjuda">' . $this->view->help()->getLink() . '</li>
			        <li id="btnSisSair"><a href="' . $urlLogout . '">Sair</a></li>
                </ul>
            </div>';
        }
        return $tmpReturn;
    }
}
