<?php

class StatisticsAdminModel extends Model {

    function __construct() {
        parent::__construct();
    }

    function useronline($where = array()) {
        $result = $this->DB()->select(TABLE_PREFIX . "statistics_useronline", "COUNT(id) AS total", $where);
        return $result[0]['total'];
    }

    function getUseronline($start = 1, $limit = 10, $where = array()) {
        try {
            $db = $this->DB();
            $sql = "SELECT * FROM " . TABLE_PREFIX . "statistics_useronline";
            if (!empty($where)) {
                $sql .= " WHERE " . $db->where($where);
            }
            $sql .= " ORDER BY date DESC LIMIT $start, $limit";
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

    function visit($time, $daily = null) {
        $s = new Statistics();

        if ($daily == true) {
            $result = $this->DB()->get_row(TABLE_PREFIX . "statistics_visit", "visit", "last_counter = '{$s->Current_Date('Y-m-d', $time)}'");
            if ($result) {
                return $result['visit'];
            } else {
                return 0;
            }
        } else {
            switch ($time) {
                case 'today':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visit", "SUM(visit) as visit", "last_counter = '{$s->Current_Date('Y-m-d')}'");
                    break;
                case 'yesterday':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visit", "SUM(visit) as visit", "last_counter = '{$s->Current_Date('Y-m-d', -1)}'");
                    break;
                case 'week':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visit", "SUM(visit) as visit", "last_counter BETWEEN '{$s->Current_Date('Y-m-d', -7)}' AND '{$s->Current_Date('Y-m-d')}'");
                    break;
                case 'month':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visit", "SUM(visit) as visit", "last_counter BETWEEN '{$s->Current_Date('Y-m-d', -30)}' AND '{$s->Current_Date('Y-m-d')}'");
                    break;
                case 'year':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visit", "SUM(visit) as visit", "last_counter BETWEEN '{$s->Current_Date('Y-m-d', -365)}' AND '{$s->Current_Date('Y-m-d')}'");
                    break;
                case 'total':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visit", "SUM(visit) as visit");
                    break;
                default:
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visit", "SUM(visit) as visit", "last_counter BETWEEN '{$s->Current_Date('Y-m-d', $time)}' AND '{$s->Current_Date('Y-m-d')}'");
                    break;
            }
        }

        if (!$result[0]['visit']) {
            return 0;
        }
        return $result[0]['visit'];
    }

    function visits($time, $time2 = null) {
        if ($time2) {
            $result = $this->DB()->select(TABLE_PREFIX . "statistics_visit", "SUM(visit) as visit", "last_counter BETWEEN '{$time}' AND '{$time2}'");
        } else {
            $result = $this->DB()->select(TABLE_PREFIX . "statistics_visit", "SUM(visit) as visit", "last_counter = '{$time}'");
        }

        if (!$result[0]['visit']) {
            return 0;
        }
        return $result[0]['visit'];
    }

    function visitor($time, $daily = null, $countonly = false) {
        $s = new Statistics();

        $select = '*';
        $sqlstatement = '';

        if ($countonly == true) {
            $select = 'COUNT(last_counter) visitor';
        }

        if ($daily == true) {
            $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", $select, "last_counter = '{$s->Current_Date('Y-m-d', $time)}'");
        } else {
            switch ($time) {
                case 'today':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", $select, "last_counter = '{$s->Current_Date('Y-m-d')}'");
                    break;
                case 'yesterday':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", $select, "last_counter = '{$s->Current_Date('Y-m-d', -1)}'");
                    break;
                case 'week':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", $select, "last_counter BETWEEN '{$s->Current_Date('Y-m-d', -7)}' AND '{$s->Current_Date('Y-m-d')}'");
                    break;
                case 'month':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", $select, "last_counter BETWEEN '{$s->Current_Date('Y-m-d', -30)}' AND '{$s->Current_Date('Y-m-d')}'");
                    break;
                case 'year':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", $select, "last_counter BETWEEN '{$s->Current_Date('Y-m-d', -365)}' AND '{$s->Current_Date('Y-m-d')}'");
                    break;
                case 'total':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", $select);
                    break;
                default:
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", $select, "last_counter BETWEEN '{$s->Current_Date('Y-m-d', $time)}' AND '{$s->Current_Date('Y-m-d')}'");
                    break;
            }
        }

        if ($countonly) {
            return $result[0]['visitor'];
        }
        return $result;
    }

    function visitors($time, $time2 = null, $countonly = false) {
        $select = '*';
        $sqlstatement = '';

        if ($countonly == true) {
            $select = 'COUNT(last_counter) visitor';
        }

        if ($time2) {
            $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", $select, "last_counter BETWEEN '{$time}' AND '{$time2}'");
        } else {
            $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", $select, "last_counter = '{$time}'");
        }

        if ($countonly) {
            return $result[0]['visitor'];
        }
        return $result;
    }

    function countRecentVisitors($where = array()) {
        $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "COUNT(id) AS total", $where);
        return $result[0]['total'];
    }

    function recentVisitors($start = 1, $limit = 10, $where = array()) {
        try {
            $db = $this->DB();
            $sql = "SELECT * FROM " . TABLE_PREFIX . "statistics_visitor";
            if (!empty($where)) {
                $sql .= " WHERE " . $db->where($where);
            }
            $sql .= " ORDER BY last_counter DESC LIMIT $start, $limit";
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

    /**
     * Get browsers list
     * @return array
     */
    function agent_list() {
        $Browsers = array();
        $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "DISTINCT agent");

        foreach ($result as $out) {
            $Browsers[] = $out['agent'];
        }

        return $Browsers;
    }

    function countAgent($agent) {
        $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "COUNT(agent) AS agent", array(
            'agent' => $agent,
        ));

        return $result[0]['agent'];
    }

    function platform_list() {
        $Platforms = array();
        $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "DISTINCT platform");

        foreach ($result as $out) {
            $Platforms[] = $out['platform'];
        }

        return $Platforms;
    }

    function countPlatform($platform) {
        $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "COUNT(platform) AS platform", array(
            'platform' => $platform,
        ));

        return $result[0]['platform'];
    }

    function agent_version_list($agent) {
        $Versions = array();
        $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "DISTINCT version", array(
            'agent' => $agent,
        ));

        foreach ($result as $out) {
            $Versions[] = $out['version'];
        }

        return $Versions;
    }

    function count_agent_version($agent, $version) {
        $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "COUNT(version) AS version", array(
            'agent' => $agent,
            'version' => $version,
        ));

        return $result[0]['version'];
    }

    function searchengine($search_engine = 'all', $time = 'total', $daily = false) {
        $s = new Statistics();
        $search_query = $s->searchengine_query($search_engine);

        if ($daily) {
            $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d', $time)}'");
        } else {
            switch ($time) {
                case 'today':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d')}' AND {$search_query}");
                    break;
                case 'yesterday':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d', -1)}' AND {$search_query}");
                    break;
                case 'week':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d', -7)}' AND {$search_query}");
                    break;
                case 'month':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d', -30)}' AND {$search_query}");
                    break;
                case 'year':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d', -365)}' AND {$search_query}");
                    break;
                case 'total':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", $search_query);
                    break;
                default:
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d', $time)}' AND {$search_query}");
                    break;
            }
        }

        return $result;
    }

    function searchengine_visitors($time, $search_engine = 'all') {
        $s = new Statistics();
        $search_query = $s->searchengine_query($search_engine);
        $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$time}' AND {$search_query}");

        return $result;
    }

    function searchword($search_engine = 'all', $time = 'total', $daily = false) {
        $s = new Statistics();
        $search_query = $s->searchword_query($search_engine);
        if ($daily) {
            $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d', $time)}'");
        } else {
            switch ($time) {
                case 'today':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d')}' AND {$search_query}");
                    break;
                case 'yesterday':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d', -1)}' AND {$search_query}");
                    break;
                case 'week':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d', -7)}' AND {$search_query}");
                    break;
                case 'month':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d', -30)}' AND {$search_query}");
                    break;
                case 'year':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d', -365)}' AND {$search_query}");
                    break;
                case 'total':
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", $search_query);
                    break;
                default:
                    $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "*", "last_counter = '{$s->Current_Date('Y-m-d', $time)}' AND {$search_query}");
                    break;
            }
        }

        return $result;
    }

    function countSearchWords($where = array()) {
        $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "COUNT(id) AS total", $where);
        return $result[0]['total'];
    }

    function searchWords($start = 1, $limit = 10, $where = array()) {
        try {
            $db = $this->DB();
            $sql = "SELECT * FROM " . TABLE_PREFIX . "statistics_visitor";
            if (!empty($where)) {
                $sql .= " WHERE " . $db->where($where);
            }
            $sql .= " ORDER BY last_counter DESC LIMIT $start, $limit";
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

    function countReferrers($where = array()) {
        $result = $this->DB()->select(TABLE_PREFIX . "statistics_visitor", "COUNT(id) AS total", $where);
        return $result[0]['total'];
    }

    function getReferrers($start = 1, $limit = 10, $where = array()) {
        try {
            $db = $this->DB();
            $sql = "SELECT * FROM " . TABLE_PREFIX . "statistics_visitor";
            if (!empty($where)) {
                $sql .= " WHERE " . $db->where($where);
            }
            $sql .= " ORDER BY last_counter DESC LIMIT $start, $limit";
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

    function getAllReferrers($where = array()) {
        try {
            $db = $this->DB();
            $sql = "SELECT * FROM " . TABLE_PREFIX . "statistics_visitor";
            if (!empty($where)) {
                $sql .= " WHERE " . $db->where($where);
            }
            $sql .= " ORDER BY last_counter DESC";
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

    function countLocations($where = array()) {
        try {
            $db = $this->DB();
            $sql = "SELECT DISTINCT location, count(location) AS visit FROM " . TABLE_PREFIX . "statistics_visitor";
            if (!empty($where)) {
                $sql .= " WHERE " . $db->where($where);
            }
            $sql .= " GROUP BY location";
            $stm = $db->prepare($sql);
            $stm->execute();
            $result = $stm->fetchAll(PDO::FETCH_ASSOC);
            return count($result);
        } catch (Exception $exc) {
            if (DEBUG == TRUE) {
                Debug::throwException("Database error!", $exc);
            }
            return 0;
        }
    }

    function getLocations($start, $limit, $where = array()) {
        try {
            $db = $this->DB();
            $sql = "SELECT DISTINCT location, count(location) AS visit FROM " . TABLE_PREFIX . "statistics_visitor";
            if (!empty($where)) {
                $sql .= " WHERE " . $db->where($where);
            }
            $sql .= " GROUP BY location ORDER BY visit DESC LIMIT $start, $limit";
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

}
