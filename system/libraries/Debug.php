<?php

class Debug {

    /**
     * Display errors for debug
     * @param string $flag 'On' or 'Off'
     */
    public static function displayErrors($flag = 'Off') {
        ini_set('display_errors', $flag);
        error_reporting(E_ALL | E_STRICT);
    }

    /**
     * Display with <pre> tag on browser
     * @param All format $value
     */
    public static function preTag($value) {
        if (is_string($value)) {
            echo "<pre>";
            echo($value);
            echo "</pre>";
        } else {
            echo "<pre>";
            print_r($value);
            echo "</pre>";
        }
    }

    /**
     * Dumps information about a variable
     * @param type $expression The variable you want to dump.
     * @param null $_ [optional]
     */
    public static function varDump($expression, $_ = null) {
        var_dump($expression, $_ = null);
    }
    /**
     * 
     * @param string $error
     * @param Exception|string $exception
     */
    public static function throwException($error, $exception) {
        if($error != "" or $error != null){
            echo "<b>" . $error . "</b><br />";
        }
        if(is_string($exception)){
            echo '<textarea cols="80" rows="10">' . $exception . '</textarea>';
        }elseif($exception instanceof Exception){
            echo '<textarea cols="80" rows="10">' . $exception->getMessage() . '</textarea><br />';
            if(DEBUG == TRUE){
                echo "Stack trace:<br />";
                echo '<textarea cols="80" rows="10">' . $exception->getTraceAsString() . '</textarea>';
            }
        }
        exit();
    }

}
