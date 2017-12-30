<?php

class TPL {

    public static function getSiteOption() {
        $option = file_get_contents(DB_PATH . 'options.json');
        $option = json_decode($option);
        $option->site_option->footer_info = stripslashes($option->site_option->footer_info);
        return $option->site_option;
    }

    public static function CodeCoin() {
        $option = file_get_contents(DB_PATH . 'options.json');
        $option = json_decode($option);
        return $option->code_coin;
    }
    public static function getPaymentRate() {
        $option = file_get_contents(DB_PATH . 'options.json');
        $option = json_decode($option);
        return $option->payment_rate;
    }

    public static function getMenu() {
        if (!class_exists('MenuModel')) {
            require_once APP_PATH . 'model' . DS . 'MenuModel.php';
        }
        $menu = new MenuModel();
        $primary_menu = $menu->getNavigation('primary_menu');
        $second_menu = $menu->get_second_menu();
        return array(
            'primary_menu' => $primary_menu,
            'second_menu' => $second_menu
        );
    }

}
