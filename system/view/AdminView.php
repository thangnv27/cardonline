<?php

class AdminView extends View {

    function __construct() {
        parent::__construct();
    }

    function render($name, $parameters = array()) {
        foreach ($parameters as $key => $value) {
            $this->$key = $value;
        }
        $template_file = ADMIN_PATH . 'view' . DS . $name . ".php";
        if (file_exists($template_file)) {
            require $template_file;
        } else {
            Debug::throwException("Template error!", "File \"" . $template_file . "\" does not exists.");
        }
    }

}
