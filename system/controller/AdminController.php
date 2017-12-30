<?php

class AdminController extends Controller {

    function __construct() {
        parent::__construct();
        $this->view = new AdminView();
    }
/*
    public function loadModel($name) {
        $path = ADMIN_PATH . 'model' . DS . ucfirst($name) . '.php';
        if (file_exists($path)) {
            $modelName = ucfirst($name) . 'Model';
            $this->model = new $modelName();
        }
    }
*/
}
