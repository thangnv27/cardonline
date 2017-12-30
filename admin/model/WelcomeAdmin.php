<?php

class WelcomeAdminModel extends Model {

    function __construct() {
        parent::__construct();
    }

    function countPosts() {
        $posts = $this->DB()->select(TABLE_PREFIX . "posts", "COUNT(id) AS total", array(
            'post_status' => 'published'
        ));
        return $posts[0]['total'];
    }

    function countProducts() {
        $products = $this->DB()->select(TABLE_PREFIX . "products", "COUNT(id) AS total", array(
            'post_status' => 'published'
        ));
        return $products[0]['total'];
    }

    function countUsers() {
        $users = $this->DB()->select(TABLE_PREFIX . "users", "COUNT(id) AS total", array(
            'is_deleted' => 0
        ));
        return $users[0]['total'];
    }

    function countPendingOrders() {
        $posts = $this->DB()->select(TABLE_PREFIX . "orders", "COUNT(id) AS total", array('status' => 0));
        return $posts[0]['total'];
    }

}
