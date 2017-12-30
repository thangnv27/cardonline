<?php

class MenuModel extends Model {

    function __construct() {
        parent::__construct();
    }

    function getMenuSlider() {
        return $this->DB()->select(TABLE_PREFIX . "menu", "*", array(
                    'name' => 'menu_slide',
                    'lang_code' => Language::$lang_content,
        ), "displayorder");
    }

    function get_second_menu() {
        return $this->DB()->select(TABLE_PREFIX . "menu", "*", array(
                    'name' => 'second_menu',
                        ), 'displayorder');
    }

    public function getNavigation($name) {
        $currentURL = getCurrentRquestUrl();
        $currentURL = parse_url($currentURL);
        $currentURL = trailingslashit($currentURL['host'] . $currentURL['path']);
        $result = '<ul class="nav navbar-nav">';
        $menus = $this->DB()->select(TABLE_PREFIX . "menu", "*", array(
            'name' => $name,
            'parent' => 0,
            'lang_code' => Language::$lang_content,
                ), 'displayorder');
        foreach ($menus as $menu) {
            $childs = $this->getNavigationChilds($name, $menu['id']);
            $url = parse_url($menu['url']);
            $url = trailingslashit($url['host'] . $url['path']);
            $class_attr = "";
            if($url == $currentURL){
                $class_attr .= " active current_menu";
            }
            if (empty($childs)) {
                $result .= '<li class="menu-item menu-item-' . $menu['id'] . $class_attr . '"><a href="' . $menu['url'] . '">' . $menu['title'] . '</a></li>';
            } else {
                $result .= '<li class="dropdown menu-item menu-item-' . $menu['id'] . $class_attr . ' parent_menu">';
                $result .= '<a href="' . $menu['url'] . '" class="dropdown-toggle" >' . $menu['title'] . ' <span class="caret"></a>';
                $result .= $childs;
                $result .= '</li>';
            }
        }
        $result .= '</ul>';
        return $result;
    }

    function getNavigationChilds($name, $parent) {
        $result = "";
        $menus = $this->DB()->select(TABLE_PREFIX . "menu", "*", array(
            'name' => $name,
            'parent' => intval($parent),
            'lang_code' => Language::$lang_content,
                ), 'displayorder');
        if (!empty($menus)) {
            $result .= '<ul class="dropdown-menu" role="menu">';
            foreach ($menus as $menu) {
                $childs = $this->getNavigationChilds($name, $menu['id']);
                if (empty($childs)) {
                    $result .= '<li class="menu-item menu-item-' . $menu['id'] . '"><a href="' . $menu['url'] . '">' . $menu['title'] . '</a></li>';
                } else {
                    $result .= '<li class="menu-item menu-item-' . $menu['id'] . '">';
                    $result .= '<a href="' . $menu['url'] . '">' . $menu['title'] . '</a>';
                    $result .= $childs;
                    $result .= '</li>';
                }
            }
            $result .= '</ul>';
        }
        return $result;
    }

}
