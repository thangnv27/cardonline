<?php

class Welcome extends Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        $userid = 0;
        if($_SESSION['user_logged_in']){
            $userid = $_SESSION['user_logged_in']['id'];
        }
        
        $usrMD = new UserModel();
        $recharge_history = $usrMD->get_recharge_history($userid);
        $his = $usrMD->get_code_history($userid);
        $coin = $usrMD->get_user_coin($userid);
        
        $this->render('index.tpl', array(
            'title' => "Trang chủ",
            'coin' => $coin,
            'history' => $his,
            'recharge_history' => $recharge_history
        ));
    }

}
