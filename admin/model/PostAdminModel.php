<?php

class PostAdminModel extends Model {

    function __construct() {
        parent::__construct();
    }

    function create($data) {
        return $this->DB()->insert(TABLE_PREFIX . "posts", $data);
    }

    function createTerms($data) {
        return $this->DB()->insert(TABLE_PREFIX . "term_relationships", $data);
    }

    function update($data, $where) {
        $this->DB()->update(TABLE_PREFIX . "posts", $data, $where);
    }

    function delete($id) {
        $db = $this->DB();
        $db->delete(TABLE_PREFIX . 'posts', array(
            'id' => $id
        ));
        $db->delete(TABLE_PREFIX . 'postmeta', array(
            'post_id' => $id
        ));
        $db->delete(TABLE_PREFIX . 'term_relationships', array(
            'object_id' => $id,
            'object_type' => 'post',
        ));
    }

    function publish($id) {
        return $this->DB()->update(TABLE_PREFIX . 'posts', array('post_status' => 'published'), array(
            'id' => $id
        ));
    }

    function publishBulk($where) {
        return $this->DB()->update(TABLE_PREFIX . 'posts', array('post_status' => 'published'), $where);
    }

    function move2trashBulk($where) {
        return $this->DB()->update(TABLE_PREFIX . 'posts', array('post_status' => 'trashed'), $where);
    }

    function updateTerms($post_id, $terms) {
        $this->DB()->delete(TABLE_PREFIX . 'term_relationships', array(
            'object_id' => $post_id,
            'object_type' => 'post',
        ));
        foreach ($terms as $cat_ID) {
            $this->createTerms(array(
                'object_id' => $post_id,
                'object_type' => 'post',
                'taxonomy_id' => $cat_ID,
            ));
        }
    }

    public function countPosts($where = array()) {
        $result = $this->DB()->select(TABLE_PREFIX . "posts", "COUNT(id) AS total", $where);
        return $result[0]['total'];
    }

    public function getPosts($start, $limit, $where = array(), $category = "") {
        try {
            $db = $this->DB();
            if (!empty($where)) {
                $where = "WHERE P.lang_code = '" . Language::$lang_content . "' AND " . $db->where($where);
            }
            $sql = "SELECT P.*, U.username FROM " . TABLE_PREFIX . "posts P JOIN " . TABLE_PREFIX . "users U 
                ON P.user_id = U.id " . $where . " ORDER BY P.id DESC LIMIT $start, $limit";
            if (is_numeric($category) and $category > 0) {
                $sql = "SELECT P.*, U.username FROM " . TABLE_PREFIX . "posts P 
                        JOIN " . TABLE_PREFIX . "users U ON P.user_id = U.id 
                        JOIN " . TABLE_PREFIX . "term_relationships T ON P.id = T.object_id 
                        WHERE P.lang_code = '" . Language::$lang_content . "' AND T.object_type = 'post' AND taxonomy_id = $category AND post_status IN ('draft','published') ORDER BY P.id DESC LIMIT $start, $limit";
            }
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

    function getPostByTitle($title) {
        return $this->DB()->select(TABLE_PREFIX . "posts", "*", array(
                    'title' => $title,
                    'lang_code' => Language::$lang_content,
        ));
    }

    function getPostByMD5Title($title_md5) {
        return $this->DB()->select(TABLE_PREFIX . "posts", "*", array(
                    'title_md5' => $title_md5,
                    'lang_code' => Language::$lang_content,
        ));
    }

    function getPostBySlug($slug) {
        return $this->DB()->select(TABLE_PREFIX . "posts", "*", array(
                    'slug' => $slug,
                    'lang_code' => Language::$lang_content,
        ));
    }

    function getPostByID($id) {
        $post = $this->DB()->select(TABLE_PREFIX . "posts", "*", array(
            'id' => $id,
        ));
        return $post;
    }

    function getAllTags() {
        $result = "";
        $tags = $this->DB()->select(TABLE_PREFIX . "posts", "tags");
        foreach ($tags as $key => $tag) {
            $tmp = explode(",", $tag['tags']);
            if ($key >= count($tags) - 1) {
                foreach ($tmp as $k => $v) {
                    if ($k >= count($tmp) - 1) {
                        $result .= '"' . $v . '"';
                    } else {
                        $result .= '"' . $v . '",';
                    }
                }
            } else {
                foreach ($tmp as $k => $v) {
                    $result .= '"' . $v . '",';
                }
            }
        }
        return $result;
    }

}
