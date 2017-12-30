<?php
/*
$dir = new DirectoryIterator(APP_PATH . 'controller');
$directories = array();
foreach ($dir as $fileinfo) {
    if ($fileinfo->isDir() && !$fileinfo->isDot()) {
        $directories[] = $fileinfo->getFilename();
    }
}
*/
function __autoload($class_name) {
//    global $directories;

    $files = array(
        // System folder
        SYS_PATH . 'controller' . DS . $class_name . '.php',
        SYS_PATH . 'model' . DS . $class_name . '.php',
        SYS_PATH . 'view' . DS . $class_name . '.php',
        SYS_PATH . 'libraries' . DS . $class_name . '.php',
        // Application folder
        APP_PATH . 'controller' . DS . $class_name . '.php',
        APP_PATH . 'model' . DS . $class_name . '.php',
        APP_PATH . 'libraries' . DS . $class_name . '.php',
        // Dashboard folder
        ADMIN_PATH . 'controller' . DS . $class_name . '.php',
        ADMIN_PATH . 'model' . DS . $class_name . '.php',
        ADMIN_PATH . 'libraries' . DS . $class_name . '.php',
    );
/*
    foreach ($directories as $dir) {
        $filename = APP_PATH . 'controller' . DS . $dir . DS . $class_name . '.php';
        if (file_exists($filename)) {
            $files[] = $filename;
        }
    }
*/
    foreach ($files as $file) {
        if (file_exists($file)) {
            require $file;
        }
    }
}

require SYS_PATH . 'registry.php';
require SYS_PATH . 'bootstrap.php';

$registry = Registry::singleton();
$bootstrap = new Bootstrap();
