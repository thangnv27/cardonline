<?php

class Session {

    function __construct() {
        @session_start();
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key) {
        if (isset($_SESSION[$key]))
            return $_SESSION[$key];
    }

    /**
     * Get all of session
     * 
     * @return array
     */
    public function all() {
        if (isset($_SESSION))
            return $_SESSION;
        else
            return array();
    }

    /**
     * Check exists session key
     * 
     * @param string $key Session key
     * @return boolean
     */
    public function has($key) {
        if (isset($_SESSION[$key]))
            return true;

        return false;
    }

    /**
     * Remove an sessions
     * @param string|array $key Session keys
     */
    public function remove($key) {
        if (is_string($key) and isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        } elseif (is_array($key)) {
            foreach ($key as $value) {
                if (isset($_SESSION[$value]))
                    unset($_SESSION[$value]);
            }
        }
    }

    /**
     * Clear all of session
     */
    public function clear() {
        //unset($_SESSION);
        session_destroy();
    }

###################### FLASH MESSAGE ###########################################
# Flash-Messages provide a way to preserve messages across different  
# HTTP-Requests. This object manages those messages. 
# Note: make sure you call session_start() in order to make this code work

    /**
     * Set value for flash message
     * 
     * @param string $name
     * @param string $value
     */
    function setFlash($name, $value) {
        $msg = serialize($value);
        unset($_SESSION['flash_message']); // unset all old flash session
        $_SESSION['flash_message'][$name] = $msg;
    }

    /**
     * Get flash message by name
     * 
     * @param string $name Session name
     * @param type $default
     * @return string
     */
    function getFlash($name) {
        $msg = unserialize($_SESSION['flash_message'][$name]);
        if ($msg == "")
            return null;
        unset($_SESSION['flash_message'][$name]); // remove the session after being retrieve  
        return $msg;
    }

    /**
     * Check exists session name
     * 
     * @param string $name Session name
     * @return boolean
     */
    function hasFlash($name) {
        if (isset($_SESSION['flash_message'])) {
            if (array_key_exists($name, $_SESSION['flash_message']))
                return true;
        }
        return false;
    }

}
