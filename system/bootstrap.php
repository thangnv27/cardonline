<?php

class Bootstrap {

    public $controller;

    function __construct() {
        /**
         * Requiring the configuration files
         */
        require APP_PATH . 'config' . DS . 'config.php';
        require APP_PATH . 'config' . DS . 'autoload.php';
        require SYS_PATH . 'config' . DS . 'config.php';
        require SYS_PATH . 'config' . DS . 'country-codes.php';
        require SYS_PATH . 'config' . DS . 'country-coordinates.php';

        /**
         * Requiring the includes functions files
         */
        require SYS_PATH . 'inc' . DS . 'functions.php';
        require SYS_PATH . 'inc' . DS . 'file.php';
        require SYS_PATH . 'inc' . DS . 'format.php';
        require SYS_PATH . 'inc' . DS . 'parse-user-agent.php';
        require SYS_PATH . 'inc' . DS . 'permalink.php';
        require SYS_PATH . 'inc' . DS . 'geoip' . DS . 'geoip.inc';

        /**
         * Configuration settings
         */
        Registry::$settings['config'] = $config;
        Registry::$settings['autoload'] = $autoload;
        Registry::$settings['system'] = $system;
        Registry::$settings['ISO_COUNTRY_CODE'] = $ISOCountryCode;
        Registry::$settings['ISO_COUNTRY_COORDINATE'] = $CountryCoordinates;
        Registry::$siteurl = $config['site_url'];

        /**
         * Defination
         */
        define('DASHBOARD_URL', Registry::$siteurl . '/dashboard');

        /**
         * Load Libraries
         */
        $this->loadLibraries();

        /* Load language */
        $lang = new Language();
        if (isset($_GET['lang']) and trim($_GET['lang']) != "") {
            $lang_code = trim($_GET['lang']);
            if (file_exists(LANG_PATH . $lang_code . '.xml') or file_exists(LANG_PATH . "admin." . $lang_code . '.xml')) {
                $lang->setLang($lang_code);
                Language::$lang_code = $lang_code;
            }
        }

        /* Call Controller */

        $url = isset($_GET['url']) ? $_GET['url'] : null;
        $url = rtrim($url, '/');
        $url = str_replace("//", "/", $url);
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);
        
        // Statistics
        $uo = new StatisticsUserOnline();
        $hits = new StatisticsHits();
        
        // Detect Device
        $_agent = $uo->get_UserAgent();
        if (in_array($_agent['platform'], array('Android', 'iPhone', 'BlackBerry', 'Windows Phone OS', 'Kindle', 'Kindle Fire', 'Playbook')) or in_array($_agent['browser'], array('IEMobile'))) {
            define('THEME_NAME', $config['theme_mobile']);
            Registry::$settings['config']['front_controller'] = Registry::$settings['config']['front_controller_mobile'];
        } else {
            define('THEME_NAME', $config['theme_default']);
        }

        if (empty($url[0])) {
            $controller = Registry::$settings['config']['front_controller'];
            $this->controller = new $controller();
            $this->controller->loadModel($controller);
            $this->controller->index();

            // Statistics
            $uo->Check_online();
            $hits->Visits();
            $hits->Visitors();
            $uo->second = Registry::$settings['statistics']['check_online_second'];
            return false;
        }
        if (strtolower($url[0]) == "dashboard") {// Dashboard
            // Load language
            $lang->loadPhrases("admin." . Language::$lang_code);

            if (empty($url[1])) {
                $controller = Registry::$settings['config']['admin_controller'];
                $this->controller = new $controller();
                $this->controller->loadModel($controller);
                $this->controller->index();
                return false;
            }

            $controller = ucfirst($url[1]) . "Admin";
            if (class_exists($controller)) {
                $this->controller = new $controller();
                $this->controller->loadModel($controller);
            } else {
                $this->controller = new Controller();
                $this->controller->view->render_404();
                return FALSE;
            }

            /* Call Methods */
            if (isset($url[3])) {
                if (method_exists($this->controller, $url[3])) {
                    $this->controller->{$url[3]}($url[2]);
                } else {
                    Debug::throwException("Method \"{$url[3]}\" does not exists.", NULL);
                }
            } else if (isset($url[2])) {
                if (method_exists($this->controller, $url[2])) {
                    $this->controller->{$url[2]}();
                } else {
                    Debug::throwException("Method \"{$url[2]}\" does not exists.", NULL);
                }
            } else {
                $this->controller->index();
            }
        } else {// Frontend
            // Load language
            $lang->loadPhrases(Language::$lang_code);

            // Statistics
            $uo->Check_online();
            $hits->Visits();
            $hits->Visitors();
            $uo->second = Registry::$settings['statistics']['check_online_second'];

            $controller = ucfirst($url[0]);
            if (class_exists($controller)) {
                $this->controller = new $controller();
                $this->controller->loadModel($controller);
            } else {
                $this->controller = new Controller();
                $this->controller->view->render_404();
                return FALSE;
            }

            /* Call Methods */
            if (isset($url[2])) {
                if (method_exists($this->controller, $url[2])) {
                    $this->controller->{$url[2]}($url[1]);
                } else {
                    Debug::throwException("Method \"{$url[2]}\" does not exists.", NULL);
                }
            } else if (isset($url[1])) {
                if (in_array(strtolower($url[0]), array('tour', 'hotel', 'post', 'page', 'category','postcategory', 'destination', 'booking'))) {
                    if (method_exists($this->controller, 'index')) {
                        $this->controller->index($url[1]);
                    } else {
                        Debug::throwException("Method \"index\" does not exists.", NULL);
                    }
                } else {
                    if (method_exists($this->controller, $url[1])) {
                        $this->controller->{$url[1]}();
                    } else {
                        Debug::throwException("Method \"{$url[1]}\" does not exists.", NULL);
                    }
                }
            } else {
                $this->controller->index();
            }
        }
    }

    function loadLibraries() {
        foreach (Registry::$settings['autoload']['libraries'] as $library) {
            $filename = SYS_PATH . 'libraries' . DS . $library . '.php';
            if (file_exists($filename)) {
                Registry::instance($library);
            } else {
                Debug::throwException("Cannot load libraries!", "The following library <strong>\"$library\"</strong> could not be found under <strong>\"$filename\"</strong>");
            }
        }
    }
}
