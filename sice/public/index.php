<?php
// Define path to ZF_FNDE root
defined('ZF_FNDE_ROOT')
|| define('ZF_FNDE_ROOT', DIRECTORY_SEPARATOR . 'usr' . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR . 'zend' . DIRECTORY_SEPARATOR . 'var'
    . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'ZFNDE' . DIRECTORY_SEPARATOR . '1.4.9'); 


// Define path to application root
defined('APPLICATION_ROOT')
        || define('APPLICATION_ROOT', realpath(dirname(dirname(__FILE__))));

include_once(ZF_FNDE_ROOT . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'config.php');

try {
    $application->bootstrap()
                ->run();
} catch (Exception $e) {
    if (APPLICATION_ENV == 'desenv') {
        echo "<h1>" . $e->getMessage() . "</h1>";
        echo "<pre>" . Fnde_Util::debug($e,false) . "</pre>";
    } else {
        echo "<h1>Erro de execução da aplicação</h1>";
        echo "[{$e->getCode()}] - {$e->getMessage()}.";
    }
}


