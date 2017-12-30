<?php

class FileAdmin extends AdminController {

    private $current_user;

    function __construct() {
        parent::__construct();
        $this->current_user = UserAdmin::checkLogin();
    }
    
    function index() {
        $this->render("file", array(
            'title' => "File Management",
        ));
    }

}
