<?php

/**
 *
 * $Rev:: 262                  $
 * $Date:: 2011-05-18 15:12:45#$
 * $Author:: TheoziranSilva    $
 *
 * @package ZFnde
 * @category View Helper
 * @name ContextMenu
 * 
 * Classe de Modelo do tipo Database: Fnde_Sei_Model_TipoPeriodicidade
 * @uses Zend_View_Helper_Abstract
 * @since v1.1.2
 * @author theoziran<theoziran.silva@fnde.gov.br
 */
class Fnde_View_Helper_ContextMenu extends Zend_View_Helper_Abstract {
    const DEFAULT_MODULE = 'default';
    const DEFAULT_CONTROLLER = 'index';
    const DEFAULT_ACTION = 'index';

    /**
     * @var Zend_Acl $acl
     */
    protected $_acl = null;

    /**
     * @return Zend_Acl
     */
    protected function getAcl() {
        $options = Zend_Registry::get('config');
        if (is_null($this->_acl)) {
            $this->_acl = new Fnde_Acl(new Zend_Config_Ini($options['security']['acl']['rules']), $options['security']['acl']['module_controller_separator'], $options['security']['acl']['privileges_separator']);
        }
        return $this->_acl;
    }

    /**
     * @param array $menu
     * @return string 
     */
    public function ContextMenu($menus) {
        $frontController = Zend_Controller_Front::getInstance();
        $baseUrl = $frontController->getBaseUrl() . '/';
        $request = $frontController->getRequest();
        $url = str_replace($baseUrl, '', $url);
        $params = explode('/', $url);

        $output = "\n<div id=\"menuContexto\">\n\t<ul>";
        foreach ($menus as $menu) {
            $url = $menu['url'];
            $item = $menu['label'];

            if ($this->isCurrent($url)) {
                $className = ' class="active"';
                $address = '#';
            } else {
                $className = '';
                $address = $url;
            }

            if (!$this->isExternal($url)) {

                $auth = Zend_Auth::getInstance();

                $cleanUrl = str_replace($_SERVER['SCRIPT_NAME'], '', $url);
                $params = explode('/', $cleanUrl);
                $defaultResources = array(self::DEFAULT_MODULE, self::DEFAULT_CONTROLLER, self::DEFAULT_ACTION);
                array_shift($params);
                for ($i = 0; $i < 3; $i++) {
                    if (empty($params[$i])) {
                        $params[$i] = $defaultResources[$i];
                    }
                }
                list($module, $controller, $action) = $params;

                $resource = $module . ':' . $controller;
                $li = "\r\t\t<li" . $className . "><a href=\"" . $address . "\">" . $item . "</a></li>\n";
                if (
                        $this->getAcl()->has($resource) &&
                        $auth->getIdentity() &&
                        $this->isAllowed($auth->getIdentity()->credentials, $resource, $action))
                {
                    $output .= $li;
                } else if (!$this->getAcl()->has($resource)) {
                    $output .= $li;
                }
            }else{
				$output .= "\r\t\t<li" . $className . "><a href=\"" . $address . "\">" . $item . "</a></li>\n";
			}
        }
        $output .= "\t</ul>\n</div>";
        return $output;
    }

    /**
     *
     * @param string $roles
     * @param string $resource
     * @param string $action
     * @return boolean
     */
    protected function isAllowed(array $roles, $resource, $action) {
        foreach ($roles as $role) {
            if ($this->getAcl()->isAllowed($role, $resource, $action)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retorna se a URL é a mesma da requisição atual
     * @param string $url
     * @return boolean
     */
    protected function isCurrent($url) {
        $frontController = Zend_Controller_Front::getInstance();
        $baseUrl = $frontController->getBaseUrl() . '/';
        $request = $frontController->getRequest();
        $url = str_replace($baseUrl, '', $url);
        $params = explode('/', $url);

        $defaults = array(self::DEFAULT_MODULE, self::DEFAULT_CONTROLLER, self::DEFAULT_ACTION);

        foreach ($defaults as $key => $default) {
            $params[$key] = empty($params[$key]) ? $default : $params[$key];
        }
        $params = array_slice($params, 0, 3);

        return (
        $request->getModuleName() == $params[0] &&
        $request->getControllerName() == $params[1] &&
        $request->getActionName() == $params[2]
        );
    }

    /**
     * Retorna se a URL é com endereço relativo
     * @param string $url
     * @return boolean
     */
    protected function isExternal($url) {
        return (strpos($url, 'http:') === 0 || strpos($url, 'https:') === 0);
    }

}