<?php

class Template {

    public function __construct() {
        
    }

    public function Smarty() {
        require SYS_PATH . 'libraries' . DS . 'smarty' . DS . 'SmartyBC.class.php';

        $smarty = new SmartyBC();
        $smarty->setTemplateDir(APP_PATH . "view" . DS . THEME_NAME . DS);
        $smarty->setCompileDir(APP_PATH . "cache" . DS);
        $smarty->setConfigDir(APP_PATH . "config" . DS);
        $smarty->setCacheDir(APP_PATH . "cache" . DS);
        //$smarty->clearAllCache();
        //$smarty->caching = Smarty::CACHING_LIFETIME_CURRENT;
        $smarty->caching = CACHE;
        $smarty->debugging = DEBUG_THEME;
        return $smarty;
    }

    public function assignValue($string, $array = array()) {
        $key = array();
        $value = array();
        foreach ($array as $k => $v) {
            $key[] = "{" . $k . "}";
            $value[] = $v;
        }
        $result = str_replace($key, $value, $string);
        return $result;
    }

}
