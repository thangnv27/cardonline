<?php

class UserGroupAdminModel extends Model {

    function __construct() {
        parent::__construct();
    }

    function getUserGroups() {
        return $this->DB()->select(TABLE_PREFIX . "usergroups", "*");
    }

    function getGroupByID($id) {
        return $this->DB()->select(TABLE_PREFIX . "usergroups", "*", array(
                    'id' => $id,
        ));
    }

    function updateCapability($capability, $id) {
        $this->DB()->update(TABLE_PREFIX . "usergroups", array('capability' => serialize($capability)), array('id' => $id));
    }

    /**
     * 
     * @param string $name
     * @return boolean
     */
    function isNameExists($name) {
        $usergroup = $this->DB()->select(TABLE_PREFIX . "usergroups", 'name', array('name' => $name));
        if (count($usergroup) == 0) {
            return false;
        } else {
            return true;
        }
    }

    function update($name, $id) {
        $this->DB()->update(TABLE_PREFIX . "usergroups", array('name' => $name), array('id' => $id));
    }

}
