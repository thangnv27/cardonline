<?php

class PageAdminModel extends Model {

    function __construct() {
        parent::__construct();
    }

    function create($data) {
        return $this->DB()->insert(TABLE_PREFIX . "pages", $data);
    }

    function update($data, $where) {
        $this->DB()->update(TABLE_PREFIX . "pages", $data, $where);
    }

    function delete($id) {
        $this->DB()->delete(TABLE_PREFIX . 'pages', array(
            'id' => $id
        ));
    }

    function publish($id) {
        return $this->DB()->update(TABLE_PREFIX . 'pages', array('post_status' => 'published'), array(
            'id' => $id
        ));
    }

    function publishBulk($where) {
        return $this->DB()->update(TABLE_PREFIX . 'pages', array('post_status' => 'published'), $where);
    }

    function move2trashBulk($where) {
        return $this->DB()->update(TABLE_PREFIX . 'pages', array('post_status' => 'trashed'), $where);
    }

    public function countPages($where = array()) {
        $result = $this->DB()->select(TABLE_PREFIX . "pages", "COUNT(id) AS total", $where);
        return $result[0]['total'];
    }

    public function getPages($start, $limit, $where = array()) {
        try {
            $db = $this->DB();
            if (!empty($where)) {
                $where = "WHERE lang_code = '" . Language::$lang_content . "' AND " . $db->where($where);
            }
            $sql = "SELECT * FROM " . TABLE_PREFIX . "pages " . $where . " ORDER BY id DESC LIMIT $start, $limit";
            $stm = $db->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            if (DEBUG == TRUE) {
                Debug::throwException("Database error!", $exc);
            }
            return array();
        }
    }

    function getPageByTitle($title) {
        return $this->DB()->select(TABLE_PREFIX . "pages", "*", array(
                    'title' => $title,
                    'lang_code' => Language::$lang_content,
        ));
    }

    function getPageByMD5Title($title_md5) {
        return $this->DB()->select(TABLE_PREFIX . "pages", "*", array(
                    'title_md5' => $title_md5,
                    'lang_code' => Language::$lang_content,
        ));
    }

    function getPageBySlug($slug) {
        return $this->DB()->select(TABLE_PREFIX . "pages", "*", array(
                    'slug' => $slug,
                    'lang_code' => Language::$lang_content,
        ));
    }

    function getPageByID($id) {
        $result = $this->DB()->select(TABLE_PREFIX . "pages", "*", array(
            'id' => $id,
        ));
        return $result;
    }

}
