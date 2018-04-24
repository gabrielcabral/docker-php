<?php
// Define path to ZF_FNDE application
defined('ZF_FNDE_APPLICATION')
        || define('ZF_FNDE_APPLICATION', ZF_FNDE_ROOT . DIRECTORY_SEPARATOR . 'application');

// Define path to ZF_FNDE application/configs
defined('ZF_FNDE_CONFIGS')
        || define('ZF_FNDE_CONFIGS', ZF_FNDE_APPLICATION . DIRECTORY_SEPARATOR . 'configs');

// Define path to ZF_FNDE layouts
defined('ZF_FNDE_LAYOUTS')
        || define('ZF_FNDE_LAYOUTS', ZF_FNDE_APPLICATION . DIRECTORY_SEPARATOR . 'layouts');

// Define path to ZF_FNDE modules
defined('ZF_FNDE_MODULES')
        || define('ZF_FNDE_MODULES', ZF_FNDE_APPLICATION . DIRECTORY_SEPARATOR . 'modules');


// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'application');

// Define path to application directory
defined('APPLICATION_DATA')
        || define('APPLICATION_DATA', APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'data');

// Define path to application directory
defined('APPLICATION_CONFIGS')
        || define('APPLICATION_CONFIGS', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs');

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'desenv'));

/**
 * Substituido pela constante: Fnde_Message::MSG_INFO 
 * @deprecated desde v1.0.7
 */
defined('MSG_ORIENTACAO')
        || define('MSG_ORIENTACAO', 'msgOrientacao' );
/**
 * Substituido pela constante: Fnde_Message::MSG_SUCCESS 
 * @deprecated desde v1.0.7
 */
defined('MSG_SUCESSO')
        || define('MSG_SUCESSO', 'msgSucesso' );
/**
 * Substituido pela constante: Fnde_Message::MSG_ERROR 
 * @deprecated desde v1.0.7
 */
defined('MSG_ERRO')
        || define('MSG_ERRO', 'msgErro' );
/**
 * Substituido pela constante: Fnde_Message::MSG_ALERT 
 * @deprecated desde v1.0.7
 */
defined('MSG_ALERTA')
        || define('MSG_ALERTA', 'msgAlerta' );

// Ensure library/ is on include_path
set_include_path(
        implode(PATH_SEPARATOR,
                array(
                    APPLICATION_PATH    . DIRECTORY_SEPARATOR . 'forms',
                    APPLICATION_ROOT    . DIRECTORY_SEPARATOR . 'library',
                    ZF_FNDE_APPLICATION . DIRECTORY_SEPARATOR . 'forms',
                    ZF_FNDE_ROOT        . DIRECTORY_SEPARATOR . 'library',
                    get_include_path()
                )
        )
);

/** Zend_Application */
require_once 'Zend/Application.php';

/**
 * @var Zend_Application
 */

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_CONFIGS . DIRECTORY_SEPARATOR . 'application.ini'
);