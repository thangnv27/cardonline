<?php

class View {

    public function __construct() {
        
    }

    public function getTemplate() {
        return new Template();
    }

    public function render($name, $parameters = array()) {
        $smarty = $this->getTemplate()->Smarty();
        foreach ($parameters as $key => $value) {
            $this->$key = $value;
            $smarty->assign($key, $value);
        }
        $template_file = APP_PATH . 'view' . DS . THEME_NAME . DS . $name;
        if (file_exists($template_file)) {
            if (MINIFY_HTML == TRUE) {
                include(SYS_PATH . 'inc' . DS . 'bbit-compress.php');
            }
            $pathinfo = pathinfo($template_file);
            $ext = strtolower($pathinfo['extension']);
            switch ($ext) {
                case "tpl":
                    $smarty->display($template_file);
                    break;
                case "php":
                    require $template_file;
                    break;
                default:
                    Debug::throwException("Unsupported!", "Not support the extension \"" . $ext . "\".\n\nFile: " . $template_file);
                    break;
            }
        } else {
            Debug::throwException("Template error!", "File \"" . $template_file . "\" does not exists.");
        }
    }

    public function render_403() {
        $file = BASE_PATH . DS . 'public' . DS . "403.html";
        include $file;
    }

    public function render_404() {
        $file = BASE_PATH . DS . 'public' . DS . "404.html";
        include $file;
    }

    public function render_405() {
        $file = BASE_PATH . DS . 'public' . DS . "405.html";
        include $file;
    }

    public function render_500() {
        $file = BASE_PATH . DS . 'public' . DS . "500.html";
        include $file;
    }

    public function render_503() {
        $file = BASE_PATH . DS . 'public' . DS . "503.html";
        include $file;
    }

    public function getSession() {
        return new Session();
    }

}
