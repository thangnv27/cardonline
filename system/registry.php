<?php

class Registry {

    public static $instance;
    public static $objects = array();
    public static $settings = array(
        "config" => array(),
        "autoload" => array(),
        "system" => array(),
    );
    public static $uri = array();
    public static $raw_uri;
    public static $siteurl;
    public static $connection_count = 0;
    public static $query_count = 0;

    private function __construct() {
        
    }

    public static function singleton() {
        if (empty(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }

    public static function instance($class_name) {
        if (empty(self::$objects[$class_name])) {
            self::$objects[$class_name] = new $class_name;
        }
        return self::$instance;
    }

}