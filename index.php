<?php

/**
 * ---------------------------------------------------------------
 * DIRECTORY SEPARATOR
 * ---------------------------------------------------------------
 *
 * Directory separator
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * ---------------------------------------------------------------
 * BASE PATH
 * ---------------------------------------------------------------
 *
 * Full root path in server
 */
define('BASE_PATH', dirname(realpath(__FILE__)));

/**
 * ---------------------------------------------------------------
 * APPLICATION PATH
 * ---------------------------------------------------------------
 *
 * Application folder name
 */
define('APP_PATH', BASE_PATH . DS . 'app' . DS);

/**
 * ---------------------------------------------------------------
 * ADMINISTRATOR PATH
 * ---------------------------------------------------------------
 *
 * Admin folder name
 */
define('ADMIN_PATH', BASE_PATH . DS . 'admin' . DS);

/**
 * ---------------------------------------------------------------
 * SYSTEM PATH
 * ---------------------------------------------------------------
 *
 * System folder name. 
 */
define('SYS_PATH', BASE_PATH . DS . 'system' . DS);

/**
 * ---------------------------------------------------------------
 * DATABASE PATH
 * ---------------------------------------------------------------
 *
 * Database folder name. 
 */
define('DB_PATH', BASE_PATH . DS . 'database' . DS);

/**
 * ---------------------------------------------------------------
 * UPLOAD PATH
 * ---------------------------------------------------------------
 *
 * Files folder
 */
define('UPLOAD_PATH', BASE_PATH . DS . 'upload' . DS);

/**
 * ---------------------------------------------------------------
 * LANGUAGE PATH
 * ---------------------------------------------------------------
 *
 * Language folder
 */
define('LANG_PATH', BASE_PATH . DS . 'language' . DS);

/**
 * ---------------------------------------------------------------
 * PUBLIC PATH
 * ---------------------------------------------------------------
 *
 * Public folder
 */
define('PUBLIC_PATH', BASE_PATH . DS . 'public' . DS);

/**
 * ---------------------------------------------------------------
 * TEMP PATH
 * ---------------------------------------------------------------
 *
 * Temp folder
 */
define('TMP_PATH', BASE_PATH . DS . 'tmp' . DS);

/**
 * ---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 * ---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
define('ENVIRONMENT', 'development');
/**
 * ---------------------------------------------------------------
 * ERROR REPORTING
 * ---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
if (defined('ENVIRONMENT')) {
    switch (ENVIRONMENT) {
        case 'development':
            ini_set('display_errors', 'On');
            error_reporting(E_ALL & ~E_NOTICE);
//            error_reporting(E_ALL | E_STRICT);
//            error_reporting(E_ALL);
            break;
        case 'testing':
            break;
        case 'production':
            ini_set('display_errors', 'Off');
            ini_set('log_errors', 'On');
            ini_set('error_log', BASE_PATH . DS . 'tmp' . DS . 'logs ' . DS . 'error.log');
            error_reporting(0);
            break;
        default:
            exit('The application environment is not set correctly.');
    }
}

/**
 * Application
 */
require SYS_PATH . 'app.php';
