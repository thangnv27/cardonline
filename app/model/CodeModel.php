<?php

class CodeModel extends Model {

    function __construct() {
        parent::__construct();
    }

    public function getCode($limit, $where = array()) {
        try {
            $db = $this->DB();
            if (!empty($where)) {
                $where = "WHERE "  . $db->where($where);
            }
            $sql = "SELECT * FROM " . TABLE_PREFIX . "code " . $where . " ORDER BY id ASC LIMIT 0, $limit";
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
    
    public function PurchaseCode($data, $where = array()) {
        $db = $this->DB();
        $db->update(TABLE_PREFIX . "code", array('user_id' => $data['user_id']), $where);
        $this->DB()->insert(TABLE_PREFIX . "payment_history", array(
            'user_id' => $data['user_id'],
            'count' => $data['count'],
            'code' => $data['code'],
            'amount'=> $data['amount']
        ));
    }

}
