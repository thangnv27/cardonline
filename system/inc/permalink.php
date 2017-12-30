<?php

function the_permalink($slug, $type = "") {
    $link = Registry::$siteurl . '/' . $type . '/' . $slug;
    if (empty($type)) {
        $link = Registry::$siteurl . '/' . $slug;
    }
    echo trailingslashit($link);
}

function get_permalink($slug, $type = "") {
    $link = Registry::$siteurl . '/' . $type . '/' . $slug;
    if (empty($type)) {
        $link = Registry::$siteurl . '/' . $slug;
    }
    return trailingslashit($link);
}

function get_user_login_url() {
    return trailingslashit(Registry::$siteurl . "/user/login");
}

function get_admin_login_url() {
    return trailingslashit(DASHBOARD_URL . "/user/login");
}
