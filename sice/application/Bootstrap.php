<?php
class Bootstrap extends Fnde_Bootstrap {

	public function _initConfig() {

//		ini_set('display_errors',1);
//		ini_set('display_startup_erros',1);
//		error_reporting(E_ALL);

		header("Content-Type: text/html;charset=iso-8859-1");
		header("Pragma: no-cache");
		header("cache-control: no-cache");
		header("expires: 0");
		header("X-UA-Compatible: IE=EmulateIE8");

		$options = $this->getOptions();
		Zend_Registry::set('config', $options);

		setlocale(LC_ALL, "pt_BR", "ptb");
		date_default_timezone_set('America/Sao_Paulo');

	}
	
	public function _initViewHelper(){
		//Initialize and/or retrieve a ViewRenderer object on demand via the helper broker
		$this->bootstrap('view');
		$viewRenderer = $this->getResource('view');
		//add the global helper directory path
		$viewRenderer->addHelperPath(APPLICATION_ROOT.'/library/Fnde/Sice/View/Helper', 'Fnde_Sice_View_Helper_');
		
	}

}
