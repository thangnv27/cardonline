<?php

class Request {

    function __construct() {
        
    }

    /**
     * Get all http request
     * 
     * @return array
     */
    public function all() {
        if (isset($_REQUEST))
            return $_REQUEST;
        else
            return array();
    }

    /**
     * Get value from request by index key
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        if (is_string($key) and isset($_REQUEST[$key])) {
            if (is_array($_REQUEST[$key])) {
                return $_REQUEST[$key];
            }
            return trim($_REQUEST[$key]);
        }
        return null;
    }

    /**
     * Get http request method <br />
     * Method: POST, GET
     * 
     * @return string
     */
    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 
     * @return \Session
     */
    public function getSession() {
        return new Session();
    }

    /**
     * Get current request url
     * @return tring
     */
    public function getCurrentRquestUrl() {
        $prefix = "http://";
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $prefix = "https://";
        }
        return $prefix . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    }

    /**
     * Browser detection system - returns whether or not the visiting browser is the one specified
     *
     * @param	string	Browser name (opera, ie, mozilla, firebord, firefox... etc. - see $is array)
     * @param	float	Minimum acceptable version for true result (optional)
     *
     * @return	boolean
     */
    public function is_browser($browser, $version = 0) {
        static $is;
        if (!is_array($is)) {
            $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
            $is = array(
                'opera' => 0,
                'ie' => 0,
                'mozilla' => 0,
                'firebird' => 0,
                'firefox' => 0,
                'camino' => 0,
                'konqueror' => 0,
                'safari' => 0,
                'webkit' => 0,
                'webtv' => 0,
                'netscape' => 0,
                'mac' => 0
            );

            // detect opera
            # Opera/7.11 (Windows NT 5.1; U) [en]
            # Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.0) Opera 7.02 Bork-edition [en]
            # Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 4.0) Opera 7.0 [en]
            # Mozilla/4.0 (compatible; MSIE 5.0; Windows 2000) Opera 6.0 [en]
            # Mozilla/4.0 (compatible; MSIE 5.0; Mac_PowerPC) Opera 5.0 [en]
            if (strpos($useragent, 'opera') !== false) {
                preg_match('#opera(/| )([0-9\.]+)#', $useragent, $regs);
                $is['opera'] = $regs[2];
            }

            // detect internet explorer
            # Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Q312461)
            # Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.0.3705)
            # Mozilla/4.0 (compatible; MSIE 5.22; Mac_PowerPC)
            # Mozilla/4.0 (compatible; MSIE 5.0; Mac_PowerPC; e504460WanadooNL)
            if (strpos($useragent, 'msie ') !== false AND !$is['opera']) {
                preg_match('#msie ([0-9\.]+)#', $useragent, $regs);
                $is['ie'] = $regs[1];
            }

            // detect macintosh
            if (strpos($useragent, 'mac') !== false) {
                $is['mac'] = 1;
            }

            // detect safari
            # Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-us) AppleWebKit/74 (KHTML, like Gecko) Safari/74
            # Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/51 (like Gecko) Safari/51
            # Mozilla/5.0 (Windows; U; Windows NT 6.0; en) AppleWebKit/522.11.3 (KHTML, like Gecko) Version/3.0 Safari/522.11.3
            # Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1C28 Safari/419.3
            # Mozilla/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/3A100a Safari/419.3
            if (strpos($useragent, 'applewebkit') !== false) {
                preg_match('#applewebkit/([0-9\.]+)#', $useragent, $regs);
                $is['webkit'] = $regs[1];

                if (strpos($useragent, 'safari') !== false) {
                    preg_match('#safari/([0-9\.]+)#', $useragent, $regs);
                    $is['safari'] = $regs[1];
                }
            }

            // detect konqueror
            # Mozilla/5.0 (compatible; Konqueror/3.1; Linux; X11; i686)
            # Mozilla/5.0 (compatible; Konqueror/3.1; Linux 2.4.19-32mdkenterprise; X11; i686; ar, en_US)
            # Mozilla/5.0 (compatible; Konqueror/2.1.1; X11)
            if (strpos($useragent, 'konqueror') !== false) {
                preg_match('#konqueror/([0-9\.-]+)#', $useragent, $regs);
                $is['konqueror'] = $regs[1];
            }

            // detect mozilla
            # Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.4b) Gecko/20030504 Mozilla
            # Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.2a) Gecko/20020910
            # Mozilla/5.0 (X11; U; Linux 2.4.3-20mdk i586; en-US; rv:0.9.1) Gecko/20010611
            if (strpos($useragent, 'gecko') !== false AND !$is['safari'] AND !$is['konqueror']) {
                // See bug #26926, this is for Gecko based products without a build
                $is['mozilla'] = 20090105;
                if (preg_match('#gecko/(\d+)#', $useragent, $regs)) {
                    $is['mozilla'] = $regs[1];
                }

                // detect firebird / firefox
                # Mozilla/5.0 (Windows; U; WinNT4.0; en-US; rv:1.3a) Gecko/20021207 Phoenix/0.5
                # Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.4b) Gecko/20030516 Mozilla Firebird/0.6
                # Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.4a) Gecko/20030423 Firebird Browser/0.6
                # Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.6) Gecko/20040206 Firefox/0.8
                if (strpos($useragent, 'firefox') !== false OR strpos($useragent, 'firebird') !== false OR strpos($useragent, 'phoenix') !== false) {
                    preg_match('#(phoenix|firebird|firefox)( browser)?/([0-9\.]+)#', $useragent, $regs);
                    $is['firebird'] = $regs[3];

                    if ($regs[1] == 'firefox') {
                        $is['firefox'] = $regs[3];
                    }
                }

                // detect camino
                # Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-US; rv:1.0.1) Gecko/20021104 Chimera/0.6
                if (strpos($useragent, 'chimera') !== false OR strpos($useragent, 'camino') !== false) {
                    preg_match('#(chimera|camino)/([0-9\.]+)#', $useragent, $regs);
                    $is['camino'] = $regs[2];
                }
            }

            // detect web tv
            if (strpos($useragent, 'webtv') !== false) {
                preg_match('#webtv/([0-9\.]+)#', $useragent, $regs);
                $is['webtv'] = $regs[1];
            }

            // detect pre-gecko netscape
            if (preg_match('#mozilla/([1-4]{1})\.([0-9]{2}|[1-8]{1})#', $useragent, $regs)) {
                $is['netscape'] = "$regs[1].$regs[2]";
            }
        }

        // sanitize the incoming browser name
        $browser = strtolower($browser);
        if (substr($browser, 0, 3) == 'is_') {
            $browser = substr($browser, 3);
        }

        // return the version number of the detected browser if it is the same as $browser
        if ($is["$browser"]) {
            // $version was specified - only return version number if detected version is >= to specified $version
            if ($version) {
                if ($is["$browser"] >= $version) {
                    return $is["$browser"];
                }
            } else {
                return $is["$browser"];
            }
        }

        // if we got this far, we are not the specified browser, or the version number is too low
        return 0;
    }

    /**
     * Check webserver's make and model
     *
     * @param	string	Browser name (apache, iis, samber, nginx... etc. - see $is array)
     * @param	float	Minimum acceptable version for true result (optional)
     *
     * @return	boolean
     */
    public function is_server($server_name, $version = 0) {
        static $server;

        // Resolve server
        if (!is_array($server)) {
            $server_name = preg_quote(strtolower($server_name), '#');
            $server = strtolower($_SERVER['SERVER_SOFTWARE']);
            $matches = array();

            if (preg_match("#(.*)(?:/| )([0-9\.]*)#i", $server, $matches)) {
                $server = array('name' => $matches[1]);
                $server['version'] = (isset($matches[2]) AND $matches[2]) ? $matches[2] : true;
            }
        }

        if (strpos($server['name'], $server_name)) {
            if (!$version OR (true === $server['version']) OR ($server['version'] >= $version)) {
                return true;
            }
        }

        return false;
    }

}
