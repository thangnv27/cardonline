<?php

class InstallModel extends Model {

    function __construct() {
        parent::__construct();
    }

    function create() {
        $languages = "DROP TABLE IF EXISTS `" . TABLE_PREFIX . "languages`; 
                    CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . "languages` (
                        `id`                bigint(20) NOT NULL AUTO_INCREMENT,
                        `english_name`      varchar(100) NOT NULL,
                        `code`              varchar(7) NOT NULL,
                        `iso`               varchar(8) NOT NULL,
                        `locale`            varchar(8) NOT NULL,
                        `active`            tinyint(4) NOT NULL,
                        `flag`              varchar(255),
                        PRIMARY KEY (`id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . ";";
        $usergroups = "DROP TABLE IF EXISTS `" . TABLE_PREFIX . "usergroups`; 
                CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . "usergroups` (
                    `id`            bigint(20) NOT NULL AUTO_INCREMENT,
                    `role`          varchar(50) NOT NULL,
                    `name`          varchar(50) NOT NULL,
                    `capability`    longtext NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . ";";
        $users = "DROP TABLE IF EXISTS `" . TABLE_PREFIX . "users`; 
                CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . "users` (
                    `id`                bigint(20) NOT NULL AUTO_INCREMENT,
                    `user_referer`      varchar(50),
                    `username`          varchar(50) NOT NULL,
                    `email`             varchar(100) NOT NULL,
                    `password`          varchar(40) NOT NULL,
                    `salt`              varchar(30) NOT NULL,
                    `activation_key`    varchar(32),
                    `role`              varchar(50) NOT NULL,
                    `capability`        longtext NOT NULL,
                    `registered_date`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `ip_address`        varchar(15),
                    `is_deleted`        tinyint(4) DEFAULT 0,
                    PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . ";";
        $usermeta = "DROP TABLE IF EXISTS `" . TABLE_PREFIX . "usermeta`; 
                CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . "usermeta` (
                    `meta_id`       bigint(20) NOT NULL AUTO_INCREMENT,
                    `user_id`       bigint(20) NOT NULL,
                    `first_name`    varchar(50) NOT NULL,
                    `last_name`     varchar(50) NOT NULL,
                    `gender`        tinyint(4) NOT NULL,
                    `dob`           date,
                    `phone`         varchar(25),
                    `website`       varchar(255),
                    `avatar`        varchar(255),
                    `yahoo`         varchar(100),
                    `skype`         varchar(100),
                    `about`         varchar(500),
                    `coin`         int(11),
                    `updated_date`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`meta_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . ";";
        $statistics_visitor = "DROP TABLE IF EXISTS `" . TABLE_PREFIX . "statistics_visitor`; 
                CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . "statistics_visitor` (
                    `id`            bigint(20) NOT NULL AUTO_INCREMENT,
                    `last_counter`  date NOT NULL,
                    `referred`      text NOT NULL,
                    `agent`         varchar(255) NOT NULL,
                    `platform`      varchar(255),
                    `version`       varchar(255),
                    `UAString`      varchar(255),
                    `ip`            varchar(15) NOT NULL,
                    `location`      varchar(15),
                    PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . ";";
        $statistics_visit = "DROP TABLE IF EXISTS `" . TABLE_PREFIX . "statistics_visit`; 
                CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . "statistics_visit` (
                    `id`            bigint(20) NOT NULL AUTO_INCREMENT,
                    `last_visit`    datetime NOT NULL,
                    `last_counter`  date NOT NULL,
                    `visit`         bigint(20) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . ";";
        $statistics_useronline = "DROP TABLE IF EXISTS `" . TABLE_PREFIX . "statistics_useronline`; 
                CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . "statistics_useronline` (
                    `id`        bigint(20) NOT NULL AUTO_INCREMENT,
                    `ip`        varchar(15) NOT NULL,
                    `timestamp` int(10) NOT NULL,
                    `date`      datetime NOT NULL,
                    `referred`  text NOT NULL,
                    `url`       text NOT NULL,
                    `agent`     varchar(255) NOT NULL,
                    `platform`  varchar(255),
                    `version`       varchar(255),
                    PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . ";";
        $files = "DROP TABLE IF EXISTS `" . TABLE_FILE . "`; 
                CREATE TABLE IF NOT EXISTS `" . TABLE_FILE . "` (
                    `id`        int(11) unsigned NOT NULL auto_increment,
                    `parent_id` int(11) unsigned NOT NULL,
                    `name`      varchar(256) NOT NULL,
                    `content`   longblob NOT NULL,
                    `size`      int(11) unsigned NOT NULL default '0',
                    `mtime`     int(11) unsigned NOT NULL,
                    `mime`      varchar(256) NOT NULL default 'unknown',
                    `read`      enum('1', '0') NOT NULL default '1',
                    `write`     enum('1', '0') NOT NULL default '1',
                    `locked`    enum('1', '0') NOT NULL default '0',
                    `hidden`    enum('1', '0') NOT NULL default '0',
                    `width`     int(11) NOT NULL,
                    `height`    int(11) NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY  `parent_name` (`parent_id`, `name`),
                    KEY         `parent_id`   (`parent_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . ";";
        $menu = "DROP TABLE IF EXISTS `" . TABLE_PREFIX . "menu`; 
                CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . "menu` (
                    `id`            bigint(20) NOT NULL AUTO_INCREMENT,
                    `lang_code`     varchar(7) NOT NULL,
                    `name`          varchar(50) NOT NULL,
                    `title`         varchar(50) NOT NULL,
                    `url`           varchar(255) NOT NULL,
                    `parent`        bigint(20) NOT NULL DEFAULT 0,
                    `description`   varchar(500),
                    `image`         varchar(255),
                    `image_link`    varchar(255),
                    `icon`          varchar(255),
                    `displayorder`  bigint(20) NOT NULL DEFAULT 1,
                    PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . ";";
        $card = "DROP TABLE IF EXISTS `" . TABLE_PREFIX . "card`; 
                CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . "card` (
                    `id`            bigint(20) NOT NULL AUTO_INCREMENT,
                    `pin`           varchar(50) NOT NULL,
                    `serial`        varchar(50) NOT NULL,
                    `value`         varchar(100) NOT NULL,
                    `network`       varchar(100) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . ";";
        $payment_history = "DROP TABLE IF EXISTS `" . TABLE_PREFIX . "payment_history`; 
                CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . "payment_history` (
                    `id`            bigint(20) NOT NULL AUTO_INCREMENT,
                    `user_id`       bigint(50) NOT NULL,
                    `count`        bigint(20) NOT NULL,
                    `code`        varchar(255) NOT NULL,
                    `amount`        INT(11) NOT NULL,
                    `payment_date`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . ";";

        $db = $this->DB();
        try {
            /* Begin a transaction, turning off autocommit */
            $db->beginTransaction();
            /* Change the database schema and data */
//            $db->exec($languages);
            $db->exec($usergroups);
            $db->exec($users);
            $db->exec($usermeta);
//            $db->exec($statistics_visit);
//            $db->exec($statistics_visitor);
//            $db->exec($statistics_useronline);
//            $db->exec($files);
//            $db->exec($menu);
//            $db->exec($card);
            $db->exec($payment_history);

            ## Default data for table Laguages
//            $db->insert(TABLE_PREFIX . "languages", array(
//                'english_name' => 'English',
//                'code' => "en", // Eg: en
//                'iso' => "en-US", // Eg: en-US
//                'locale' => "en_US", // Eg: en_US
//                'active' => 1, // 1 or 0
//                'flag' => Registry::$siteurl . '/public/flags/US.png' // URL flag icon
//            ));

            ## Default data for table UserGroups
            $adminCapability = array(
                'options' => array('view' => 1, 'edit' => 1),
                'languages' => array('view' => 1, 'edit' => 1),
                'userGroups' => array('view' => 1, 'edit' => 1, 'permission' => 1),
                'users' => array('view' => 1, 'edit' => 1, 'create' => 1, 'delete' => 1, 'permission' => 1),
                'code' => array('view' => 1, 'edit' => 1, 'create' => 1, 'delete' => 1),
                'files' => array('view' => 1, 'edit' => 1, 'upload' => 1, 'delete' => 1),
                'menu' => array('manage' => 1),
                'settings' => array('allow' => 1,),
            );
            $editorCapability = array(
                'options' => array('view' => 0, 'edit' => 0),
                'languages' => array('view' => 0, 'edit' => 0),
                'userGroups' => array('view' => 0, 'edit' => 0, 'permission' => 0),
                'users' => array('view' => 0, 'edit' => 0, 'create' => 0, 'delete' => 0, 'permission' => 0),
                'code' => array('view' => 0, 'edit' => 0, 'create' => 0, 'delete' => 0),
                'files' => array('view' => 1, 'edit' => 1, 'upload' => 1, 'delete' => 1),
                'menu' => array('manage' => 0),
                'settings' => array('allow' => 0,),
            );
            $subscriberCapability = array(
                'options' => array('view' => 0, 'edit' => 0),
                'languages' => array('view' => 0, 'edit' => 0),
                'userGroups' => array('view' => 0, 'edit' => 0, 'permission' => 0),
                'users' => array('view' => 0, 'edit' => 0, 'create' => 0, 'delete' => 0, 'permission' => 0),
                'code' => array('view' => 0, 'edit' => 0, 'create' => 0, 'delete' => 0),
                'files' => array('view' => 0, 'edit' => 0, 'upload' => 0, 'delete' => 0),
                'menu' => array('manage' => 0),
                'settings' => array('allow' => 0,),
            );
            $db->insert(TABLE_PREFIX . "usergroups", array(
                'role' => 'administrator',
                'name' => 'Administrator',
                'capability' => serialize($adminCapability),
            ));
            $db->insert(TABLE_PREFIX . "usergroups", array(
                'role' => 'editor',
                'name' => 'Editor',
                'capability' => serialize($editorCapability),
            ));
            $db->insert(TABLE_PREFIX . "usergroups", array(
                'role' => 'subscriber',
                'name' => 'Subscriber',
                'capability' => serialize($subscriberCapability),
            ));

            ## Default data for table Users
            $salt = Utils::fetch_user_salt(30);
            $password = Utils::hash_password("1234567", $salt);
            $db->insert(TABLE_PREFIX . "users", array(
                'username' => "admin",
                'email' => 'ngothangit@gmail.com',
                'password' => $password,
                'salt' => $salt,
                'role' => 'administrator',
                'capability' => serialize($adminCapability),
            ));
            // Example User
//            for ($index = 0; $index < 20; $index++) {
//                $salt1 = Utils::fetch_user_salt(30);
//                $pwd = Utils::hash_password("1234567", $salt1);
//                $db->insert(TABLE_PREFIX . "users", array(
//                    'username' => "demo{$index}",
//                    'email' => "demo{$index}@gmail.com",
//                    'password' => $pwd,
//                    'salt' => $salt1,
//                    'role' => 'subscriber',
//                    'capability' => serialize($subscriberCapability),
//                ));
//            }

            /* $db->insert(TABLE_FILE, array(
              'parent_id' => 0,
              'name' => 'DATABASE',
              'content' => '',
              'size' => 0,
              'mtime' => 0,
              'mime' => 'directory',
              'read' => 1,
              'write' => 1,
              'locked' => 0,
              'hidden' => 0,
              'width' => 0,
              'height' => 0
              )); */
            /* Database connection is now back in autocommit mode */
            $db->commit();
        } catch (Exception $exc) {
            /* Recognize mistake and roll back changes */
            $db->rollBack();
            Debug::throwException("Database error!", $exc);
        }
    }

}
