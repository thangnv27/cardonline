<?php

class MenuAdminModel extends Model {

    function __construct() {
        parent::__construct();
    }

    function create($data) {
        return $this->DB()->insert(TABLE_PREFIX . "menu", $data);
    }

    function update($data, $where) {
        $this->DB()->update(TABLE_PREFIX . "menu", $data, $where);
    }

    function delete($id) {
        return $this->DB()->delete(TABLE_PREFIX . 'menu', "id=" . $id);
    }

    function getMenuByID($id) {
        return $this->DB()->select(TABLE_PREFIX . "menu", "*", array(
                    'id' => $id,
        ));
    }

    /**
     * @param string $name Menu Name
     * @param bool $has_none First item is none
     * @return array 
     */
    public function menuOptions($name, $has_none = true) {
        $result = array();
        if ($has_none) {
            $result = array('0' => Language::$phrases['context']['none']);
        }
        $menus = $this->DB()->select(TABLE_PREFIX . "menu", "*", array(
            'name' => $name,
            'parent' => 0,
            'lang_code' => Language::$lang_content,
        ));
        foreach ($menus as $menu) {
            $result[$menu['id']] = $menu['title'];
            $childs = $this->menuChildOptions($name, $menu['id'], 0);
            $result = $result + $childs;
        }
        return $result;
    }

    /**
     * 
     * @param int $parent Menu parent
     * @return array 
     */
    function menuChildOptions($name, $parent, $indent = 0) {
        $result = array();
        $menus = $this->DB()->select(TABLE_PREFIX . "menu", "*", array(
            'name' => $name,
            'parent' => intval($parent),
            'lang_code' => Language::$lang_content,
        ));
        foreach ($menus as $menu) {
            $result[$menu['id']] = Utils::indentSpace($indent + 4) . $menu['title'];
            $childs = $this->menuChildOptions($name, $menu['id'], $indent + 4);
            $result = $result + $childs;
        }
        return $result;
    }

    /**
     * 
     * @return array 
     */
    function menuRowsTable($name) {
        $result = array();
        $menus = $this->DB()->select(TABLE_PREFIX . "menu", "*", array(
            'lang_code' => Language::$lang_content,
            'name' => $name,
            'parent' => 0,
                ), "displayorder");
        foreach ($menus as $menu) {
            $result[] = $menu;
            $childs = $this->menuChildRowsTable($menu['id'], 0, $name);
            foreach ($childs as $child) {
                $result[] = $child;
            }
        }
        return $result;
    }

    /**
     * 
     * @param int $parent Menu parent
     * @param string $name
     * @return array 
     */
    function menuChildRowsTable($parent, $indent = 0, $name = "") {
        $result = array();
        $menus = $this->DB()->select(TABLE_PREFIX . "menu", "*", array(
            'lang_code' => Language::$lang_content,
            'name' => $name,
            'parent' => intval($parent),
                ), "displayorder");
        foreach ($menus as $menu) {
            $menu['title'] = Utils::indentDash($indent + 2) . " " . $menu['title'];
            $result[] = $menu;
            $childs = $this->menuChildRowsTable($menu['id'], $indent + 2, $name);
            foreach ($childs as $child) {
                $result[] = $child;
            }
        }
        return $result;
    }

}
