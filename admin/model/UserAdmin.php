<?php

class UserAdminModel extends Model {

    function __construct() {
        parent::__construct();
    }

    public function getSalt($username) {
        $user = $this->DB()->select(TABLE_PREFIX . "users", array('salt'), array('username' => $username));
        if (!empty($user)) {
            return $user[0]['salt'];
        } else {
            return "";
        }
    }

    public function getUserLogin($where) {
        $db = $this->DB();
        $stm = $db->prepare("SELECT * FROM " . TABLE_PREFIX . "users U LEFT JOIN " . TABLE_PREFIX . "usermeta UM 
                ON U.id = UM.user_id WHERE " . $db->where($where) . " LIMIT 1");
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                $stm->bindValue(":$key", $value);
            }
        }
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get User by field is unique: id, username, email
     * @param int|string $val Value of [id, username, email]
     * @return null
     */
    public function getUserByFieldUnique($val) {
        try {
            $sql = "SELECT * FROM " . TABLE_PREFIX . "users U LEFT JOIN " . TABLE_PREFIX . "usermeta UM 
                ON U.id = UM.user_id WHERE U.username = '$val' OR U.email = '$val' LIMIT 1";
            if (is_numeric($val)) {
                $sql = "SELECT * FROM " . TABLE_PREFIX . "users U LEFT JOIN " . TABLE_PREFIX . "usermeta UM 
                    ON U.id = UM.user_id WHERE U.id = $val LIMIT 1";
            }
            $db = $this->DB();
            $stm = $db->prepare($sql);
            $stm->execute();
            $user = $stm->fetchAll(PDO::FETCH_ASSOC);
            if (count($user) == 0) {
                return null;
            } else {
                $user[0]['user_id'] = $user[0]['id'];
                $user[0]['capability'] = @unserialize($user[0]['capability']);
                return $user[0];
            }
        } catch (Exception $exc) {
            if (DEBUG == TRUE) {
                Debug::throwException("Database error!", $exc);
            }
            return null;
        }
    }

    /**
     * 
     * @param string $username
     * @return boolean
     */
    public function isUsernameExists($username) {
        $result = $this->DB()->select(TABLE_PREFIX . "users", 'COUNT(username) AS total', array('username' => $username));
        if ($result[0]['total'] == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 
     * @param string $email
     * @return boolean
     */
    public function isEmailExists($email) {
        $result = $this->DB()->select(TABLE_PREFIX . "users", 'COUNT(email) AS total', array('email' => $email));
        if ($result[0]['total'] == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getCapabilityByRole($role) {
        $group = $this->DB()->select(TABLE_PREFIX . "usergroups", 'capability', array('role' => $role));
        if (count($group) == 0) {
            return "";
        } else {
            return $group[0]['capability'];
        }
    }

    public function updateCapability($capability, $user_id) {
        $this->DB()->update(TABLE_PREFIX . "users", array('capability' => serialize($capability)), array('id' => $user_id));
    }

    public function getRoles() {
        $roles = array();
        $groups = $this->DB()->select(TABLE_PREFIX . "usergroups");
        foreach ($groups as $group) {
            $roles[$group['role']] = $group['name'];
        }
        return $roles;
    }

    public function getRoleName($role) {
        $group = $this->DB()->select(TABLE_PREFIX . "usergroups", "name", array('role' => $role));
        if (empty($group)) {
            return FALSE;
        } else {
            return $group[0]['name'];
        }
    }

    public function countUsers($where = "") {
        if (empty($where)) {
            $where = array(
                'is_deleted' => 0,
            );
        } else {
            $where .= " AND is_deleted = 0";
        }
        $users = $this->DB()->select(TABLE_PREFIX . "users", "COUNT(id) AS total", $where);
        return $users[0]['total'];
    }

    public function getUsers($start, $limit, $where = "") {
        try {
            $db = $this->DB();
            $sql = "SELECT U.*,G.name as groupname FROM " . TABLE_PREFIX . "users U "
                    . "JOIN " . TABLE_PREFIX . "usergroups G ON U.role = G.role "
                    . "WHERE U.is_deleted=0 ";
            if (!empty($where)) {
                $sql .= "AND " . $where;
            }
            $sql .= " ORDER BY U.id DESC LIMIT $start, $limit";
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

    public function delete($where) {
        $this->DB()->update(TABLE_PREFIX . "users", array('is_deleted' => 1), $where);
    }

    /**
     * Create a new User
     * @param array $basic Base information
     * @return int|bool
     */
    public function createUser($basic) {
        return $this->DB()->insert(TABLE_PREFIX . "users", $basic);
    }

    /**
     * Add meta data for User
     * @param array $meta User meta data
     * @return int|bool
     */
    public function createUserMeta($meta) {
        return $this->DB()->insert(TABLE_PREFIX . "usermeta", $meta);
    }

    /**
     * 
     * @param array $basic Base information
     * @param array $meta User meta data
     */
    public function updateUser($basic, $meta, $user_id) {
        $this->DB()->update(TABLE_PREFIX . "users", $basic, array('id' => $user_id));
        $users = $this->DB()->select(TABLE_PREFIX . "usermeta", array('user_id'), array('user_id' => $user_id));
        if (empty($users)) {
            $meta['user_id'] = $user_id;
            $this->createUserMeta($meta);
        } else {
            $this->DB()->update(TABLE_PREFIX . "usermeta", $meta, array('user_id' => $user_id));
        }
    }

    /**
     * 
     * @param string $password
     * @param int $user_id
     */
    public function updatePassword($password, $user_id) {
        return $this->DB()->update(TABLE_PREFIX . "users", array('password' => $password), array('id' => $user_id));
    }

    public function activateUser($where) {
        $this->DB()->update(TABLE_PREFIX . "users", array('activation_key' => ""), $where);
    }

}
