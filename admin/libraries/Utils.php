<?php

class Utils {

    /**
     * Indent white space
     * @param $length Number space
     * @return String with white space
     */
    public static function indentSpace($length = 4) {
        $space = "";
        for ($i = 0; $i < $length; $i++) {
            $space .= "&nbsp;";
        }
        return $space;
    }

    /**
     * Indent white space
     * @param $length Number space
     * @return String with white space
     */
    public static function indentDash($length = 2) {
        $space = "";
        for ($i = 0; $i < $length; $i++) {
            $space .= "-";
        }
        return $space;
    }

    /**
     * Cities in Vietnamese
     * @return array
     */
    public static function vn_city_list() {
        return array(
            "An Giang", "Bà Rịa - Vũng Tàu", "Bạc Liêu", "Bắc Kạn", "Bắc Giang", "Bắc Ninh", "Bến Tre", "Bình Dương",
            "Bình Định", "Bình Phước", "Bình Thuận", "Cà Mau", "Cao Bằng", "Cần Thơ", "Đà Nẵng", "Đắk Lắk", "Đắk Nông",
            "Đồng Nai", "Đồng Tháp", "Điện Biên", "﻿Gia Lai", "Hà Giang", "Hà Nam", "Hà Nội", "Hà Tĩnh", "Hải Dương",
            "Hải Phòng", "Hòa Bình", "Hậu Giang", "Hưng Yên", "TP. Hồ Chí Minh", "Khánh Hòa", "Kiên Giang", "Kon Tum",
            "Lai Châu", "Lào Cai", "Lạng Sơn", "Lâm Đồng", "Long An", "Nam Định", "Nghệ An", "Ninh Bình", "Ninh Thuận",
            "Phú Thọ", "Phú Yên", "Quảng Bình", "Quảng Nam", "Quảng Ngãi", "Quảng Ninh", "Quảng Trị", "Sóc Trăng",
            "Sơn La", "Tây Ninh", "Thái Bình", "Thái Nguyên", "Thanh Hóa", "Thừa Thiên - Huế", "Tiền Giang",
            "Trà Vinh", "Tuyên Quang", "Vĩnh Long", "Vĩnh Phúc", "Yên Bái", "Nơi khác",
        );
    }

    public static function gender() {
        $gender = array(0 => 'Female', 1 => 'Male', 2 => 'Other');
        if (Language::$lang_code == "vi") {
            $gender = array(0 => 'Nữ', 1 => 'Nam', 2 => 'Khác');
        }
        return $gender;
    }

    public static function getProductStatus() {
        $status = array('In stock' => 'In stock', 'Out of stock' => 'Out of stock', 'Coming soon' => 'Coming soon');
        if (Language::$lang_code == "vi") {
            $status = array('Còn hàng' => 'Còn hàng', 'Hết hàng' => 'Hết hàng', 'Sắp có hàng' => 'Sắp có hàng');
        }
        return $status;
    }

    public static function getOrderStatus() {
        $status = array(0 => 'Pending', 1 => 'In Progress', 2 => 'Paid', 3 => 'Canceled');
        if (Language::$lang_code == "vi") {
            $status = array(0 => 'Chờ xử lý', 1 => 'Đang xử lý', 2 => 'Đã thanh toán', 3 => 'Đã hủy');
        }
        return $status;
    }

    public static function getDeliveryStatus() {
        $status = array(0 => 'Pending', 1 => 'Shipping', 2 => 'Shipped');
        if (Language::$lang_code == "vi") {
            $status = array(0 => 'Chờ xử lý', 1 => 'Đang chuyển hàng', 2 => 'Đã giao hàng');
        }
        return $status;
    }

    public static function getPostStatus() {
        $status = array('published' => 'Published', 'draft' => 'Draft', 'trashed' => 'Trashed');
        if (Language::$lang_code == "vi") {
            $status = array('published' => 'Xuất bản', 'draft' => 'Nháp', 'trashed' => 'Thùng rác');
        }
        return $status;
    }

    public static function getMenuLocation() {
        $status = array('primary_menu' => 'Primary Menu', 'second_menu' => 'Second Menu',);
        if (Language::$lang_code == "vi") {
            $status = array('primary_menu' => 'Menu chính', 'second_menu' => 'Menu phụ',);
        }
        return $status;
    }

    /**
     * Remove BBCODE from text document
     * @param string $code text document
     * @return string text document
     */
    public static function removeBBCode($code) {
        $code = preg_replace("/(\[)(.*?)(\])/i", '', $code);
        $code = preg_replace("/(\[\/)(.*?)(\])/i", '', $code);
        $code = preg_replace("/http(.*?).(.*)/i", '', $code);
        $code = preg_replace("/\<a href(.*?)\>/", '', $code);
        $code = preg_replace("/:(.*?):/", '', $code);
        $code = str_replace("\n", '', $code);
        return $code;
    }

    /**
     * Get short content from full contents
     * 
     * @param integer $length 
     * @return string
     */
    public static function get_short_content($contents, $length) {
        $short = "";
        $contents = strip_tags($contents);
        if (strlen($contents) >= $length) {
            $text = explode(" ", substr($contents, 0, $length));
            for ($i = 0; $i < count($text) - 1; $i++) {
                if ($i == count($text) - 2) {
                    $short .= $text[$i];
                } else {
                    $short .= $text[$i] . " ";
                }
            }
            $short .= "...";
        } else {
            $short = $contents;
        }
        return $short;
    }

    /**
     * Video Youtube
     */
    public static function shortcode_youtube($content = NULL, $width = 300, $height = 300) {
        if ("" === $content)
            return 'No YouTube Video ID Set';
        $id = $text = $content;
        return '<object width="' . $width . '" height="' . $height . '"><param name="movie" value="http://www.youtube.com/v/' . $id . '"></param><embed src="http://www.youtube.com/v/' . $id . '" type="application/x-shockwave-flash" width="' . $width . '" height="' . $height . '"></embed></object>';
    }

    /**
     * Remove special char
     * 
     * @param string $string
     * @return string
     */
    public static function removeSpecialChar($string) {
        $specialChar = array("!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "-", "+", "=", ";", ":", "'", "\"", ",", ".", "/", "<", ">", "?",);
        foreach ($specialChar as $key => $value) {
            $pos = strpos($string, $value);
            if ($pos) {
                $string = str_replace(substr($string, $pos, 2), ucwords(substr($string, $pos + 1, 1)), $string);
            }
        }
        return $string;
    }

    /**
     * Generate random string 
     * 
     * @param integer $length default length = 32
     * @return string
     */
    public static function random_string($length = 32) {
        $key = '';
        $rand = str_split(strtolower(md5(time() * microtime())));
        $keys = array_merge(range(0, 9), range('a', 'z'));
        $keys = array_merge($keys, $rand);

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    /**
     * Replaces url entities with -
     *
     * @param string $fragment
     * @return string
     */
    public static function clean_entities($fragment) {
        $translite_simbols = array(
            '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
            '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
            '#(ì|í|ị|ỉ|ĩ)#',
            '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
            '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
            '#(ỳ|ý|ỵ|ỷ|ỹ)#',
            '#(đ)#',
            '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
            '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
            '#(Ì|Í|Ị|Ỉ|Ĩ)#',
            '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
            '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
            '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
            '#(Đ)#',
            "/[^a-zA-Z0-9\-\_]/",
        );
        $replace = array(
            'a',
            'e',
            'i',
            'o',
            'u',
            'y',
            'd',
            'A',
            'E',
            'I',
            'O',
            'U',
            'Y',
            'D',
            '-',
        );
        $fragment = preg_replace($translite_simbols, $replace, $fragment);
        $fragment = preg_replace('/(-)+/', '-', $fragment);

        return strtolower($fragment);
    }

    /**
     * Random number generator
     *
     * @param	integer	Minimum desired value
     * @param	integer	Maximum desired value
     * @param	mixed	Seed for the number generator (if not specified, a new seed will be generated)
     */
    public static function grand($min = 0, $max = 0, $seed = -1) {
        mt_srand(crc32(microtime()));

        if ($max AND $max <= mt_getrandmax()) {
            $number = mt_rand($min, $max);
        } else {
            $number = mt_rand();
        }
        // reseed so any calls outside this function don't get the second number
        mt_srand();

        return $number;
    }

    /**
     * Tests a string to see if it's a valid phone number
     *
     * @param	string	$phone Phone number
     *
     * @return	boolean
     */
    public static function is_valid_phone_number($phone) {
        return preg_match("#^[0-9[:space:]\+\-\.\(\)]+$#si", $phone);
    }

    /**
     * Tests a string to see if it's a valid email address
     *
     * @param	string	Email address
     *
     * @return	boolean
     */
    public static function is_valid_email($email) {
//        return filter_var($email, FILTER_VALIDATE_EMAIL);
//        return preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$^", $email);
        return preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s\'"<>@,;]+\.+[a-z]{2,6}))$#si', $email);
    }

############################ USER ##############################################
    /**
     * Check valid username
     * @param string $username Account using to signin
     * @return string Username is valid
     */

    public static function is_valid_username($username) {
        return preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*$^", $username);
    }

    /**
     * Generates a random password that is much stronger than what we currently use.
     *
     * @param	integer	Length of desired password
     */
    public static function fetch_random_password($length = 8) {
        $password_characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz';
        $total_password_characters = strlen($password_characters) - 1;

        $digit = self::grand(0, $length - 1);

        $newpassword = '';
        for ($i = 0; $i < $length; $i++) {
            if ($i == $digit) {
                $newpassword .= chr(self::grand(48, 57));
                continue;
            }

            $newpassword .= $password_characters{self::grand(0, $total_password_characters)};
        }
        return $newpassword;
    }

    /**
     * Hash password
     * 
     * @return string password encrypted
     */
    public static function hash_password($password, $salt) {
        return sha1(md5($password) . $salt);
    }

    /**
     * Generates a totally random string
     * @param	integer	Length of string to create
     * @return	string	Generated String
     */
    public static function fetch_user_salt($length = 30) {
        $salt = '';
        for ($i = 0; $i < $length; $i++) {
            $salt .= chr(self::grand(33, 126));
        }
        return $salt;
    }

}
