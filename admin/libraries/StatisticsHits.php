<?php

class StatisticsHits extends Statistics {

    public $result = null;
    private $exclusion_match = FALSE;
    private $exclusion_reason = '';
    private $exclusion_record = FALSE;
    private $tbl_visit;
    private $tbl_visitor;
    private $agent;
    private $ip;

    public function __construct() {
        parent::__construct();

        $this->tbl_visit = TABLE_PREFIX . "statistics_visit";
        $this->tbl_visitor = TABLE_PREFIX . "statistics_visitor";
        $this->agent = $this->get_UserAgent();
        $this->ip = $this->get_IP();
    }

    // From: http://www.php.net/manual/en/function.ip2long.php

    private function net_match($network, $ip) {
        // determines if a network in the form of 192.168.17.1/16 or
        // 127.0.0.1/255.255.255.255 or 10.0.0.1 matches a given ip
        $ip_arr = explode('/', $network);

        if (!isset($ip_arr[1])) {
            $ip_arr[1] = 0;
        }

        $network_long = ip2long($ip_arr[0]);

        $x = ip2long($ip_arr[1]);
        $mask = long2ip($x) == $ip_arr[1] ? $x : 0xffffffff << (32 - $ip_arr[1]);
        $ip_long = ip2long($ip);

        return ($ip_long & $mask) == ($network_long & $mask);
    }

    public function Visits() {
        // If we're a webcrawler or referral from ourselves or an excluded address don't record the visit.
        if (!$this->exclusion_match) {
            if ($this->db->check_exists_table($this->tbl_visit)) {
                $this->result = $this->db->get_row($this->tbl_visit, "*", array(), "ID", "DESC");

                if (substr($this->result['last_visit'], 0, -1) != substr($this->Current_Date('Y-m-d H:i:s'), 0, -1)) {

                    if ($this->result['last_counter'] != $this->Current_Date('Y-m-d')) {
                        $this->db->insert($this->tbl_visit, array(
                            'last_visit' => $this->Current_Date(),
                            'last_counter' => $this->Current_date('Y-m-d'),
                            'visit' => $this->coefficient
                        ));
                    } else {
                        $this->db->update($this->tbl_visit, array(
                            'last_visit' => $this->Current_Date(),
                            'visit' => ((int) $this->result['visit']) + $this->coefficient
                                ), array(
                            'last_counter' => $this->result['last_counter']
                        ));
                    }
                }
            }
        }
    }

    public function Visitors() {
        // If we're a webcrawler or referral from ourselves or an excluded address don't record the visit.
        if (!$this->exclusion_match) {
            if ($this->db->check_exists_table($this->tbl_visitor)) {
                $this->result = $this->db->get_row($this->tbl_visitor, "*", array(
                    'last_counter' => $this->Current_Date('Y-m-d'),
                    'ip' => $this->ip,
                    'agent' => $this->agent['browser'],
                    'platform' => $this->agent['platform'],
                    'version' => $this->agent['version'],
                ));

                if (!$this->result) {
                    if (Registry::$settings['system']['statistics']['store_ua'] == true) {
                        $ua = $_SERVER['HTTP_USER_AGENT'];
                    } else {
                        $ua = '';
                    }
//                if (function_exists('geoip_record_by_name')) {
//                    $record = geoip_record_by_name($this->ip);
//                    if ($record) {
//                        Debug::preTag($record);
//                    }
//                }
                    $location = "000";
                    if (function_exists('geoip_country_code_by_addr')) {
                        $gi = geoip_open(SYS_PATH . "inc" . DS . "geoip" . DS . "GeoIP.dat", GEOIP_STANDARD);
                        $location = geoip_country_code_by_addr($gi, $this->ip);
                        geoip_close($gi);
                    }

                    $this->db->insert($this->tbl_visitor, array(
                        'last_counter' => $this->Current_date('Y-m-d'),
                        'referred' => $this->get_Referred(),
                        'agent' => $this->agent['browser'],
                        'platform' => $this->agent['platform'],
                        'version' => $this->agent['version'],
                        'ip' => $this->ip,
                        'location' => $location,
                        'UAString' => $ua
                    ));
                }
            }
        }
    }

}
