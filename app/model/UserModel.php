<?php

class UserModel extends Model {

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

    /**
     * 
     * @param string $username
     * @return boolean
     */
    public function isUsernameExists($username) {
        $user = $this->DB()->select(TABLE_PREFIX . "users", 'username', array('username' => $username));
        if (count($user) == 0) {
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
        $user = $this->DB()->select(TABLE_PREFIX . "users", 'email', array('email' => $email));
        if (count($user) == 0) {
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

    public function getUserLogin($where) {
        try {
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
        } catch (Exception $exc) {
            Debug::throwException("Database error!", $exc);
            return FALSE;
        }
    }

    /**
     * Create a new User
     * @param array $data 
     * @return int|bool
     */
    public function createUser($data) {
        return $this->DB()->insert(TABLE_PREFIX . "users", $data);
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
     * @param string $password
     * @param int $user_id
     */
    public function updatePassword($password, $user_id) {
        return $this->DB()->update(TABLE_PREFIX . "users", array('password' => $password), array('id' => $user_id));
    }

    public function updateCoin($coin, $user_id) {
        $users = $this->DB()->select(TABLE_PREFIX . "usermeta", array('user_id', 'coin'), array('user_id' => $user_id));

        if (empty($users)) {
            $meta['user_id'] = $user_id;
            $meta['coin'] = $coin;
            $this->createUserMeta($meta);
        } else {
            $coin = intval($users[0]['coin']) + $coin;
            $this->DB()->update(TABLE_PREFIX . "usermeta", array('coin' => $coin), array('user_id' => $user_id));
        }
    }

    public function get_user_coin($user_id) {
        $users = $this->DB()->select(TABLE_PREFIX . "usermeta", array('coin'), array('user_id' => $user_id));

        if (!empty($users)) {
            $coin = $users[0]['coin'];
            return $coin;
        }
    }

    public function updatecoin_buy($tien_tru, $user_id) {
        $users = $this->DB()->select(TABLE_PREFIX . "usermeta", array('user_id', 'coin'), array('user_id' => $user_id));
        $coin = intval($users[0]['coin']) - $tien_tru;
        $this->DB()->update(TABLE_PREFIX . "usermeta", array('coin' => $coin), array('user_id' => $user_id));
    }

    public function update_code_history($user_id,$count, $code, $amount) {
        return $this->DB()->insert(TABLE_PREFIX . "payment_history", array(
            'user_id' => $user_id,
            'count' => $count,
            'code' => $code,
            'amount'=> $amount
        ));
    }
    
        public function get_code_history($user_id) {
        $history = $this->DB()->select(TABLE_PREFIX . "payment_history", '*', array('user_id' => $user_id));

        if (!empty($history)) {
            return $history;
        }
    }
    public function recharge_history($user_id,$pin, $serial, $network, $value) {
        return $this->DB()->insert(TABLE_PREFIX . "card", array(
            'user_id' => $user_id,
            'pin' => $pin,
            'serial' => $serial,
            'value' => $value,
            'network'=> $network
        ));
    }
    
        public function get_recharge_history($user_id) {
        $history = $this->DB()->select(TABLE_PREFIX . "card", '*', array('user_id' => $user_id));

        if (!empty($history)) {
            return $history;
        }
    }

}
