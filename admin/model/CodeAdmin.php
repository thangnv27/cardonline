<?php

class CodeAdminModel extends Model {

    function __construct() {
        parent::__construct();
    }

    function create($data) {
        return $this->DB()->insert(TABLE_PREFIX . "code", $data);
    }

    function update($data, $where) {
        $this->DB()->update(TABLE_PREFIX . "code", $data, $where);
    }

    function delete($id) {
        $this->DB()->delete(TABLE_PREFIX . 'code', array(
            'id' => $id
        ));
    }

    function publish($id) {
        return $this->DB()->update(TABLE_PREFIX . 'code', array('code_status' => 'published'), array(
            'id' => $id
        ));
    }

    function publishBulk($where) {
        return $this->DB()->update(TABLE_PREFIX . 'code', array('code_status' => 'published'), $where);
    }

    function move2trashBulk($where) {
        return $this->DB()->update(TABLE_PREFIX . 'code', array('code_status' => 'trashed'), $where);
    }

    public function countPages($where = array()) {
        $result = $this->DB()->select(TABLE_PREFIX . "code", "COUNT(id) AS total", $where);
        return $result[0]['total'];
    }

    public function getPages($start, $limit, $where = array()) {
        try {
            $db = $this->DB();
            if (!empty($where)) {
                $where = "WHERE "  . $db->where($where);
            }
            $sql = "SELECT * FROM " . TABLE_PREFIX . "code " . $where . " ORDER BY id DESC LIMIT $start, $limit";
            
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

    function getPageBySlug($slug) {
        return $this->DB()->select(TABLE_PREFIX . "code", "*", array(
                    'slug' => $slug,
                    'lang_code' => Language::$lang_content,
        ));
    }

    function getCodeByID($id) {
        $result = $this->DB()->select(TABLE_PREFIX . "code", "*", array(
            'id' => $id,
        ));
        return $result;
    }

}
