<?php

class LanguageAdminModel extends Model {

    function __construct() {
        parent::__construct();
    }

    public static function getLangActived() {
        try {
            $db = new Database();
            $table_name = TABLE_PREFIX . "languages";
            if ($db->check_exists_table($table_name)) {
                $lang = $db->select($table_name, "*", array('active' => 1));
                if (empty($lang)) {
                    return false;
                } else {
                    return $lang[0];
                }
            }
            return false;
        } catch (Exception $exc) {
            return false;
        }
    }

    /**
     * @param string $search_query Query string
     * @return array 
     */
    function getLanguages($search_query = "") {
        $where = array();
        if ($search_query != "") {
            $where = "english_name LIKE '%$search_query%' OR code LIKE '%$search_query%' OR "
                    . "iso LIKE '%$search_query%' OR locale LIKE '%$search_query%'";
        }
        return $this->DB()->select(TABLE_PREFIX . "languages", "*", $where);
    }

    function getLanguageByID($id) {
        $langs = $this->DB()->select(TABLE_PREFIX . "languages", "*", array(
            'id' => $id,
        ));
        return $langs;
    }

    function active($id) {
        $this->DB()->update(TABLE_PREFIX . 'languages', array('active' => 0), array('active' => 1));
        $this->DB()->update(TABLE_PREFIX . 'languages', array('active' => 1), array('id' => $id));
    }

}
