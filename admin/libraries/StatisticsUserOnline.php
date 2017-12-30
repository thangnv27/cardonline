<?php

class StatisticsUserOnline extends Statistics {

    private $table_name;
    private $timestamp;
    private $agent;
    public $second;
    public $result = null;

    public function __construct($second = 30) {
        parent::__construct();

        $this->table_name = TABLE_PREFIX . "statistics_useronline";
        $this->agent = $this->get_UserAgent();
        $this->timestamp = date('U');
        $this->second = $second;
        if (Registry::$settings['system']['statistics']['check_online_second']) {
            $this->second = Registry::$settings['system']['statistics']['check_online_second'];
        }
    }

    public function Is_user() {
        if ($this->db->check_exists_table($this->table_name)) {
            $this->result = $this->db->select($this->table_name, "*", array(
                'ip' => $this->get_IP(),
                'agent' => $this->agent['browser'],
                'platform' => $this->agent['platform'],
                'version' => $this->agent['version'],
            ));
            if (!empty($this->result)) {
                return true;
            }
        }
        return FALSE;
    }

    public function Check_online() {
        if ($this->Is_user()) {
            $this->Update_user();
        } else {
            $this->Add_user();
        }

        $this->Delete_user();
    }

    public function Add_user() {
        if ($this->db->check_exists_table($this->table_name)) {
            $this->db->insert($this->table_name, array(
                'ip' => $this->get_IP(),
                'timestamp' => $this->timestamp,
                'date' => $this->Current_Date(),
                'referred' => $this->get_Referred(),
                'url' => getCurrentRquestUrl(),
                'agent' => $this->agent['browser'],
                'platform' => $this->agent['platform'],
                'version' => $this->agent['version'],
            ));
        }
    }

    public function Update_user() {
        if ($this->db->check_exists_table($this->table_name)) {
            $this->db->update($this->table_name, array(
                'timestamp' => $this->timestamp,
                'date' => $this->Current_Date(),
                'referred' => $this->get_Referred(),
                'url' => getCurrentRquestUrl(),
                    ), array(
                'ip' => $this->get_IP(),
                'agent' => $this->agent['browser'],
                'platform' => $this->agent['platform'],
                'version' => $this->agent['version'],
            ));
        }
    }

    public function Delete_user() {
        if ($this->db->check_exists_table($this->table_name)) {
            $this->result = $this->timestamp - $this->second;
            $this->db->delete($this->table_name, "timestamp < '$this->result'");
        }
    }

}
