<?php

class Hash {

    /**
     *
     * @param string $algo The algorithm (md5, sha1, whirlpool, etc)
     * @param string $data The data to encode
     * @param string $salt The salt (This should be the same throughout the system probably)
     * @param bool $raw_output When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     * @return string The hashed/salted data
     */
    public static function create($algo, $data, $salt, $raw_output = false) {

        $context = hash_init($algo, HASH_HMAC, $salt);
        hash_update($context, $data);

        return hash_final($context, $raw_output);
    }

    /**
     * Generates a totally random string
     * @param	integer	Length of string to create
     * @return	string	Generated String
     */
    public static function generate_salt($length = 30) {
        $salt = '';
        for ($i = 0; $i < $length; $i++) {
            $salt .= chr(self::grand(33, 126));
        }
        return $salt;
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

}
