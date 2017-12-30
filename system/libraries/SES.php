<?php

/**
 * Simple Email Service
 * Send mail with Amazon Email Service
 */
class SES extends SimpleEmailService {

    function __construct() {
        $config = Registry::$settings['config']['ses'];
        $this->setAuth($config['access_key'], $config['secret_key']);
        $this->__host = $config['host'];
    }

    function message() {
        $m = new SimpleEmailServiceMessage();
        $m->setSubjectCharset('UTF-8');
        $m->setMessageCharset('UTF-8');
        return $m;
    }

}
