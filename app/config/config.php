<?php

$config = array();
################ CONFIGURATION #################################################
/* Domain Name */
$config['domain'] = 'ppo.vn';
/* URL of your website. Note: do not add a trailing slash. ('/') */
$config['site_url'] = 'http://demo.ppo.vn/cardonline';
/* Default controller for Frontend */
$config['front_controller'] = 'Welcome';
$config['front_controller_mobile'] = 'Welcome';
/* Default controller for Backend */
$config['admin_controller'] = 'WelcomeAdmin';
/* Default theme */
$config['theme_default'] = 'default';
/* Mobile theme */
$config['theme_mobile'] = 'default';
/* Email configuration with SwiftMailer */
$config['swiftmailer']['host'] = "smtp.gmail.com"; // Default: localhost
$config['swiftmailer']['port'] = 465; // Default: 25
$config['swiftmailer']['auth'] = TRUE;
$config['swiftmailer']['username'] = "ppo.global@gmail.com";
$config['swiftmailer']['password'] = "apxwlhqtqjfoipet";
$config['swiftmailer']['security'] = 'ssl'; // 'ssl', 'tls' and NULL available
/* Amazon Email Service */
$config['ses']['host'] = "email.us-east-1.amazonaws.com";
$config['ses']['access_key'] = "";
$config['ses']['secret_key'] = "";
/* Definition */
define('DEFAULT_LANG', 'en');
define('DEBUG', TRUE);
define('DEBUG_THEME', FALSE);
define('CACHE', FALSE);
define('MINIFY_HTML', FALSE);

################ DATABASE ######################################################
/* Database type: mysql, mysqli, etc. */
define('DB_TYPE', 'mysql');

/* Database name */
define('DB_NAME', 'ppovn_cardonline');

/* Database username */
define('DB_USER', 'ppovn_demo');

/* Database password */
define('DB_PASSWORD', 'ppodemo123##');

/* Hostname Server: 'localhost' or IP address */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', 'utf8_general_ci');

/** Prefix name of tables */
define('TABLE_PREFIX', '');

/** Name of table files */
define('TABLE_FILE', TABLE_PREFIX . 'files');

################ FTP ###########################################################
define('FTP_SERVER', 'localhost');
define('FTP_PORT', 21);
define('FTP_USERNAME', '');
define('FTP_PASSWORD', '');

################ Set default timezone ##########################################
date_default_timezone_set('Asia/Ho_Chi_Minh');


################ Bao Kim merchan ###############################################

define('CORE_API_HTTP_USR', 'merchant_16199');
define('CORE_API_HTTP_PWD', '16199T0rJ6lLmNZFw2Q740PvIpKqWts99ic');
