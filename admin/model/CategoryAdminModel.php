<?php

class CategoryAdminModel extends Model {

    private $taxonomy = 'post';

    function __construct() {
        parent::__construct();
    }

    public function allCategories() {
        return $this->DB()->select(TABLE_PREFIX . "categories", "*", array(
                    'taxonomy' => $this->taxonomy,
                    'lang_code' => Language::$lang_content,
        ));
    }

    function create($data) {
        return $this->DB()->insert(TABLE_PREFIX . "categories", $data);
    }

    function update($data, $where) {
        $this->DB()->update(TABLE_PREFIX . "categories", $data, $where);
    }

    function delete($id) {
        return $this->DB()->delete(TABLE_PREFIX . 'categories', "id=" . $id);
    }

    function deleteBulk($where) {
        return $this->DB()->delete(TABLE_PREFIX . 'categories', $where);
    }

    function getCategoryByID($id) {
        return $this->DB()->select(TABLE_PREFIX . "categories", "*", array(
                    'taxonomy' => $this->taxonomy,
                    'id' => $id,
        ));
    }

    function getCategoryByName($name) {
        return $this->DB()->select(TABLE_PREFIX . "categories", "*", array(
                    'taxonomy' => $this->taxonomy,
                    'name' => $name,
                    'lang_code' => Language::$lang_content,
        ));
    }

    function getCategoryBySlug($slug) {
        return $this->DB()->select(TABLE_PREFIX . "categories", "*", array(
                    'taxonomy' => $this->taxonomy,
                    'slug' => $slug,
                    'lang_code' => Language::$lang_content,
        ));
    }

    function countPostByCatID($cat_ID) {
        $result = $this->DB()->select(TABLE_PREFIX . "term_relationships", "COUNT(id) AS total", array(
            'object_type' => $this->taxonomy,
            'taxonomy_id' => $cat_ID,
        ));
        return $result[0]['total'];
    }

    function countChildByCatID($cat_ID) {
        $result = $this->DB()->select(TABLE_PREFIX . "categories", "COUNT(id) AS total", array(
            'taxonomy' => $this->taxonomy,
            'parent' => $cat_ID,
        ));
        return $result[0]['total'];
    }

    /**
     * @param bool $has_none First item is none
     * @return array 
     */
    public function categoryOptions($has_none = true) {
        $result = array();
        if ($has_none) {
            $result = array('0' => Language::$phrases['context']['none']);
        }
        $categories = $this->DB()->select(TABLE_PREFIX . "categories", "*", array(
            'taxonomy' => $this->taxonomy,
            'parent' => 0,
            'lang_code' => Language::$lang_content,
        ));
        foreach ($categories as $category) {
            $result[$category['id']] = $category['name'];
            $childs = $this->categoryChildOptions($category['id'], 0);
            $result = $result + $childs;
        }
        return $result;
    }

    /**
     * 
     * @param int $parent Category parent
     * @return array 
     */
    function categoryChildOptions($parent, $indent = 0) {
        $result = array();
        $categories = $this->DB()->select(TABLE_PREFIX . "categories", "*", array(
            'taxonomy' => $this->taxonomy,
            'parent' => intval($parent),
            'lang_code' => Language::$lang_content,
        ));
        foreach ($categories as $category) {
            $result[$category['id']] = Utils::indentSpace($indent + 4) . $category['name'];
            $childs = $this->categoryChildOptions($category['id'], $indent + 4);
            $result = $result + $childs;
        }
        return $result;
    }

    /**
     * @param string $search_query Query string
     * @return array 
     */
    function categoryRowsTable($search_query = "") {
        $result = array();
        $where = array();
        if ($search_query == "") {
            $where = array(
                'taxonomy' => $this->taxonomy,
                'parent' => 0,
                'lang_code' => Language::$lang_content,
            );
        } else {
            $where = "taxonomy = '{$this->taxonomy}' AND parent = 0 AND "
                    . "(name LIKE '%$search_query%' OR slug LIKE '%$search_query%' OR "
                    . "description LIKE '%$search_query%')";
        }
        $categories = $this->DB()->select(TABLE_PREFIX . "categories", "*", $where);
        foreach ($categories as $category) {
            $result[] = $category;
            $childs = $this->categoryChildRowsTable($category['id'], 0, $search_query);
            foreach ($childs as $child) {
                $result[] = $child;
            }
        }
        return $result;
    }

    /**
     * 
     * @param int $parent Category parent
     * @param string $search_query Query string
     * @return array 
     */
    function categoryChildRowsTable($parent, $indent = 0, $search_query = "") {
        $result = array();
        $where = array();
        if ($search_query == "") {
            $where = array(
                'taxonomy' => $this->taxonomy,
                'parent' => intval($parent),
                'lang_code' => Language::$lang_content,
            );
        } else {
            $where = "taxonomy = '{$this->taxonomy}' AND parent = $parent AND "
                    . "(name LIKE '%$search_query%' OR slug LIKE '%$search_query%' OR "
                    . "description LIKE '%$search_query%')";
        }
        $categories = $this->DB()->select(TABLE_PREFIX . "categories", "*", $where);
        foreach ($categories as $category) {
            $category['name'] = Utils::indentDash($indent + 2) . " " . $category['name'];
            $result[] = $category;
            $childs = $this->categoryChildRowsTable($category['id'], $indent + 2, $search_query);
            foreach ($childs as $child) {
                $result[] = $child;
            }
        }
        return $result;
    }

}
