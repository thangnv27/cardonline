<?php

class Statistics {

    protected $db;
    private $ip;
    private $result;
    private $agent;
    public $coefficient = 1;

    public function __construct() {
        $this->db = new Database();
        $this->agent = $this->get_UserAgent();
        if (Registry::$settings['system']['statistics']['coefficient']) {
            $this->coefficient = Registry::$settings['system']['statistics']['coefficient'];
        }
    }

    public function Primary_Values() {
        $this->result = $this->db->select(TABLE_PREFIX . "statistics_useronline");
        if (empty($this->result)) {
            $this->db->insert(TABLE_PREFIX . "statistics_useronline", array(
                'ip' => $this->get_IP(),
                'timestamp' => date('U'),
                'date' => $this->Current_Date(),
                'referred' => $this->get_Referred(),
                'agent' => $this->agent['browser'],
                'platform' => $this->agent['platform'],
                'version' => $this->agent['version']
            ));
        }

        $this->result = $this->db->select(TABLE_PREFIX . "statistics_visit");
        if (empty($this->result)) {
            $this->db->insert(TABLE_PREFIX . "statistics_visit", array(
                'last_visit' => $this->Current_Date(),
                'last_counter' => $this->Current_date('Y-m-d'),
                'visit' => 1
            ));
        }

        $this->result = $this->db->select(TABLE_PREFIX . "statistics_visitor");
        if (empty($this->result)) {
            $location = "000";
            if (function_exists('geoip_country_code_by_addr')) {
                $gi = geoip_open(SYS_PATH . "inc" . DS . "geoip" . DS . "GeoIP.dat", GEOIP_STANDARD);
                $location = geoip_country_code_by_addr($gi, $this->ip);
                geoip_close($gi);
            }
            $this->db->insert(TABLE_PREFIX . "statistics_visitor", array(
                'last_counter' => $this->Current_date('Y-m-d'),
                'referred' => $this->get_Referred(),
                'agent' => $this->agent['browser'],
                'platform' => $this->agent['platform'],
                'version' => $this->agent['version'],
                'ip' => $this->get_IP(),
                'location' => $location
            ));
        }
    }

    public function get_IP() {
        if (getenv('HTTP_CLIENT_IP')) {
            $this->ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $this->ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $this->ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $this->ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $this->ip = getenv('HTTP_FORWARDED');
        } else {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }

        return $this->ip;
    }

    public function get_UserAgent() {
        $agent = parse_user_agent();

        if ($agent['browser'] == null) {
            $agent['browser'] = "Unknown";
        }
        if ($agent['platform'] == null) {
            $agent['platform'] = "Unknown";
        }
        if ($agent['version'] == null) {
            $agent['version'] = "Unknown";
        }

        return $agent;
    }

    public function get_Referred($default_referr = false) {
        $referr = '';
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referr = $_SERVER['HTTP_REFERER'];
        }
        if ($default_referr) {
            $referr = $default_referr;
        }

        $referr = strip_tags($referr);
        if (!$referr) {
            $referr = Registry::$siteurl;
        }

        return $referr;
    }

    public function Current_Date($format = 'Y-m-d H:i:s', $strtotime = null) {
        if ($strtotime) {
            return date($format, strtotime("{$strtotime} day"));
        } else {
            return date($format);
        }
    }

    public function platform_is_bot($platform) {
        if (strpos($platform, "compatible;") !== FALSE) {
            return TRUE;
        } else if (strpos($platform, "bot.htm") !== FALSE) {
            return TRUE;
        }
        return FALSE;
    }

    public function searchengine_list() {
        // This function returns an array or array's which define what search engines we should look for.
        //
        // By default will only return ones that have not been disabled by the user, this can be overridden by the $all parameter.
        //
        // Each sub array is made up of the following items:
        //		name 		 = The proper name of the search engine
        //		tag 		 = a short one word, all lower case, representation of the search engine
        //		sqlpattern   = either a single SQL style search pattern OR an array or search patterns to match the hostname in a URL against
        //		regexpattern = either a single regex style search pattern OR an array or search patterns to match the hostname in a URL against
        //		querykey 	 = the URL key that contains the search string for the search engine
        //		image		 = the name of the image file to associate with this search engine (just the filename, no path info)
        //
        $engines = array(
            'baidu' => array('name' => 'Baidu', 'tag' => 'baidu', 'sqlpattern' => '%baidu.com%', 'regexpattern' => 'baidu\.com', 'querykey' => 'wd', 'image' => 'baidu.png'),
            'bing' => array('name' => 'Bing', 'tag' => 'bing', 'sqlpattern' => '%bing.com%', 'regexpattern' => 'bing\.com', 'querykey' => 'q', 'image' => 'bing.png'),
            'duckduckgo' => array('name' => 'DuckDuckGo', 'tag' => 'duckduckgo', 'sqlpattern' => array('%duckduckgo.com%', '%ddg.gg%'), 'regexpattern' => array('duckduckgo\.com', 'ddg\.gg'), 'querykey' => 'q', 'image' => 'duckduckgo.png'),
            'google' => array('name' => 'Google', 'tag' => 'google', 'sqlpattern' => '%google.%', 'regexpattern' => 'google\.', 'querykey' => 'q', 'image' => 'google.png'),
            'yahoo' => array('name' => 'Yahoo!', 'tag' => 'yahoo', 'sqlpattern' => '%yahoo.com%', 'regexpattern' => 'yahoo\.com', 'querykey' => 'p', 'image' => 'yahoo.png'),
            'yandex' => array('name' => 'Yandex', 'tag' => 'yandex', 'sqlpattern' => '%yandex.ru%', 'regexpattern' => 'yandex\.ru', 'querykey' => 'text', 'image' => 'yandex.png')
        );

        return $engines;
    }

    public function searchword_query($search_engine = 'all') {
        $searchengine_list = $this->searchengine_list();
        $search_query = '';

        if (strtolower($search_engine) == 'all') {
            foreach ($searchengine_list as $se) {
                if (is_array($se['sqlpattern'])) {
                    foreach ($se['sqlpattern'] as $subse) {
                        $search_query .= "(`referred` LIKE '{$subse}{$se['querykey']}=%' AND `referred` NOT LIKE '{$subse}{$se['querykey']}=&%' AND `referred` NOT LIKE '{$subse}{$se['querykey']}=') OR ";
                    }
                } else {
                    $search_query .= "(`referred` LIKE '{$se['sqlpattern']}{$se['querykey']}=%' AND `referred` NOT LIKE '{$se['sqlpattern']}{$se['querykey']}=&%' AND `referred` NOT LIKE '{$se['sqlpattern']}{$se['querykey']}=')  OR ";
                }
            }

            // Trim off the last ' OR ' for the loop above.
            $search_query = substr($search_query, 0, strlen($search_query) - 4);
        } else {
            if (is_array($searchengine_list[$search_engine]['sqlpattern'])) {
                foreach ($searchengine_list[$search_engine]['sqlpattern'] as $se) {
                    $search_query .= "(`referred` LIKE '{$se}{$searchengine_list[$search_engine]['querykey']}=%' AND `referred` NOT LIKE '{$se}{$searchengine_list[$search_engine]['querykey']}=&%' AND `referred` NOT LIKE '{$se}{$searchengine_list[$search_engine]['querykey']}=') OR ";
                }

                // Trim off the last ' OR ' for the loop above.
                $search_query = substr($search_query, 0, strlen($search_query) - 4);
            } else {
                $search_query .= "(`referred` LIKE '{$searchengine_list[$search_engine]['sqlpattern']}{$searchengine_list[$search_engine]['querykey']}=%' AND `referred` NOT LIKE '{$searchengine_list[$search_engine]['sqlpattern']}{$searchengine_list[$search_engine]['querykey']}=&%' AND `referred` NOT LIKE '{$searchengine_list[$search_engine]['sqlpattern']}{$searchengine_list[$search_engine]['querykey']}=')";
            }
        }

        return $search_query;
    }

    public function searchengine_query($search_engine = 'all') {
        $searchengine_list = $this->searchengine_list();
        $search_query = '';

        if (strtolower($search_engine) == 'all') {
            foreach ($searchengine_list as $se) {
                if (is_array($se['sqlpattern'])) {
                    foreach ($se['sqlpattern'] as $subse) {
                        $search_query .= "`referred` LIKE '{$subse}' OR ";
                    }
                } else {
                    $search_query .= "`referred` LIKE '{$se['sqlpattern']}' OR ";
                }
            }

            // Trim off the last ' OR ' for the loop above.
            $search_query = substr($search_query, 0, strlen($search_query) - 4);
        } else {
            if (is_array($searchengine_list[$search_engine]['sqlpattern'])) {
                foreach ($searchengine_list[$search_engine]['sqlpattern'] as $se) {
                    $search_query .= "`referred` LIKE '{$se}' OR ";
                }

                // Trim off the last ' OR ' for the loop above.
                $search_query = substr($search_query, 0, strlen($search_query) - 4);
            } else {
                $search_query .= "`referred` LIKE '{$searchengine_list[$search_engine]['sqlpattern']}'";
            }
        }

        return $search_query;
    }

    public function searchengine_regex($search_engine = 'all') {
        $searchengine_list = $this->searchengine_list();
        $search_query = '';

        if (strtolower($search_engine) == 'all') {
            foreach ($searchengine_list as $se) {
                if (is_array($se['regexpattern'])) {
                    foreach ($se['regexpattern'] as $subse) {
                        $search_query .= "{$subse}|";
                    }
                } else {
                    $search_query .= "{$se['regexpattern']}|";
                }
            }

            // Trim off the last '|' for the loop above.
            $search_query = substr($search_query, 0, strlen($search_query) - 1);
        } else {
            if (is_array($searchengine_list[$search_engine]['regexpattern'])) {
                foreach ($searchengine_list[$search_engine]['regexpattern'] as $se) {
                    $search_query .= "{$se}|";
                }

                // Trim off the last '|' for the loop above.
                $search_query = substr($search_query, 0, strlen($search_query) - 1);
            } else {
                $search_query .= $searchengine_list[$search_engine]['regexpattern'];
            }
        }

        // Add the brackets and return
        return "({$search_query})";
    }

    public function Check_Search_Engines($search_engine_name, $search_engine = null) {
        if (strstr($search_engine, $search_engine_name)) {
            return 1;
        }
    }

    public function Search_Engine_Info($url = false) {
        if (!$url) {
            $url = isset($_SERVER['HTTP_REFERER']) ? $this->get_Referred() : false;
        }
        if ($url == false) {
            return false;
        }

        $parts = parse_url($url);
        $search_engines = $this->searchengine_list();

        foreach ($search_engines as $key => $value) {
            $search_regex = $this->searchengine_regex($key);
            preg_match('/' . $search_regex . '/', $parts['host'], $matches);

            if (isset($matches[1])) {
                return $value;
            }
        }

        return array('name' => 'Unknown', 'tag' => '', 'sqlpattern' => '', 'regexpattern' => '', 'querykey' => 'q', 'image' => 'unknown.png');
    }

    public function Search_Engine_QueryString($url = false) {
        if (!$url) {
            $url = isset($_SERVER['HTTP_REFERER']) ? $this->get_Referred() : false;
        }
        if ($url == false) {
            return false;
        }

        $parts = parse_url($url);

        if (array_key_exists('query', $parts)) {
            parse_str($parts['query'], $query);
        } else {
            $query = array();
        }

        $search_engines = $this->searchengine_list();

        foreach ($search_engines as $key => $value) {
            $search_regex = $this->searchengine_regex($key);

            preg_match('/' . $search_regex . '/', $parts['host'], $matches);

            if (isset($matches[1])) {
                if (array_key_exists($search_engines[$key]['querykey'], $query)) {
                    $words = strip_tags($query[$search_engines[$key]['querykey']]);
                } else {
                    $words = '';
                }
                if ($words == '') {
                    $words = 'No search query found!';
                }
                return $words;
            }
        }

        return '';
    }

    public function check_adwords($url = false) {
        $result = FALSE;
        if (!$url) {
            $url = isset($_SERVER['HTTP_REFERER']) ? $this->get_Referred() : false;
        }
        if ($url == false) {
            return false;
        }

        $parts = parse_url($url);

        if (array_key_exists('query', $parts)) {
            parse_str($parts['query'], $query);
        } else {
            $query = array();
        }

        $search_engines = $this->searchengine_list();

        foreach ($search_engines as $key => $value) {
            $search_regex = $this->searchengine_regex($key);

            preg_match('/' . $search_regex . '/', $parts['host'], $matches);

            if (isset($matches[1])) {
                if (array_key_exists('adurl', $query)) {
                    $result = TRUE;
                }
                return $result;
            }
        }

        return $result;
    }

    public function get_gmap_coordinate($country, $coordinate) {
        global $CountryCoordinates;

        if (Registry::$settings['system']['statistics']['google_coordinates']) {
            $api_url = "http://maps.google.com/maps/api/geocode/json?address={$country}&sensor=false";

            if (function_exists('file_get_contents')) {

                $json = file_get_contents($api_url);
                $response = json_decode($json);

                if ($response->status != 'OK')
                    return false;
            } elseif (function_exists('curl_version')) {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = json_decode(curl_exec($ch));

                if ($response->status != 'OK')
                    return false;
            } else {
                $response = false;
            }

            $result = $response->results[0]->geometry->location->{$coordinate};
        } else {
            $result = $CountryCoordinates[$country][$coordinate];
        }

        if ($result == '') {
            $result = '0';
        }

        return $result;
    }

}
