<?php

class Install extends Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
//        $this->model->create();
        echo 'Install complete!';
    }

    function create_license() {
        $domain = Registry::$settings['config']['domain'];
        $secret_key = Hash::generate_salt();
        $encrypt = strtoupper(Hash::create('SHA1', $domain, $secret_key));
        $part1 = substr($encrypt, 0, 10);
        $part2 = substr($encrypt, 10, 10);
        $part3 = substr($encrypt, 20, 10);
        $part4 = substr($encrypt, 30, 10);
        $access_key = $part4 . "-" . $part1 . "-" . $part3 . "-" . $part2;
        echo "Secret key: <input type='text' value='$secret_key' onclick='this.focus();this.select();' size='100' />";
        echo '<br />';
        echo "Access key: <input type='text' value='$access_key' onclick='this.focus();this.select();' size='100' />";
    }

}
