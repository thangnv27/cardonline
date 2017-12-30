<?php

/**
 * Get current request url
 * @return tring
 */
function getCurrentRquestUrl() {
    $prefix = "http://";
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $prefix = "https://";
    }
    return $prefix . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
}

function getCurlData($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
    $curlData = curl_exec($curl);
    curl_close($curl);
    return $curlData;
}

/**
 * Convert number to words
 * @param Integer $number
 * @return String
 */
function number_to_words($number, $show = false) {
    $hyphen = ' ';
    $conjunction = '  ';
    $separator = ' ';
    $negative = 'negative ';
    $decimal = ' point ';
    $dictionary = array(
        0 => 'không',
        1 => 'một',
        2 => 'hai',
        3 => 'ba',
        4 => 'bốn',
        5 => 'năm',
        6 => 'sáu',
        7 => 'bảy',
        8 => 'tám',
        9 => 'chín',
        10 => 'mười',
        11 => 'mười một',
        12 => 'mười hai',
        13 => 'mười ba',
        14 => 'mười bốn',
        15 => 'mười năm',
        16 => 'mười sáu',
        17 => 'mười bảy',
        18 => 'mười tám',
        19 => 'mười chín',
        20 => 'hai mươi',
        30 => 'ba mươi',
        40 => 'bốn mươi',
        50 => 'năm mươi',
        60 => 'sáu mươi',
        70 => 'bảy mươi',
        80 => 'tám mươi',
        90 => 'chín mươi',
        100 => 'trăm',
        1000 => 'ngàn',
        1000000 => 'triệu',
        1000000000 => 'tỷ',
        1000000000000 => 'nghìn tỷ',
        1000000000000000 => 'ngàn triệu triệu',
        1000000000000000000 => 'tỷ tỷ'
    );
    if (!is_numeric($number)) {
        return false;
    }
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
                'number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING
        );
        return false;
    }
    if ($number < 0) {
        return $negative . number_to_words(abs($number));
    }

    $string = $fraction = null;
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= number_to_words($remainder);
            }
            break;
    }
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    if ($show) {
        echo $string;
    }
    return $string;
}

/**
 * Convert Object to Array
 * @param object $d
 * @return array
 */
function objectToArray($d) {
    if (is_object($d)) {
        // Gets the properties of the given object
        // with get_object_vars function
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        /*
         * Return array converted to object
         * Using __FUNCTION__ (Magic constant)
         * for recursive call
         */
        return array_map(__FUNCTION__, $d);
    } else {
        // Return array
        return $d;
    }
}

/**
 * Converts a bitfield into an array of 1 / 0 values based on the array describing the resulting fields
 *
 * @param	integer	(ref) Bitfield
 * @param	array	Array containing field definitions - array('canx' => 1, 'cany' => 2, 'canz' => 4) etc
 *
 * @return	array
 */
function convert_bits_to_array(&$bitfield, $_FIELDNAMES) {
    $bitfield = intval($bitfield);
    $arry = array();
    foreach ($_FIELDNAMES AS $field => $bitvalue) {
        if ($bitfield & $bitvalue) {
            $arry["$field"] = 1;
        } else {
            $arry["$field"] = 0;
        }
    }
    return $arry;
}
/**
 * 
 * @param string $str
 * @param int $type
 * <p>type=1 ~~> UTF8->ISO</p>
 * <p>Type=2 ~~> ISO->UTF8</p>
 * @return string
 */
function convertCharset($str, $type) {
//--------------------------------------------UTF-8
    $a1 = array('ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'á', 'à', 'ả', 'ã', 'ạ', 'â', 'ă', 'Á', 'À', 'Ả', 'Ã', 'Ạ', 'Â', 'Ă');

    $e1 = array('ế', 'ề', 'ể', 'ễ', 'ệ', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê');

    $i1 = array('í', 'ì', 'ỉ', 'ĩ', 'ị', 'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị');

    $o1 = array('ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'Ố', 'Ồ', 'Ổ', 'Ô', 'Ộ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ơ', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ơ');

    $u1 = array('ứ', 'ừ', 'ử', 'ữ', 'ự', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư');

    $y1 = array('ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ');

    $d1 = array('đ', 'Đ');
//--------------------------------------------ISO
    $a2 = array('&#7845;', '&#7847;', '&#7849;', '&#7851;', '&#7853;', '&#7844;', '&#7846;', '&#7848;', '&#7850;', '&#7852;', '&#7855;', '&#7857;', '&#7859;', '&#7861;', '&#7863;', '&#7854;', '&#7856;', '&#7858;', '&#7860;', '&#7862;', '&aacute;', '&agrave;', '&#7843;', '&atilde;', '&#7841;', '&acirc;', '&#259;', 'Á', '&Agrave;', '&#7842;', '&Atilde;', '&#7840;', 'Â', 'Ă');

    $e2 = array('&#7871;', '&#7873;', '&#7875;', '&#7877;', '&#7879;', '&#7870;', '&#7872;', '&#7874;', '&#7876;', '&#7878;', 'é', '&egrave;', '&#7867;', '&#7869;', '&#7865;', '&ecirc;', 'É', '&Egrave;', '&#7866;', '&#7868;', '&#7864;', '&Ecirc;');

    $i2 = array('&iacute;', '&igrave;', '&#7881;', '&#297;', '&#7883;', 'Í', '&Igrave;', '&#7880;', '&#296;', '&#7882;');

    $o2 = array('&#7889;', '&#7891;', '&#7893;', '&#7895;', '&#7897;', '&#7888;', '&#7890;', '&#7892;', 'Ô', '&#7896;', '&#7899;', '&#7901;', '&#7903;', '&#7905;', '&#7907;', '&#7898;', '&#7900;', '&#7902;', '&#7904;', '&#7906;', '&oacute;', '&ograve;', '&#7887;', '&otilde;', '&#7885;', '&ocirc;', '&#417;', 'Ó', '&Ograve;', '&#7886;', '&Otilde;', '&#7884;', 'Ô', '&#416;');

    $u2 = array('&#7913;', '&#7915;', '&#7917;', '&#7919;', '&#7921;', '&#7912;', '&#7914;', '&#7916;', '&#7918;', '&#7920;', 'ú', '&ugrave;', '&#7911;', '&#361;', '&#7909;', '&#432;', 'Ú', '&Ugrave;', '&#7910;', '&#360;', '&#7908;', '&#431;');

    $y2 = array('ý', '&#7923;', '&#7927;', '&#7929;', '&#7925;', 'Ý', '&#7922;', '&#7926;', '&#7928;', '&#7924;');

    $d2 = array('&#273;', '&#272;');
//--------------------------------------------CONVERT
    if ($type == 1) {
        $str = str_replace($a1, $a2, $str);
        $str = str_replace($e1, $e2, $str);
        $str = str_replace($i1, $i2, $str);
        $str = str_replace($o1, $o2, $str);
        $str = str_replace($u1, $u2, $str);
        $str = str_replace($y1, $y2, $str);
        $str = str_replace($d1, $d2, $str);
    } else {
        $str = str_replace($a2, $a1, $str);
        $str = str_replace($e2, $e1, $str);
        $str = str_replace($i2, $i1, $str);
        $str = str_replace($o2, $o1, $str);
        $str = str_replace($u2, $u1, $str);
        $str = str_replace($y2, $y1, $str);
        $str = str_replace($d2, $d1, $str);
    }
//--------------------------------------------Return
    return $str;
}

/**
 * Transliterates non ASCII chars to ASCII.
 * This is an approximation.
 *
 * Note: Performance and accuracy is gained if the pecl translit extension is available.
 * @see http://pecl.php.net/package/translit
 *
 * @param	string String to transliterate
 * @return	string
 */
function to_ascii($str) {
    if (!$str) {
        return;
    }

    if (function_exists('transliterate')) {
        return transliterate($str, array('normalize_ligature'), 'ISO-8859-1', 'ISO-8859-1');
    }

    static $lookup = array(
        '&Agrave;' => 'A',
        '&Aacute;' => 'A',
        '&Acirc;' => 'A',
        '&Atilde;' => 'A',
        '&Auml;' => 'AE',
        '&Aring;' => 'A',
        '&AElig;' => 'AE',
        '&Ccedil;' => 'C',
        '&Egrave;' => 'E',
        '&Eacute;' => 'E',
        '&Ecirc;' => 'E',
        '&Euml;' => 'E',
        '&Igrave;' => 'I',
        '&Iacute;' => 'I',
        '&Icirc;' => 'I',
        '&Iuml;' => 'I',
        '&ETH;' => 'Dj',
        '&Ntilde;' => 'N',
        '&Ograve;' => 'O',
        '&Oacute;' => 'O',
        '&Ocirc;' => 'O',
        '&Otilde;' => 'O',
        '&Ouml;' => 'OE',
        '&Oslash;' => 'U',
        '&Ugrave;' => 'U',
        '&Uacute;' => 'U',
        '&Ucirc;' => 'U',
        '&Uuml;' => 'UE',
        '&Yacute;' => 'Y',
        '&THORN;' => 'Th',
        '&szlig;' => 'ss',
        '&agrave;' => 'a',
        '&aacute;' => 'a',
        '&acirc;' => 'a',
        '&atilde;' => 'a',
        '&auml;' => 'ae',
        '&aring;' => 'a',
        '&aelig;' => 'ae',
        '&ccedil;' => 'c',
        '&egrave;' => 'e',
        '&eacute;' => 'e',
        '&ecirc;' => 'e',
        '&euml;' => 'e',
        '&igrave;' => 'i',
        '&iacute;' => 'i',
        '&icirc;' => 'i',
        '&iuml;' => 'i',
        '&eth;' => 'dj',
        '&ntilde;' => 'n',
        '&ograve;' => 'o',
        '&oacute;' => 'o',
        '&ocirc;' => 'o',
        '&otilde;' => 'o',
        '&ouml;' => 'oe',
        '&oslash;' => 'o',
        '&ugrave;' => 'u',
        '&uacute;' => 'u',
        '&ucirc;' => 'u',
        '&uuml;' => 'ue',
        '&yacute;' => 'y',
        '&thorn;' => 'th',
        '&yuml;' => 'y'
    );

    $str = htmlentities($str);
    $str = str_replace(array_keys($lookup), array_values($lookup), $str);
    $str = html_entity_decode($str);
    $str = preg_replace('#[^a-z0-9]+#i', '-', $str);

    return $str;
}

/**
 * Converts a string to utf8
 *
 * @param	string	The variable to clean
 * @param	string	The source charset
 * @param	bool	Whether to strip invalid utf8 if we couldn't convert
 * @return	string	The reencoded string
 */
function to_utf8($in, $charset = false, $strip = true) {
    if ('' === $in OR false === $in OR is_null($in)) {
        return $in;
    }

    // Fallback to UTF-8
    if (!$charset) {
        $charset = 'UTF-8';
    }

    // Try iconv
    if (function_exists('iconv')) {
        $out = @iconv($charset, 'UTF-8//IGNORE', $in);
        return $out;
    }

    // Try mbstring
    if (function_exists('mb_convert_encoding')) {
        return @mb_convert_encoding($in, 'UTF-8', $charset);
    }

    if (!$strip) {
        return $in;
    }

    // Strip non valid UTF-8
    // TODO: Do we really want to do this?
    $utf8 = '#([\x09\x0A\x0D\x20-\x7E]' . # ASCII
            '|[\xC2-\xDF][\x80-\xBF]' . # non-overlong 2-byte
            '|\xE0[\xA0-\xBF][\x80-\xBF]' . # excluding overlongs
            '|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}' . # straight 3-byte
            '|\xED[\x80-\x9F][\x80-\xBF]' . # excluding surrogates
            '|\xF0[\x90-\xBF][\x80-\xBF]{2}' . # planes 1-3
            '|[\xF1-\xF3][\x80-\xBF]{3}' . # planes 4-15
            '|\xF4[\x80-\x8F][\x80-\xBF]{2})#S'; # plane 16

    $out = '';
    $matches = array();
    while (preg_match($utf8, $in, $matches)) {
        $out .= $matches[0];
        $in = substr($in, strlen($matches[0]));
    }

    return $out;
}

/**
 * Gets the Unicode Ordinal for a UTF-8 character.
 *
 * @param	string	Character to convert
 * @return	int		Ordinal value or false if invalid
 */
function ord_uni($chr) {
    // Valid lengths and first byte ranges
    static $check_len = array(
        1 => array(0, 127),
        2 => array(192, 223),
        3 => array(224, 239),
        4 => array(240, 247),
        5 => array(248, 251),
        6 => array(252, 253)
    );

    // Get length
    $blen = strlen($chr);

    // Get single byte ordinals
    $b = array();
    for ($i = 0; $i < $blen; $i++) {
        $b[$i] = ord($chr[$i]);
    }

    // Check expected length
    foreach ($check_len AS $len => $range) {
        if (($b[0] >= $range[0]) AND ($b[0] <= $range[1])) {
            $elen = $len;
        }
    }

    // If no range found, or chr is too short then it's invalid
    if (!isset($elen) OR ($blen < $elen)) {
        return false;
    }

    // Normalise based on octet-sequence length
    switch ($elen) {
        case (1):
            return $b[0];
        case (2):
            return ($b[0] - 192) * 64 + ($b[1] - 128);
        case (3):
            return ($b[0] - 224) * 4096 + ($b[1] - 128) * 64 + ($b[2] - 128);
        case (4):
            return ($b[0] - 240) * 262144 + ($b[1] - 128) * 4096 + ($b[2] - 128) * 64 + ($b[3] - 128);
        case (5):
            return ($b[0] - 248) * 16777216 + ($b[1] - 128) * 262144 + ($b[2] - 128) * 4096 + ($b[3] - 128) * 64 + ($b[4] - 128);
        case (6):
            return ($b[0] - 252) * 1073741824 + ($b[1] - 128) * 16777216 + ($b[2] - 128) * 262144 + ($b[3] - 128) * 4096 + ($b[4] - 128) * 64 + ($b[5] - 128);
    }
}

/**
 * Strips NCRs from a string.
 *
 * @param	string	The string to strip from
 * @return	string	The result
 */
function stripncrs($str) {
    return preg_replace('/(&#[0-9]+;)/', '', $str);
}

/**
 * 
 * @param string $domain
 * @return bool
 */
function is_valid_domain($domain) {
    // Strip out any http or https or www
    $search = array('http://', 'https://', 'www.');
    $replace = array('', '', '');
    $domain = str_replace($search, $replace, $domain);
    /* if (filter_var(gethostbyname($domain), FILTER_VALIDATE_IP)) {
      return TRUE;
      } */
    $regex = "/^([a-zA-Z0-9][a-zA-Z0-9\-]{1,63})([a-zA-Z0-9\-\.]{1,63})?\.[a-zA-Z\.]{2,6}$/i";
    return preg_match($regex, $domain, $matches);
}

function remove_special_char($text) {
    return preg_replace("#(~|`|!|\#|\\$|%|\^|&|\*|=|-|_|\+|\[|\]|{|}|\\\|\||'|:|;|<|>|\?|/)#", "", $text);
}
