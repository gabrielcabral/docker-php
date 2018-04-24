<?php

/**
 * Classe centralizada de Bootstrap
 *
 * Faz a inicialização dos componentes padroes utilizados por todas as aplicações.
 *
 * @author $
 */
class Fnde_Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initAutoLoader() {
        $this->bootstrap('FrontController');
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);

        $controller = Zend_Controller_Front::getInstance();
        //$controller->throwExceptions(true);
        $controller->setBaseUrl($controller->getBaseUrl());
       // $controller->registerPlugin(new Fnde_Plugin_Authenticate());
    }

    protected function _initConfig() {
        Zend_Registry::set('config', $this->getOptions());
    }

    protected function _initAuthPlugin() {
        $options = Zend_Registry::get('config');
        if (intval($options['security']['enabled']) == 1) {
            $auth = Zend_Auth::getInstance();
            $auth->setStorage(new Zend_Auth_Storage_Session($options['app']['name'], true));
            $acl = new Fnde_Acl(new Zend_Config_Ini($options['security']['acl']['rules']), $options['security']['acl']['module_controller_separator'], $options['security']['acl']['privileges_separator']);
            $authPlugin = new Fnde_Auth_Controller_Plugin_Auth($auth, $acl, $options['security']['authplugin']);
            $this->getResource('FrontController')->registerPlugin($authPlugin);
        }
    }

    public function _initZFDebug() {
        $config = Zend_Registry::get('config');
        //die(Fnde_Util::debug($config));
        if ($config['ZFDebug']['enabled']) {
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('ZFDebug');
            ZFDebug_Controller_Plugin_Debug::$standardPlugins[] = 'Auth';
            $options = array(
                'plugins' => array(
                    'Variables',
                    //'Auth',
                    'Memory',
                    'Time',
                    //'File' => array('base_path' => APPLICATION_ROOT),
                    'Html',
                    'Exception',
                    'Session'
                )
            );

            # Instantiate the database adapter and setup the plugin.
            # Alternatively just add the plugin like above and rely on the autodiscovery feature.
            if ($this->hasPluginResource('db')) {
                $this->bootstrap('db');
                $db = $this->getPluginResource('db')->getDbAdapter();
                $options['plugins']['Database']['adapter'] = $db;
            }

            # Setup the cache plugin
            if ($this->hasPluginResource('cache')) {
                $this->bootstrap('cache');
                $cache = $this - getPluginResource('cache')->getDbAdapter();
                $options['plugins']['Cache']['backend'] = $cache->getBackend();
            }

            $debug = new ZFDebug_Controller_Plugin_Debug($options);

            $this->bootstrap('frontController');
            $frontController = $this->getResource('frontController');
            $frontController->registerPlugin($debug);
        }
    }

    protected function _initView() {
	header("X-UA-Compatible: IE=8");
        $options = Zend_Registry::get('config');
        if (isset($options['resources']['view'])) {
            $optView = $options['resources']['view'];
            $view = new Zend_View($options['resources']['view']);
        } else {
            $view = new Zend_View();
        }

        if (isset($options['app'])) {
            $optView['application']['description'] = $options['app']['description'];
        }

        /**
         * Definição de Metatags baseado nas informações existentes.
         */
        $optView['meta']['name']['title'] = "{$optView['titlePrefix']} {$optView['titleSeparator']} {$optView['application']['name']}";
        $optView['meta']['name']['description'] = $optView['application']['description'];

        $optView['meta']['http-equiv']['Content-Type'] = "text/html;charset={$optView['encoding']}";
        $optView['meta']['http-equiv']['Content-Language'] = $optView['language'];


        // Set Doctype
        $view->doctype($optView['doctype']);

        // Set Meta.http-equiv
        foreach ($optView['meta']['http-equiv'] as $key => $value) {
            $view->headMeta()->appendHttpEquiv($key, $value);
        }
        // Set Meta.name
        foreach ($optView['meta']['name'] as $key => $value) {
            $view->headMeta()->appendName($key, $value);
        }
        $view->headTitle($optView['titlePrefix']);
        $view->headTitle()->setSeparator($optView['titleSeparator']);
        $view->headTitle()->append(" {$options['app']['name']} {$options['app']['version']} ");

//        $view->jQuery()
//                ->setLocalPath($optView['jquery']['localPath'])
//                ->setUiLocalPath($optView['jquery']['UiLocalPath'])
//                ->addStylesheet($optView['jquery']['stylesheet']);

        foreach ($optView['headLink']['stylesheet'] as $value) {
            $view->headLink()->appendStylesheet($value);
        }
        foreach ($optView['headScript']['file'] as $value) {
            $view->headScript()->appendFile($value);
        }

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
        Zend_Registry::set('view', $view);
        return $view;
    }

    protected function _initNavigation() {
        $options = Zend_Registry::get('config');
        $container = new Zend_Navigation(new Zend_Config_Ini(APPLICATION_CONFIGS . '/menu.ini', 'nav'));
        $this->getResource('view')->navigation($container);
    }

}
